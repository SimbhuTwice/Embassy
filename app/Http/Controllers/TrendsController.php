<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\User;
use App\RoleMaster;
use App\CompanyMaster;
use App\CompanyBranch;
use App\BranchSettings;
use App\DistechDevice;
use App\ReportRange;
// use App\RoleUser;
use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Config;
use Illuminate\Support\Facades\Log;

class TrendsController extends Controller
{
    public function viewtrends()
    {
        if (is_null(session('user_id'))) return redirect()->route('logout');

        ini_set('max_execution_time', Config::get('constants.ExecutionTimeOut'));
        $recordCount = Config::get('constants.GetDistechRecords');        
        $compbrDevLocList = session('compbrDevLocList');
        
        $currentdate = Carbon::now('Asia/colombo');
        $currdt = date('Y-m-d', strtotime($currentdate));
        $timeList = DB::table(session('table_name'))
            ->distinct()
            ->where('company_id', session('company_id'))
            ->where('branch_id', session('branch_id'))
            ->where('status_date', $currdt)
            ->orderByDesc('status_time')
            ->take($recordCount)
            ->pluck('status_time');
        $timeList = $timeList->reverse();
        $timeLastItem = $timeList->last();
        $timeList = json_decode(json_encode($timeList), true);

        $disdevList = DB::table(session('table_name'))
            ->where('company_id', session('company_id'))
            ->where('branch_id', session('branch_id'))
            ->where('status_date', $currdt)
            ->whereIn('status_time', $timeList)
            ->orderBy('object_type')
            ->orderBy('object_name')
            ->orderBy('status_time')
            ->get();
        $asnList = $disdevList->unique('asn_value');

        // $asnList = DB::table(session('table_name'))
        //     ->distinct()
        //     ->where('company_id', session('company_id'))
        //     ->where('branch_id', session('branch_id'))
        //     ->where('status_date', $currdt)
        //     ->whereIn('status_time', $timeList)
        //     ->pluck('asn_value');

        $compbrSettings = BranchSettings::where('branch_id', session('branch_id'))
            ->where('field', Config::get('constants.BS_Refresh_Time'))->first();
        $branchSettingsRefreshTime = intval($compbrSettings->value) * 60000;
        $chartCount = 0;
        
        $headerObjectNameList = array();
        $gaugeChartObjectNameList = array();
        $valueChartObjectNameList = array();
        $trendChartObjectNameList = array();
        $binaryDeviceLocationList = array();
        $timeArr = array();

        foreach ($timeList as $tm)
            $timeArr[] = date(Config::get('constants.XAxisTimeFormat'), strtotime($tm));

        foreach ($asnList as $asnkey => $asnvalue)
        {
            $asn = $asnvalue->asn_value;
            $disdevtempList = $disdevList->where('asn_value', $asn)->pluck(['present_value']);
            $objectName = $disdevList->where('asn_value', $asn)->pluck(['object_name']);
            $objectType = $disdevList->where('asn_value', $asn)->pluck(['object_type']);
            $devlocname = $disdevList->where('asn_value', $asn)->pluck(['device_location']);

            foreach ($devlocname as $obj)
                $devlocationname = $obj;
            foreach ($objectName as $obj)
                $objname = $obj;
            foreach ($objectType as $obj)
                $objtypename = $obj;

            $actualname = str_replace("_", " ", $objname);
            $actualname = str_replace(" sensor", "", strtolower($actualname));
            $actualname = ucwords($actualname);

            // if (strpos(strtolower($objname), "temperature"))
            //     $objtemp = $actualname;
            // else if (strpos(strtolower($objname), "temp"))
            // {
            //     $objtemp = str_replace("temp", "temperature", strtolower($objname));
            //     $objtemp = str_replace("_", " ", $objtemp);
            // }
            // else
            //     $objtemp = $actualname;
            // $objtemp = ucwords($objtemp);
            $objtemp = ucwords($actualname);
            
            
            $explodeName = strpbrk($objtemp, "1234567890");
            if ($explodeName != '')
            {
                $exploded = explode($explodeName, $objtemp);
                $clubbedName = trim($exploded[0], " ");
            }
            else
                $clubbedName = $objtemp;
            
            $clubbedName = str_replace(" sensor", "", strtolower($clubbedName));
            $clubbedName = ucwords($clubbedName);
            $clubbedIndex = array_search($clubbedName, array_column($headerObjectNameList, "deviceName"));
            $binClubbedIndex = array_search($clubbedName, array_column($binaryDeviceLocationList, "deviceName"));

            if ($binClubbedIndex !== false)
                $binaryDeviceLocationList[$clubbedIndex]["deviceLocation"] = intval($binaryDeviceLocationList[$binClubbedIndex]["deviceLocation"]) . ',' . $devlocationname;
            else
            {
                if ($objtypename == Config::get('constants.api_binary_input') || $objtypename == Config::get('constants.api_binary_value') || $objtypename == Config::get('constants.api_binary_output'))
                    $binaryDeviceLocationList[] = ["name" => $objname, "actualname" => $actualname, "deviceName" => $clubbedName, "deviceLocation" => $devlocationname];
            }

            if ($clubbedIndex !== false)
                $headerObjectNameList[$clubbedIndex]["count"] = intval($headerObjectNameList[$clubbedIndex]["count"]) + 1;
            else
            {
                // if ($objtypename == Config::get('constants.api_analog_input') || $objtypename == Config::get('constants.api_analog_value')) {
                    $brdevloc1 = $compbrDevLocList->where('device_name', $objname)->pluck(['show_in_header']);
                    $brdevlocname1 = 0;
                    foreach ($brdevloc1 as $obj1)
                        $brdevlocname1 = $obj1;
                    if ($brdevlocname1 == 1) {
                        $headerObjectNameList[] = ["name" => $objname, "actualname" => $actualname, "deviceName" => $clubbedName, "count" => 1]; 
                    }
                // }
            }

            $brdevloc = $compbrDevLocList->where('device_name', $objname)->where('chart_type', Config::get('constants.chart_gauge'))->pluck(['device_location']);
            $brdevlocname = '';
            foreach ($brdevloc as $obj)
                $brdevlocname = $obj;

            $brdevloc = $compbrDevLocList->where('device_name', $objname)->pluck(['img_src']);
            $brimgsrc = '';
            foreach ($brdevloc as $obj)
                $brimgsrc = $obj;

            if ($brdevlocname != '')
            {
                $chartCount += 1;
                if (strpos(strtolower($objname), "oom")) {}
                else
                    $gaugeChartObjectNameList[] = ['actualname' => $actualname, 'name' => $objname, 'deviceLocation' => $brdevlocname, 'presentvalue' => 0, 'imgsrc' => $brimgsrc];
            }

            $brdevloc = $compbrDevLocList->where('device_name', $objname)->where('chart_type', Config::get('constants.chart_value'))->pluck(['device_location']);
            $brdevlocname = '';
            foreach ($brdevloc as $obj)
                $brdevlocname = $obj;
            if ($brdevlocname != '')
                $valueChartObjectNameList[] = ['actualname' => $actualname, 'name' => $objname, 'deviceLocation' => $brdevlocname, 'presentvalue' => 0, 'imgsrc' => $brimgsrc];

            $brdevloc = $compbrDevLocList->where('device_name', $objname)->where('chart_type', Config::get('constants.chart_trend'))->pluck(['device_location']);
            $brdevlocname = '';
            foreach ($brdevloc as $obj)
                $brdevlocname = $obj;
            if ($brdevlocname != '')
            {
                $chartCount += 1;
                $valueChartObjectNameList[] = ['actualname' => $actualname, 'name' => $objname, 'deviceLocation' => $brdevlocname, 'presentvalue' => 0, 'imgsrc' => $brimgsrc];
                $trendChartObjectNameList[] = ['actualname' => $actualname, 'name' => $objname, 'deviceLocation' => $brdevlocname, 'presentvalue' => 0, 'imgsrc' => $brimgsrc];
            }
        }

        array_multisort(array_column($headerObjectNameList, 'actualname'),  SORT_ASC,
            $headerObjectNameList);

        $reportRangeList = ReportRange::where('branch_id', session('branch_id'))
            ->select('range_name', 'sort_order')->distinct()->orderBy('sort_order')->get();
        $defaultRangeType = Config::get('constants.Reports_Current');
        $reportIntervalList = ReportRange::where('branch_id', session('branch_id'))
            ->where('range_name', $defaultRangeType)
            ->select('interval_name', 'interval_value')->get();
        $trendDeviceList = $compbrDevLocList->where('show_in_trends', 1)->pluck(['device_name']);

        $newTrendDeviceList = array();
        $chartnoList = $compbrDevLocList->where('chart_nos')->unique('chart_nos');
        $chartnoCount = 0;
        foreach ($chartnoList as $chartnoKey => $chartnovalue) {
            $chartno = $chartnovalue->chart_nos;
            $newTrendDeviceList[] = ['chartno' => $chartno, 
                'trends' => $compbrDevLocList->where('chart_nos', $chartno)->where('show_in_trends', 1)];
            $chartnoCount += 1;
        }

        return view('newtrends', [
            'refreshTime' => $branchSettingsRefreshTime,
            'chartCount' => $chartCount,
            'headerObjectNameList' => $headerObjectNameList,
            'binaryDeviceLocationList' => $binaryDeviceLocationList,
            'gaugeChartObjectNameList' => $gaugeChartObjectNameList,
            'valueChartObjectNameList' => $valueChartObjectNameList,
            'trendChartObjectNameList' => $trendChartObjectNameList,
            'reportRangeList' => $reportRangeList,
            'trendDeviceList' => $trendDeviceList,
            'defaultRangeType' => $defaultRangeType,
            'reportIntervalList' => $reportIntervalList,
            'newTrendDeviceList' => $newTrendDeviceList,
            'chartnoCount' => $chartnoCount,
        ]);
    }

    public function getReportRangeInterval(string $rangetype)
    {
        $reportIntervalList = ReportRange::where('branch_id', session('branch_id'))
            ->where('range_name', $rangetype)
            ->select('interval_name', 'interval_value')
            ->orderBy('id')->get();
        return json_encode($reportIntervalList);
    }

    // public function drawgraph(string $rangetype, int $intervalvalue)
    public function drawgraph(string $rangetype)
    {
        $timeList = array();
        $compbrDevLocList = session('compbrDevLocList');
        $chartnoList = $compbrDevLocList->where('chart_nos')->unique('chart_nos');
        $chartColorList = array();
        foreach ($chartnoList as $chartnoKey => $chartnovalue) {
            $chartColorList[] = ['chartno' => $chartnovalue->chart_nos, 'chartcolor' => $chartnovalue->chart_nos_color];
        }
        return json_encode(['timeArr' => $timeList, 'chartColorList' => $chartColorList]);
    }

    // public function drawgraphdata(string $deviceName, string $rangetype, int $intervalvalue)
    public function drawgraphdata(string $deviceName, string $rangetype)
    {
        ini_set('max_execution_time', Config::get('constants.ExecutionTimeOut'));
        $currentdate = Carbon::now('Asia/colombo');
        $currdt = date('Y-m-d', strtotime($currentdate));
        $devname = str_replace("btn_", "", $deviceName);

        if ($rangetype == Config::get('constants.Reports_Current')) {
            $dataList = DB::table('trends_device_status')
                ->select('status_hour', 'object_type', 'object_name', 
                    DB::raw("TRUNC(AVG(present_value::numeric), 2) AS average"), 
                    DB::raw("TRUNC(SUM(present_value::numeric), 2) AS total"))                
                ->where('status_date', $currentdate)
                ->where('branch_id', session('branch_id'))
                ->where('object_name', $devname)
                ->groupBy('status_hour', 'object_type', 'object_name')
                ->orderByRaw("status_hour, object_name")->get();

            // $dataList = DB::select('select distinct tds.status_hour, tds.object_type, tds.object_name, ' .
            //     ' TRUNC(AVG(tds.present_value::numeric), 2) AS average, ' .
            //     ' TRUNC(SUM(tds.present_value::numeric), 2) AS total ' .
            //     ' FROM trends_device_status tds ' .
            //     // ' INNER JOIN branch_device_location bdl ON bdl.branch_id = device_status.branch_id ' .
            //     // ' AND bdl.device_name::text = device_status.object_name::text ' .
            //     // ' WHERE bdl.show_in_trends = 1 AND device_status.status_date = \'' . $currdt . '\' ' .
            //     ' WHERE tds.status_date = \'' . $currdt . '\' ' .
            //     ' AND tds.branch_id = ' . session('branch_id') .
            //     ' AND tds.object_name = \'' . $devname . '\' ' .
            //     ' GROUP BY tds.status_hour, tds.object_type, tds.object_name ' .
            //     ' ORDER BY tds.status_hour, tds.object_name');
            return json_encode(['dataArr' => $dataList]);
        }
        else if ($rangetype == Config::get('constants.Reports_Weekly')) {
            $startWeek = Carbon::now('Asia/colombo')->startOfWeek()->addDays(-1);
            $tempDate = Carbon::now('Asia/colombo')->startOfWeek()->addDays(-1);
            $diffDays = $tempDate->startOfDay()->diffInDays();

            $timeArr = array();
            $timeArr[] = [$startWeek->format('l')];
            for ($i = 0; $i < $diffDays; $i += 1) {
                $tempDt = $tempDate->addDays(1);
                $timeArr[] = [$tempDt->format('l')];
            }
            $dataList = DB::table('trends_device_status')
                ->select('status_datetime', 'object_type', 'object_name', 
                    DB::raw("TRUNC(AVG(present_value::numeric), 2) AS average"), 
                    DB::raw("TRUNC(SUM(present_value::numeric), 2) AS total"))                
                ->whereBetween('status_datetime', [$startWeek, $currentdate])
                ->where('branch_id', session('branch_id'))
                ->where('object_name', $devname)
                ->groupBy('status_datetime', 'object_type', 'object_name')
                ->orderByRaw("status_datetime, object_name")->get();
            return json_encode(['timeArr' => $timeArr, 'dataArr' => $dataList]);
        }
        else if ($rangetype == Config::get('constants.Reports_Monthly')) {
            $startMonth = Carbon::now('Asia/colombo')->startOfMonth();
            $endWeek = $startMonth->copy()->endOfWeek()->addDays(-1);
            
            $dataList = DB::table('trends_device_status')
                ->select('status_datetime', 'object_type', 'object_name', 
                    DB::raw("TRUNC(AVG(present_value::numeric), 2) AS average"), 
                    DB::raw("TRUNC(SUM(present_value::numeric), 2) AS total"))                
                ->whereBetween('status_datetime', [$startMonth, $currentdate])
                ->where('branch_id', session('branch_id'))
                ->where('object_name', $devname)
                ->groupBy('status_datetime', 'object_type', 'object_name')
                ->orderByRaw("status_datetime, object_name")->get();

            $endloop = false;
            $timeArr = array();
            $weeklyList = array();
            for ($i = 1; !$endloop; $i += 1) {
                $timeArr[] = ['Week-' . $i];
                if ($endWeek >= $currentdate)
                    $endWeek = $currentdate;

               $datasublist = $dataList->where('status_datetime', '>=', $startMonth)
                    ->where('status_datetime', '<=', $endWeek)->pluck('average');
                $weeklyList[] = ['status_date' => 'Week-' . $i, 'average' => number_format($datasublist->avg(), 2)];

                if ($endWeek == $currentdate)
                    $endloop = true;
                else {
                    $startMonth = $endWeek->copy()->addDays(1)->startOfDay();
                    $endWeek = $endWeek->addDays(7);
                }
            }
            return json_encode(['timeArr' => $timeArr, 'dataArr' => $weeklyList]);
        }
        else if ($rangetype == Config::get('constants.Reports_Yearly')) {
            $curryear = date('Y', strtotime($currentdate));
            $startYear = Carbon::createFromDate(intval($curryear), 1, 1);

            $dataList = DB::table('trends_device_status')
                ->select('status_month', 'object_type', 'object_name', 
                    DB::raw("TRUNC(AVG(present_value::numeric), 2) AS average"), 
                    DB::raw("TRUNC(SUM(present_value::numeric), 2) AS total"))                
                ->whereBetween('status_date', [$startYear, $currentdate])
                ->where('branch_id', session('branch_id'))
                ->where('object_name', $devname)
                ->groupBy('status_month', 'object_type', 'object_name')
                ->orderByRaw("status_month, object_name")->get();

            $timeArr = array();
            foreach ($dataList as $data) {
                $tm = $data->status_month;
                $tempDt = Carbon::createFromDate(intval($curryear), $tm, 1);
                $timeArr[] = [$tempDt->format('F')];
            }
            return json_encode(['timeArr' => $timeArr, 'dataArr' => $dataList]);
        }
    }

    // public function drawgraphdata(string $deviceName, string $rangetype, int $intervalvalue)
    public function drawgraphdatanew(string $devicenames, string $rangetype)
    {
        ini_set('max_execution_time', Config::get('constants.ExecutionTimeOut'));
        $currentdate = Carbon::now('Asia/colombo');
        $currdt = date('Y-m-d', strtotime($currentdate));
        $deviceNameList = explode(',', $devicenames);

        if ($rangetype == Config::get('constants.Reports_Current')) {
            $dataList = DB::table('trends_device_status')
                ->select('status_hour', 'object_type', 'object_name', 'device_uom',
                    DB::raw("TRUNC(AVG(present_value::numeric), 2) AS average"), 
                    DB::raw("TRUNC(SUM(present_value::numeric), 2) AS total"))                
                ->where('status_date', $currentdate)
                ->where('branch_id', session('branch_id'))
                ->whereIn('object_name', $deviceNameList)
                ->groupBy('status_hour', 'object_type', 'object_name', 'device_uom')
                ->orderByRaw("status_hour, object_name")->get();
            return json_encode(['dataArr' => $dataList]);
        }
        else if ($rangetype == Config::get('constants.Reports_Weekly')) {
            $startWeek = Carbon::now('Asia/colombo')->startOfWeek()->addDays(-1);
            $tempDate = Carbon::now('Asia/colombo')->startOfWeek()->addDays(-1);
            $diffDays = $tempDate->startOfDay()->diffInDays();

            $timeArr = array();
            $timeArr[] = [$startWeek->format('l')];
            for ($i = 0; $i < $diffDays; $i += 1) {
                $tempDt = $tempDate->addDays(1);
                $timeArr[] = [$tempDt->format('l')];
            }
            $dataList = DB::table('trends_device_status')
                ->select('status_datetime', 'object_type', 'object_name', 'device_uom',
                    DB::raw("TRUNC(AVG(present_value::numeric), 2) AS average"), 
                    DB::raw("TRUNC(SUM(present_value::numeric), 2) AS total"))                
                ->whereBetween('status_datetime', [$startWeek, $currentdate])
                ->where('branch_id', session('branch_id'))
                ->whereIn('object_name', $deviceNameList)
                ->groupBy('status_datetime', 'object_type', 'object_name', 'device_uom')
                ->orderByRaw("status_datetime, object_name")->get();
            return json_encode(['timeArr' => $timeArr, 'dataArr' => $dataList]);
        }
        else if ($rangetype == Config::get('constants.Reports_Monthly')) {
            $startMonth = Carbon::now('Asia/colombo')->startOfMonth();
            $endWeek = $startMonth->copy()->endOfWeek()->addDays(-1);
            
            $dataList = DB::table('trends_device_status')
                ->select('status_datetime', 'object_type', 'object_name', 'device_uom',
                    DB::raw("TRUNC(AVG(present_value::numeric), 2) AS average"), 
                    DB::raw("TRUNC(SUM(present_value::numeric), 2) AS total"))                
                ->whereBetween('status_datetime', [$startMonth, $currentdate])
                ->where('branch_id', session('branch_id'))
                ->whereIn('object_name', $deviceNameList)
                ->groupBy('status_datetime', 'object_type', 'object_name', 'device_uom')
                ->orderByRaw("status_datetime, object_name")->get();

            $endloop = false;
            $timeArr = array();
            $weeklyList = array();
            for ($i = 1; !$endloop; $i += 1) {
                $timeArr[] = ['Week-' . $i];

                for ($j = 0; $j < count($deviceNameList); $j += 1) {
                    if ($endWeek >= $currentdate)
                        $endWeek = $currentdate;

                    $datasublist = $dataList->where('object_name', $deviceNameList[$j])
                        ->where('status_datetime', '>=', $startMonth)
                        ->where('status_datetime', '<=', $endWeek)->pluck('average');
                    $weeklyList[] = ['status_date' => 'Week-' . $i, 'average' => number_format($datasublist->avg(), 2), 'object_name' => $deviceNameList[$j]];

                    if ($endWeek == $currentdate)
                        $endloop = true;
                    else {
                        $startMonth = $endWeek->copy()->addDays(1)->startOfDay();
                        $endWeek = $endWeek->addDays(7);
                    }
                }
            }
            return json_encode(['timeArr' => $timeArr, 'dataArr' => $weeklyList]);
        }
        else if ($rangetype == Config::get('constants.Reports_Yearly')) {
            $curryear = date('Y', strtotime($currentdate));
            $startYear = Carbon::createFromDate(intval($curryear), 1, 1);

            $dataList = DB::table('trends_device_status')
                ->select('status_month', 'object_type', 'object_name', 'device_uom', 
                    DB::raw("TRUNC(AVG(present_value::numeric), 2) AS average"), 
                    DB::raw("TRUNC(SUM(present_value::numeric), 2) AS total"))                
                ->whereBetween('status_date', [$startYear, $currentdate])
                ->where('branch_id', session('branch_id'))
                ->whereIn('object_name', $deviceNameList)
                ->groupBy('status_month', 'object_type', 'object_name', 'device_uom')
                ->orderByRaw("status_month, object_name")->get();

            $timeArr = array();
            $monthnoList = $dataList->unique('status_month');
            foreach ($monthnoList as $monthnoKey => $monthnovalue) {
                $tm = $monthnovalue->status_month;
                $tempDt = Carbon::createFromDate(intval($curryear), $tm, 1);
                $timeArr[] = [$tempDt->format('F')];
            }

            // foreach ($dataList as $data) {
            //     $tm = $data->status_month;
            //     $tempDt = Carbon::createFromDate(intval($curryear), $tm, 1);
            //     $timeArr[] = [$tempDt->format('F')];
            // }
            return json_encode(['timeArr' => $timeArr, 'dataArr' => $dataList]);
        }
    }

    // public function exportCSV(string $devicenames, string $rangetype, int $intervalvalue)
    public function exportCSV(string $devicenames, string $rangetype)
    {
        $currentdate = Carbon::now('Asia/colombo');
        $currdt = date('Y-m-d', strtotime($currentdate));
        $fileName = 'trends_' . date('Ymd', strtotime($currentdate)) . '.csv';
        $deviceNameList = explode(',', $devicenames);
        
        $headers = array(
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        );
        $compbrDevLocList = session('compbrDevLocList');

        if ($rangetype == Config::get('constants.Reports_Monthly')) {
            $startMonth = Carbon::now('Asia/colombo')->startOfMonth();
            $endWeek = $startMonth->copy()->endOfWeek()->addDays(-1);
            $reportheader = 'Monthly Report (' . date('d-m-Y', strtotime($startMonth)) . ' to ' . date('d-m-Y', strtotime($currentdate)) . ')';
            
            $dataList = DB::table('trends_device_status')
                ->select('status_datetime', 'object_type', 'object_name', 'device_uom',
                    DB::raw("TRUNC(AVG(present_value::numeric), 2) AS average"), 
                    DB::raw("TRUNC(SUM(present_value::numeric), 2) AS total"))                
                ->whereBetween('status_datetime', [$startMonth, $currentdate])
                ->where('branch_id', session('branch_id'))
                ->whereIn('object_name', $deviceNameList)
                ->groupBy('status_datetime', 'object_type', 'object_name', 'device_uom')
                ->orderByRaw("status_datetime, object_name")->get();

            $callback = function() use($dataList, $reportheader, $deviceNameList, $compbrDevLocList, $currentdate) {
                $file = fopen('php://output', 'w');
                fputcsv($file, [$reportheader]);
                
                for ($i = 0; $i < count($deviceNameList); $i += 1) {
                    $startMonth = Carbon::now('Asia/colombo')->startOfMonth();
                    $endWeek = $startMonth->copy()->endOfWeek()->addDays(-1);
                    $brdevloc = $compbrDevLocList->where('device_name', $deviceNameList[$i])->pluck(['trends_calculation']);
                    $trendscalc = '';
                    foreach ($brdevloc as $obj1)
                        $trendscalc = $obj1;

                    $datasublist = $dataList->where('object_name', $deviceNameList[$i])->toArray();
                    $deviceNamewithUOM = '';
                    foreach ($datasublist as $datakey => $data) {
                        $deviceNamewithUOM = str_replace("_", " ", $deviceNameList[$i]) . ' (' . trim($data->device_uom) . ')';
                        break;
                    }
                    
                    $isTotalColumn = false;
                    if ($trendscalc == Config::get('constants.TrendsCalculation'))
                        $columns = array('WEEK', 'FROM', 'TO', 'AVERAGE');
                    else {
                        $columns = array('WEEK', 'FROM', 'TO', 'AVERAGE', 'TOTAL');
                        $isTotalColumn = true;
                    }
        
                    fputcsv($file, [$deviceNamewithUOM]);
                    fputcsv($file, $columns);

                    $endloop = false;
                    for ($j = 1; !$endloop; $j += 1) {
                        if ($endWeek >= $currentdate)
                            $endWeek = $currentdate;

                        $datasubavglist = $dataList->where('status_datetime', '>=', $startMonth)
                            ->where('status_datetime', '<=', $endWeek)->pluck('average');
                        $datasubtotallist = $dataList->where('status_datetime', '>=', $startMonth)
                            ->where('status_datetime', '<=', $endWeek)->pluck('total');

                        $row['WEEK']  = 'Week-' . $j;
                        $row['FROM']  = date('d-m-Y', strtotime($startMonth));
                        $row['TO']  = date('d-m-Y', strtotime($endWeek));
                        $row['AVERAGE']  = number_format($datasubavglist->avg(), 2);
                        if ($isTotalColumn) {
                            $row['TOTAL'] = number_format($datasubtotallist->avg(), 2);
                            fputcsv($file, array($row['WEEK'], $row['FROM'], $row['TO'], $row['AVERAGE'], $row['TOTAL']));
                        }
                        else 
                            fputcsv($file, array($row['WEEK'], $row['FROM'], $row['TO'], $row['AVERAGE']));

                        if ($endWeek == $currentdate)
                            $endloop = true;
                        else {
                            $startMonth = $endWeek->copy()->addDays(1)->startOfDay();
                            $endWeek = $endWeek->addDays(7);
                        }
                    }
                    fputcsv($file, [" "]);
                    fputcsv($file, [" "]);
                    fputcsv($file, [" "]);
                }
                fclose($file);
            };
            return response()->stream($callback, 200, $headers);
        }
        else {
            $startdate = $currentdate->copy();
            $dbcolumnname = 'status_hour AS status_period';
            $reportheader = 'Today\'s Report - ' . date('d-m-Y', strtotime($currentdate));
            if ($rangetype == Config::get('constants.Reports_Weekly')) {
                $startdate = Carbon::now('Asia/colombo')->startOfWeek()->addDays(-1);
                $dbcolumnname = 'TRIM(status_dayname) AS status_period';
                $reportheader = 'Weekly Report (' . date('d-m-Y', strtotime($startdate)) . ' to ' . date('d-m-Y', strtotime($currentdate)) . ')';
            }
            // else if ($rangetype == Config::get('constants.Reports_Monthly')) {
            //     $startdate = Carbon::now('Asia/colombo')->startOfMonth();
            //     $dbcolumnname = 'status_hour AS status_period';
            //     $reportheader = 'Monthly Report (' . date('d-m-Y', strtotime($startdate)) . ' to ' . date('d-m-Y', strtotime($currentdate)) . ')';
            // }
            else if ($rangetype == Config::get('constants.Reports_Yearly')) {
                $curryear = date('Y', strtotime($currentdate));
                $startdate = Carbon::createFromDate(intval($curryear), 1, 1);
                $dbcolumnname = 'TRIM(status_monthname) AS status_period';
                $reportheader = 'Yearly Report (' . date('d-m-Y', strtotime($startdate)) . ' to ' . date('d-m-Y', strtotime($currentdate)) . ')';
            }

            if ($rangetype == Config::get('constants.Reports_Current')) {
                $dataList = DB::table('trends_device_status')
                    ->select(DB::raw($dbcolumnname), 'object_type', 'object_name', 'device_uom',
                    // ->select('status_hour', 'object_type', 'object_name', 'device_uom',
                        DB::raw("TRUNC(AVG(present_value::numeric), 2) AS average"), 
                        DB::raw("TRUNC(SUM(present_value::numeric), 2) AS total"))                
                    ->where('status_date', $currentdate)
                    ->where('branch_id', session('branch_id'))
                    ->whereIn('object_name', $deviceNameList)
                    ->groupBy('status_period', 'object_type', 'object_name', 'device_uom')
                    ->orderByRaw("status_period, object_name")->get();
            }
            else {
                $daterange[] = [$startdate];
                $daterange[] = [$currentdate];

                if ($rangetype == Config::get('constants.Reports_Weekly')) {
                    $dataList = DB::table('trends_device_status')
                        ->select(DB::raw($dbcolumnname), 'status_day_sort', 'object_type', 'object_name', 'device_uom',
                            DB::raw("TRUNC(AVG(present_value::numeric), 2) AS average"), 
                            DB::raw("TRUNC(SUM(present_value::numeric), 2) AS total"))                
                        ->whereBetween('status_datetime', $daterange)
                        ->where('branch_id', session('branch_id'))
                        ->whereIn('object_name', $deviceNameList)
                        ->groupBy('status_period', 'status_day_sort', 'object_type', 'object_name', 'device_uom')
                        ->orderByRaw("status_day_sort, status_period, object_name")->get();
                }
                // else if ($rangetype == Config::get('constants.Reports_Monthly')) {}
                else if ($rangetype == Config::get('constants.Reports_Yearly')) {
                    $dataList = DB::table('trends_device_status')
                        ->select(DB::raw($dbcolumnname), 'object_type', 'object_name', 'device_uom',
                            DB::raw("TRUNC(AVG(present_value::numeric), 2) AS average"), 
                            DB::raw("TRUNC(SUM(present_value::numeric), 2) AS total"))                
                        ->whereBetween('status_datetime', $daterange)
                        ->where('branch_id', session('branch_id'))
                        ->whereIn('object_name', $deviceNameList)
                        ->groupBy('status_period', 'object_type', 'object_name', 'device_uom')
                        ->orderByRaw("status_period, object_name")->get();
                }
            }

            $strposition = -1;
            $callback = function() use($dataList, $reportheader, $deviceNameList, $compbrDevLocList) {
                $file = fopen('php://output', 'w');
                fputcsv($file, [$reportheader]);
                for ($i = 0; $i < count($deviceNameList); $i += 1) {
                    $brdevloc = $compbrDevLocList->where('device_name', $deviceNameList[$i])->pluck(['trends_calculation']);
                    $trendscalc = '';
                    foreach ($brdevloc as $obj1)
                        $trendscalc = $obj1;

                    $datasublist = $dataList->where('object_name', $deviceNameList[$i])->toArray();
                    $deviceNamewithUOM = '';
                    foreach ($datasublist as $datakey => $data) {
                        $deviceNamewithUOM = str_replace("_", " ", $deviceNameList[$i]) . ' (' . trim($data->device_uom) . ')';
                        break;
                    }

                    $isTotalColumn = false;
                    if ($trendscalc == Config::get('constants.TrendsCalculation'))
                        $columns = array('TIME', 'AVERAGE');
                    else {
                        $columns = array('TIME', 'AVERAGE', 'TOTAL');
                        $isTotalColumn = true;
                    }
        
                    fputcsv($file, [$deviceNamewithUOM]);
                    fputcsv($file, $columns);
        
                    foreach ($datasublist as $datakey => $data) {
                        if (intval(strpos($data->status_period, ":")) > 0) {
                            $strposition = strpos($data->status_period, " ");
                            if ($strposition > 0)
                                $row['TIME']  = substr($data->status_period, $strposition + 1);
                            else
                                $row['TIME']  = $data->status_period;
                        }
                        else
                            $row['TIME']  = $data->status_period;
                        $row['AVERAGE'] = $data->average;

                        if ($isTotalColumn) {
                            $row['TOTAL'] = $data->total;
                            fputcsv($file, array($row['TIME'], $row['AVERAGE'], $row['TOTAL']));
                        }
                        else {
                            fputcsv($file, array($row['TIME'], $row['AVERAGE']));
                        }
                    }

                    fputcsv($file, [" "]);
                    fputcsv($file, [" "]);
                    fputcsv($file, [" "]);
                }
                fclose($file);
            };            
            return response()->stream($callback, 200, $headers);
        }

        
    }
}