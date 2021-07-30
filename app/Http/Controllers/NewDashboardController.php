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

class NewDashboardController extends Controller
{
    public function index()
    {
        if (is_null(session('user_id'))) return redirect()->route('logout');

        ini_set('max_execution_time', Config::get('constants.ExecutionTimeOut'));
        $recordCount = Config::get('constants.GetDashboardDistechRecords');        
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
        $asnList = $disdevList->unique('asn_value');\Log::info($asnList);

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
        $compbrSettings = BranchSettings::where('branch_id', session('branch_id'))
            ->where('field', Config::get('constants.BS_Change_Case'))->first();
        $branchSettingsChangeCase = $compbrSettings->value;
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
            $actualname = str_ireplace(" sensor", "", $actualname);
            if ($branchSettingsChangeCase == Config::get('constants.BS_Change_Case_Y'))
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
            $objtemp = $actualname;
            if ($branchSettingsChangeCase == Config::get('constants.BS_Change_Case_Y'))
                $objtemp = ucwords($actualname);

            $brdevnamehdr = $compbrDevLocList->where('device_name', $objname)->pluck(['device_name_header']);
            $brdevnameheader = '';
            foreach ($brdevnamehdr as $obj)
                $brdevnameheader = $obj;
            
            $brdevloc = $compbrDevLocList->where('device_name', $objname)->pluck(['chart_type']);
            $brdevcharttype = '';
            foreach ($brdevloc as $obj)
                $brdevcharttype = $obj;

            $brdevlocclubbed = $compbrDevLocList->where('device_name', $objname)->pluck(['is_clubbed']);
            $brdevclubbed = '';
            foreach ($brdevlocclubbed as $obj)
                $brdevclubbed = $obj;

            $brdevlocsort = $compbrDevLocList->where('device_name', $objname)->pluck(['sort_order']);
            $brdevsort = 0;
            foreach ($brdevlocsort as $obj)
                $brdevsort = $obj;

            if ($brdevclubbed == '1') {
                $explodeName = strpbrk($brdevnameheader, "1234567890");
                if ($explodeName != '')
                {
                    $exploded = explode($explodeName, $brdevnameheader);
                    $clubbedName = trim($exploded[0], " ");
                }
                else
                    $clubbedName = $brdevnameheader;
            }
            else
                $clubbedName = $brdevnameheader;
            
            $clubbedName = str_ireplace(" sensor", "", $clubbedName);

            if ($branchSettingsChangeCase == Config::get('constants.BS_Change_Case_Y'))
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
                        $headerObjectNameList[] = ["name" => $objname, "actualname" => $actualname, "deviceName" => $clubbedName, "count" => 1, 
                            "chartType" => strtolower($brdevcharttype), 'sort_order' => $brdevsort];
                    }
                // }
            }

            $gaugedeviceName = $compbrDevLocList->where('device_name', $objname)->where('chart_type', Config::get('constants.chart_gauge'))->pluck(['device_name']);
            $brgaugedevname = '';
            foreach ($gaugedeviceName as $obj)
                $brgaugedevname = $obj;

            $brdevloc = $compbrDevLocList->where('device_name', $objname)->where('chart_type', Config::get('constants.chart_gauge'))->pluck(['device_location']);
            $brdevlocname = '';
            foreach ($brdevloc as $obj)
                $brdevlocname = $obj;

            $brdevloc = $compbrDevLocList->where('device_name', $objname)->pluck(['img_src']);
            $brimgsrc = '';
            foreach ($brdevloc as $obj)
                $brimgsrc = $obj;

            if ($brgaugedevname != '')
            {
                $chartCount += 1;
                if (strpos(strtolower($objname), "oom")) {}
                else
                    $gaugeChartObjectNameList[] = ['actualname' => $actualname, 'name' => $objname, 'deviceLocation' => $brdevlocname, 'presentvalue' => 0, 
                        'imgsrc' => $brimgsrc, 'sort_order' => $brdevsort];
            }

            $brdevloc = $compbrDevLocList->where('device_name', $objname)->where('chart_type', Config::get('constants.chart_value'))->pluck(['device_location']);
            $brdevlocname = '';
            foreach ($brdevloc as $obj)
                $brdevlocname = $obj;
            
            $valuedevname = $compbrDevLocList->where('device_name', $objname)->where('chart_type', Config::get('constants.chart_value'))->pluck(['device_name']);
            $brvaluedevname = '';
            foreach ($valuedevname as $obj)
                $brvaluedevname = $obj;
            if ($brvaluedevname != '')
                $valueChartObjectNameList[] = ['actualname' => $actualname, 'name' => $objname, 'deviceLocation' => $brdevlocname, 'presentvalue' => 0, 
                    'imgsrc' => $brimgsrc, 'sort_order' => $brdevsort];

            $brdevloc = $compbrDevLocList->where('device_name', $objname)->where('chart_type', Config::get('constants.chart_trend'))->pluck(['device_location']);
            $brdevlocname = '';
            foreach ($brdevloc as $obj)
                $brdevlocname = $obj;
            
            $valuedevname = $compbrDevLocList->where('device_name', $objname)->where('chart_type', Config::get('constants.chart_trend'))->pluck(['device_name']);
            $brvaluedevname = '';
            foreach ($valuedevname as $obj)
                $brvaluedevname = $obj;
            if ($brvaluedevname != '')
            {
                $chartCount += 1;
                $valueChartObjectNameList[] = ['actualname' => $actualname, 'name' => $objname, 'deviceLocation' => $brdevlocname, 'presentvalue' => 0, 
                    'imgsrc' => $brimgsrc, 'sort_order' => $brdevsort];
                $trendChartObjectNameList[] = ['actualname' => $actualname, 'name' => $objname, 'deviceLocation' => $brdevlocname, 'presentvalue' => 0, 
                    'imgsrc' => $brimgsrc, 'sort_order' => $brdevsort];
            }
        }
        
        array_multisort(array_column($headerObjectNameList, 'sort_order'),  SORT_ASC,
            $headerObjectNameList);
        array_multisort(array_column($gaugeChartObjectNameList, 'sort_order'),  SORT_ASC,
            $gaugeChartObjectNameList);
        array_multisort(array_column($valueChartObjectNameList, 'sort_order'),  SORT_ASC,
            $valueChartObjectNameList);
        array_multisort(array_column($trendChartObjectNameList, 'sort_order'),  SORT_ASC,
            $trendChartObjectNameList);\Log::info($gaugeChartObjectNameList);

        return view('newdashboardv2', [
            'refreshTime' => $branchSettingsRefreshTime,
            'chartCount' => $chartCount,
            'headerObjectNameList' => $headerObjectNameList,
            'binaryDeviceLocationList' => $binaryDeviceLocationList,
            'gaugeChartObjectNameList' => $gaugeChartObjectNameList,
            'valueChartObjectNameList' => $valueChartObjectNameList,
            'trendChartObjectNameList' => $trendChartObjectNameList,
        ]);
    }

    public function fetchcurrentdata()
    {
        $brcompbranchid = session('branch_id');
        if (isset($brcompbranchid))
        {
            ini_set('max_execution_time', Config::get('constants.ExecutionTimeOut'));
            $recordCount = Config::get('constants.GetDashboardDistechRecords');
            $currentdate = Carbon::now('Asia/colombo');
            $currdt = date('Y-m-d H:i:s', strtotime($currentdate));
            $compbr = CompanyBranch::where('id', session('branch_id'))->first();
            $compbrSettings = BranchSettings::where('branch_id', session('branch_id'))
                ->where('field', Config::get('constants.BS_Refresh_Time'))->first();
            $branchSettingsRefreshTime = intval($compbrSettings->value) * 60000;
            $compbrSettings = BranchSettings::where('branch_id', session('branch_id'))
                ->where('field', Config::get('constants.BS_Change_Case'))->first();
            $branchSettingsChangeCase = $compbrSettings->value;
            $compbrDevLocList = session('compbrDevLocList');

            // Get Weather Report
            $weathertemp = 'NA';
            $weatherstatus = 'NA';
            $weatherArray = array();
            $loclatitude = session('latitude');
            $loclongitude = session('longitude');
            if (isset($loclatitude) && isset($loclongitude)) {
                $weatherapi = Config::get('constants.weather_api') . 'lon=' . $loclongitude . '&lat=' . $loclatitude . Config::get('constants.weather_api_query');
                try {
                    $client = new Client();
                    $response = $client->request('GET', $weatherapi, 
                        [
                            'http_errors' => false, // For Exception Handling
                            'timeout' => Config::get('constants.guzzleTimeOut'),
                            'verify' => false,
                        ]);
            
                    if ($response->getStatusCode() == 200) {                
                        $serviceResult = $response->getBody()->getContents();
                        $serviceResultjson = json_decode($serviceResult, true);
                        if (isset($serviceResultjson)) {
                            foreach($serviceResultjson as $srkey => $srvalue) {
                                if ($srkey == 'dataseries')
                                {
                                    foreach($srvalue as $srkey1 => $srvalue1) {                            
                                        $weathertemp = $srvalue1['temp2m']['min'] . '/' . $srvalue1['temp2m']['max'];
                                        $weatherstatus = $srvalue1['weather'];
                                        $weatherArray[] = ['temp' => $weathertemp, 'status' => $weatherstatus, 'uom' => Config::get('constants.uom_degree')];
                                    }
                                }
                            }
                        }
                        else {
                            $weathertemp = 'NA';
                            $weatherstatus = 'NA';
                            $weatherArray[] = ['temp' => $weathertemp, 'status' => $weatherstatus, 'uom' => Config::get('constants.uom_degree')];
                        }
                    }
                }
                catch (\Exception $e) {
                    $weathertemp = 'NA';
                    $weatherstatus = 'NA';
                    $weatherArray[] = ['temp' => $weathertemp, 'status' => $weatherstatus, 'uom' => ''];
                    \Log::info("Error:");
                    \Log::info($e);
                }
            }
            // Weather Report ends

            // Fetch Current BACnet(distech control) data
            $insertData = array();
            $tableName = $compbr->table_name;
            // $branchDevLocList = BranchDeviceLocation::where('company_id', $comp->id)
            //     ->where('branch_id', $compbr->id)
            //     ->where('is_active', 1)
            //     ->orderBy('id')->get();
            $branchDevLocList = $compbrDevLocList;
            try 
            {
                $clientTest = new Client();
                $response = $clientTest->request('GET', $compbr->distech_deviceip . '/api/rest/v1/protocols/bacnet/local/objects/', 
                    [
                        'http_errors' => false, // For Exception Handling
                        'timeout' => Config::get('constants.guzzleTimeOut'),
                        'verify' => false,
                        'auth' => [$compbr->distech_username, $compbr->distech_password]
                    ]);
                if ($response->getStatusCode() == 200)
                {
                    $objectType = Config::get('constants.api_analog_input');
                    try
                    {                            
                        $client = new Client();
                        $response = $client->request('GET', $compbr->distech_deviceip . '/api/rest/v1/protocols/bacnet/local/objects/' . $objectType, 
                            [
                                'http_errors' => false, // For Exception Handling
                                'verify' => false,
                                'auth' => [$compbr->distech_username, $compbr->distech_password]
                            ]);
                    
                        $serviceResult = $response->getBody()->getContents();
                        $serviceResultjson = json_decode($serviceResult, true);
                        foreach($serviceResultjson as $srkey => $srvalue)
                        {
                            $client1 = new Client();
                            $response1 = $client1->request('GET', $compbr->distech_deviceip . $srvalue['href'] . '/properties/object-name', 
                                [
                                    'verify' => false,
                                    'auth' => [$compbr->distech_username, $compbr->distech_password]
                                ]);
                            $serviceResult1 = $response1->getBody()->getContents();
                            $serviceResultjson1 = json_decode($serviceResult1, true);
                            foreach($serviceResultjson1 as $srkey1 => $srvalue1)
                            {
                                $objectName = $srvalue1;
                                break;
                            }

                            $count = 0;
                            foreach($serviceResultjson1 as $srkey1 => $srvalue1)
                            {
                                $count += 1;
                                $asnValue = $srvalue1;
                                if($count == 2)
                                    break;
                            }

                            $client1 = new Client();
                            $response1 = $client1->request('GET', $compbr->distech_deviceip . $srvalue['href'] . '/properties/present-value', 
                                [
                                    'verify' => false,
                                    'auth' => [$compbr->distech_username, $compbr->distech_password]
                                ]);
                            $serviceResult1 = $response1->getBody()->getContents();
                            $serviceResultjson1 = json_decode($serviceResult1, true);
                            foreach($serviceResultjson1 as $srkey1 => $srvalue1)
                            {
                                $presentValue = $srvalue1;
                                break;
                            }

                            $devlocname = $branchDevLocList->where('device_name', $objectName)->pluck('device_location');
                            foreach ($devlocname as $devloc)
                                $locname = $devloc;

                            $insertData[] = [
                                'company_id' => $compbr->company_id,
                                'branch_id' => $compbr->id,
                                'object_type' => $objectType,
                                'object_name' => $objectName,
                                'asn_value' => $asnValue,
                                'present_value' => $presentValue,
                                'device_location' => $locname,
                                // 'status_flag' => $statusFlags,
                                'status_date' => $currdt,
                                'status_time' => $currdt,
                                'distech_deviceip' => $compbr->distech_deviceip,
                            ];

                            // $client1 = new Client();
                            // $response1 = $client1->request('GET', $compbr->distech_deviceip . $srvalue['href'] . '/properties/status-flags', 
                            //     [
                            //         'verify' => false,
                            //         'auth' => [$compbr->distech_username, $compbr->distech_password]
                            //     ]);
                            // $serviceResult1 = $response1->getBody()->getContents();
                            // $serviceResultjson1 = json_decode($serviceResult1, true);
                            // foreach($serviceResultjson1 as $srkey1 => $srvalue1)
                            // {
                            //     $statusFlags = $srvalue1;
                            //     break;
                            // }
                            
                            // $disdev = new DistechDevice;
                            // $disdev->company_id = $compbr->company_id;
                            // $disdev->branch_id = $compbr->id;
                            // $disdev->object_type = $objectType;
                            // $disdev->object_name = $objectName;
                            // $disdev->asn_value = $asnValue;
                            // $disdev->present_value = $presentValue;
                            // // $disdev->status_flag = $statusFlags;
                            // $disdev->status_date = $currdt;
                            // $disdev->status_time = $currdt;
                            // $disdev->distech_deviceip = $compbr->distech_deviceip;
                            // $disdev->save();
                        }
                    }
                    catch (\Exception $e) {
                        \Log::info("Error: time: " . $currdt . " for Company Branch Id: " . $compbr->id . " object type: " . $objectType);
                        \Log::info($e);
                    }
                    
                    $objectType = Config::get('constants.api_analog_value');
                    try
                    {    
                        $client = new Client();
                        $response = $client->request('GET', $compbr->distech_deviceip . '/api/rest/v1/protocols/bacnet/local/objects/' . $objectType, 
                            [
                                'verify' => false,
                                'auth' => [$compbr->distech_username, $compbr->distech_password]
                            ]);
                    
                        $serviceResult = $response->getBody()->getContents();
                        $serviceResultjson = json_decode($serviceResult, true);
                        foreach($serviceResultjson as $srkey => $srvalue)
                        {
                            $client1 = new Client();
                            $response1 = $client1->request('GET', $compbr->distech_deviceip . $srvalue['href'] . '/properties/object-name', 
                                [
                                    'verify' => false,
                                    'auth' => [$compbr->distech_username, $compbr->distech_password]
                                ]);
                            $serviceResult1 = $response1->getBody()->getContents();
                            $serviceResultjson1 = json_decode($serviceResult1, true);
                            foreach($serviceResultjson1 as $srkey1 => $srvalue1)
                            {
                                $objectName = $srvalue1;
                                break;
                            }

                            $count = 0;
                            foreach($serviceResultjson1 as $srkey1 => $srvalue1)
                            {
                                $count += 1;
                                $asnValue = $srvalue1;
                                if($count == 2)
                                    break;
                            }

                            $client1 = new Client();
                            $response1 = $client1->request('GET', $compbr->distech_deviceip . $srvalue['href'] . '/properties/present-value', 
                                [
                                    'verify' => false,
                                    'auth' => [$compbr->distech_username, $compbr->distech_password]
                                ]);
                            $serviceResult1 = $response1->getBody()->getContents();
                            $serviceResultjson1 = json_decode($serviceResult1, true);
                            foreach($serviceResultjson1 as $srkey1 => $srvalue1)
                            {
                                $presentValue = $srvalue1;
                                break;
                            }

                            $devlocname = $branchDevLocList->where('device_name', $objectName)->pluck('device_location');
                            foreach ($devlocname as $devloc)
                                $locname = $devloc;

                            $insertData[] = [
                                'company_id' => $compbr->company_id,
                                'branch_id' => $compbr->id,
                                'object_type' => $objectType,
                                'object_name' => $objectName,
                                'asn_value' => $asnValue,
                                'present_value' => $presentValue,
                                'device_location' => $locname,
                                // 'status_flag' => $statusFlags,
                                'status_date' => $currdt,
                                'status_time' => $currdt,
                                'distech_deviceip' => $compbr->distech_deviceip,
                            ];

                            // $client1 = new Client();
                            // $response1 = $client1->request('GET', $compbr->distech_deviceip . $srvalue['href'] . '/properties/status-flags', 
                            //     [
                            //         'verify' => false,
                            //         'auth' => [$compbr->distech_username, $compbr->distech_password]
                            //     ]);
                            // $serviceResult1 = $response1->getBody()->getContents();
                            // $serviceResultjson1 = json_decode($serviceResult1, true);
                            // foreach($serviceResultjson1 as $srkey1 => $srvalue1)
                            // {
                            //     $statusFlags = $srvalue1;
                            //     break;
                            // }

                            // $disdev = new DistechDevice;
                            // $disdev->company_id = $compbr->company_id;
                            // $disdev->branch_id = $compbr->id;
                            // $disdev->object_type = $objectType;
                            // $disdev->object_name = $objectName;
                            // $disdev->asn_value = $asnValue;
                            // $disdev->present_value = $presentValue;
                            // // $disdev->status_flag = $statusFlags;
                            // $disdev->status_date = $currdt;
                            // $disdev->status_time = $currdt;
                            // $disdev->distech_deviceip = $compbr->distech_deviceip;
                            // $disdev->save();
                        }
                    }
                    catch (\Exception $e) {
                        \Log::info("Error: time: " . $currdt . "  for Company Branch Id: " . $compbr->id . " object type: " . $objectType);
                        \Log::info($e);
                    }

                    $objectType = Config::get('constants.api_binary_value');
                    try
                    {
                        $client = new Client();
                        $response = $client->request('GET', $compbr->distech_deviceip . '/api/rest/v1/protocols/bacnet/local/objects/' . $objectType, 
                            [
                                'verify' => false,
                                'auth' => [$compbr->distech_username, $compbr->distech_password]
                            ]);
                    
                        $serviceResult = $response->getBody()->getContents();
                        $serviceResultjson = json_decode($serviceResult, true);
                        foreach($serviceResultjson as $srkey => $srvalue)
                        {
                            $client1 = new Client();
                            $response1 = $client1->request('GET', $compbr->distech_deviceip . $srvalue['href'] . '/properties/object-name', 
                                [
                                    'verify' => false,
                                    'auth' => [$compbr->distech_username, $compbr->distech_password]
                                ]);
                            $serviceResult1 = $response1->getBody()->getContents();
                            $serviceResultjson1 = json_decode($serviceResult1, true);
                            foreach($serviceResultjson1 as $srkey1 => $srvalue1)
                            {
                                $objectName = $srvalue1;
                                break;
                            }

                            $count = 0;
                            foreach($serviceResultjson1 as $srkey1 => $srvalue1)
                            {
                                $count += 1;
                                $asnValue = $srvalue1;
                                if($count == 2)
                                    break;
                            }

                            $client1 = new Client();
                            $response1 = $client1->request('GET', $compbr->distech_deviceip . $srvalue['href'] . '/properties/present-value', 
                                [
                                    'verify' => false,
                                    'auth' => [$compbr->distech_username, $compbr->distech_password]
                                ]);
                            $serviceResult1 = $response1->getBody()->getContents();
                            $serviceResultjson1 = json_decode($serviceResult1, true);
                            foreach($serviceResultjson1 as $srkey1 => $srvalue1)
                            {
                                $presentValue = $srvalue1;
                                break;
                            }

                            $devlocname = $branchDevLocList->where('device_name', $objectName)->pluck('device_location');
                            foreach ($devlocname as $devloc)
                                $locname = $devloc;

                            $insertData[] = [
                                'company_id' => $compbr->company_id,
                                'branch_id' => $compbr->id,
                                'object_type' => $objectType,
                                'object_name' => $objectName,
                                'asn_value' => $asnValue,
                                'present_value' => $presentValue,
                                'device_location' => $locname,
                                // 'status_flag' => $statusFlags,
                                'status_date' => $currdt,
                                'status_time' => $currdt,
                                'distech_deviceip' => $compbr->distech_deviceip,
                            ];

                            // $client1 = new Client();
                            // $response1 = $client1->request('GET', $compbr->distech_deviceip . $srvalue['href'] . '/properties/status-flags', 
                            //     [
                            //         'verify' => false,
                            //         'auth' => [$compbr->distech_username, $compbr->distech_password]
                            //     ]);
                            // $serviceResult1 = $response1->getBody()->getContents();
                            // $serviceResultjson1 = json_decode($serviceResult1, true);
                            // foreach($serviceResultjson1 as $srkey1 => $srvalue1)
                            // {
                            //     $statusFlags = $srvalue1;
                            //     break;
                            // }

                            // $disdev = new DistechDevice;
                            // $disdev->company_id = $compbr->company_id;
                            // $disdev->branch_id = $compbr->id;
                            // $disdev->object_type = $objectType;
                            // $disdev->object_name = $objectName;
                            // $disdev->asn_value = $asnValue;
                            // $disdev->present_value = $presentValue;
                            // $disdev->status_flag = $statusFlags;
                            // $disdev->status_date = $currdt;
                            // $disdev->status_time = $currdt;
                            // $disdev->distech_deviceip = $compbr->distech_deviceip;
                            // $disdev->save();
                        }
                    }
                    catch (\Exception $e) {
                        \Log::info("Error: time: " . $currdt . "  for Company Branch Id: " . $compbr->id . " object type: " . $objectType);
                        \Log::info($e);
                    }

                    $objectType = Config::get('constants.api_binary_input');
                    try
                    {
                        $client = new Client();
                        $response = $client->request('GET', $compbr->distech_deviceip . '/api/rest/v1/protocols/bacnet/local/objects/' . $objectType, 
                            [
                                'verify' => false,
                                'auth' => [$compbr->distech_username, $compbr->distech_password]
                            ]);
                    
                        $serviceResult = $response->getBody()->getContents();
                        $serviceResultjson = json_decode($serviceResult, true);
                        foreach($serviceResultjson as $srkey => $srvalue){
                            $client1 = new Client();
                            $response1 = $client1->request('GET', $compbr->distech_deviceip . $srvalue['href'] . '/properties/object-name', 
                                [
                                    'verify' => false,
                                    'auth' => [$compbr->distech_username, $compbr->distech_password]
                                ]);
                            $serviceResult1 = $response1->getBody()->getContents();
                            $serviceResultjson1 = json_decode($serviceResult1, true);
                            foreach($serviceResultjson1 as $srkey1 => $srvalue1)
                            {
                                $objectName = $srvalue1;
                                break;
                            }
                            
                            $count = 0;
                            foreach($serviceResultjson1 as $srkey1 => $srvalue1)
                            {
                                $count += 1;
                                $asnValue = $srvalue1;
                                if($count == 2)
                                    break;
                            }

                            $client1 = new Client();
                            $response1 = $client1->request('GET', $compbr->distech_deviceip . $srvalue['href'] . '/properties/present-value', 
                                [
                                    'verify' => false,
                                    'auth' => [$compbr->distech_username, $compbr->distech_password]
                                ]);
                            $serviceResult1 = $response1->getBody()->getContents();
                            $serviceResultjson1 = json_decode($serviceResult1, true);
                            foreach($serviceResultjson1 as $srkey1 => $srvalue1)
                            {
                                $presentValue = $srvalue1;
                                break;
                            }

                            $devlocname = $branchDevLocList->where('device_name', $objectName)->pluck('device_location');
                            foreach ($devlocname as $devloc)
                                $locname = $devloc;

                            $insertData[] = [
                                'company_id' => $compbr->company_id,
                                'branch_id' => $compbr->id,
                                'object_type' => $objectType,
                                'object_name' => $objectName,
                                'asn_value' => $asnValue,
                                'present_value' => $presentValue,
                                'device_location' => $locname,
                                // 'status_flag' => $statusFlags,
                                'status_date' => $currdt,
                                'status_time' => $currdt,
                                'distech_deviceip' => $compbr->distech_deviceip,
                            ];

                            // $client1 = new Client();
                            // $response1 = $client1->request('GET', $compbr->distech_deviceip . $srvalue['href'] . '/properties/status-flags', 
                            //     [
                            //         'verify' => false,
                            //         'auth' => [$compbr->distech_username, $compbr->distech_password]
                            //     ]);
                            // $serviceResult1 = $response1->getBody()->getContents();
                            // $serviceResultjson1 = json_decode($serviceResult1, true);
                            // foreach($serviceResultjson1 as $srkey1 => $srvalue1)
                            // {
                            //     $statusFlags = $srvalue1;
                            //     break;
                            // }

                            // $disdev = new DistechDevice;
                            // $disdev->company_id = $compbr->company_id;
                            // $disdev->branch_id = $compbr->id;
                            // $disdev->object_type = $objectType;
                            // $disdev->object_name = $objectName;
                            // $disdev->asn_value = $asnValue;
                            // $disdev->present_value = $presentValue;
                            // $disdev->status_flag = $statusFlags;
                            // $disdev->status_date = $currdt;
                            // $disdev->status_time = $currdt;
                            // $disdev->distech_deviceip = $compbr->distech_deviceip;
                            // $disdev->save();
                        }
                    }
                    catch (\Exception $e) {
                        \Log::info("Error: time: " . $currdt . "  for Company Branch Id: " . $compbr->id . " object type: " . $objectType);
                        \Log::info($e);
                    }

                    $objectType = Config::get('constants.api_binary_output');
                    try
                    {
                        $client = new Client();
                        $response = $client->request('GET', $compbr->distech_deviceip . '/api/rest/v1/protocols/bacnet/local/objects/' . $objectType, 
                            [
                                'verify' => false,
                                'auth' => [$compbr->distech_username, $compbr->distech_password]
                            ]);
                    
                        $serviceResult = $response->getBody()->getContents();
                        $serviceResultjson = json_decode($serviceResult, true);
                        foreach($serviceResultjson as $srkey => $srvalue){
                            $client1 = new Client();
                            $response1 = $client1->request('GET', $compbr->distech_deviceip . $srvalue['href'] . '/properties/object-name', 
                                [
                                    'verify' => false,
                                    'auth' => [$compbr->distech_username, $compbr->distech_password]
                                ]);
                            $serviceResult1 = $response1->getBody()->getContents();
                            $serviceResultjson1 = json_decode($serviceResult1, true);
                            foreach($serviceResultjson1 as $srkey1 => $srvalue1)
                            {
                                $objectName = $srvalue1;
                                break;
                            }
                            
                            $count = 0;
                            foreach($serviceResultjson1 as $srkey1 => $srvalue1)
                            {
                                $count += 1;
                                $asnValue = $srvalue1;
                                if($count == 2)
                                    break;
                            }

                            $client1 = new Client();
                            $response1 = $client1->request('GET', $compbr->distech_deviceip . $srvalue['href'] . '/properties/present-value', 
                                [
                                    'verify' => false,
                                    'auth' => [$compbr->distech_username, $compbr->distech_password]
                                ]);
                            $serviceResult1 = $response1->getBody()->getContents();
                            $serviceResultjson1 = json_decode($serviceResult1, true);
                            foreach($serviceResultjson1 as $srkey1 => $srvalue1)
                            {
                                $presentValue = $srvalue1;
                                break;
                            }

                            $devlocname = $branchDevLocList->where('device_name', $objectName)->pluck('device_location');
                            foreach ($devlocname as $devloc)
                                $locname = $devloc;

                            $insertData[] = [
                                'company_id' => $compbr->company_id,
                                'branch_id' => $compbr->id,
                                'object_type' => $objectType,
                                'object_name' => $objectName,
                                'asn_value' => $asnValue,
                                'present_value' => $presentValue,
                                'device_location' => $locname,
                                // 'status_flag' => $statusFlags,
                                'status_date' => $currdt,
                                'status_time' => $currdt,
                                'distech_deviceip' => $compbr->distech_deviceip,
                            ];

                            // $client1 = new Client();
                            // $response1 = $client1->request('GET', $compbr->distech_deviceip . $srvalue['href'] . '/properties/status-flags', 
                            //     [
                            //         'verify' => false,
                            //         'auth' => [$compbr->distech_username, $compbr->distech_password]
                            //     ]);
                            // $serviceResult1 = $response1->getBody()->getContents();
                            // $serviceResultjson1 = json_decode($serviceResult1, true);
                            // foreach($serviceResultjson1 as $srkey1 => $srvalue1)
                            // {
                            //     $statusFlags = $srvalue1;
                            //     break;
                            // }

                            // $disdev = new DistechDevice;
                            // $disdev->company_id = $compbr->company_id;
                            // $disdev->branch_id = $compbr->id;
                            // $disdev->object_type = $objectType;
                            // $disdev->object_name = $objectName;
                            // $disdev->asn_value = $asnValue;
                            // $disdev->present_value = $presentValue;
                            // $disdev->status_flag = $statusFlags;
                            // $disdev->status_date = $currdt;
                            // $disdev->status_time = $currdt;
                            // $disdev->distech_deviceip = $compbr->distech_deviceip;
                            // $disdev->save();
                        }
                    }
                    catch (\Exception $e) {
                        \Log::info("Error: time: " . $currdt . "  for Company Branch Id: " . $compbr->id . " object type: " . $objectType);
                        \Log::info($e);
                    }
                    DB::table($tableName)->insert($insertData);
                }
                else
                {
                    \Log::info("Error: " . $response->getStatusCode() . " for Company Branch Id: " . $compbr->id);
                }
            }
            catch (\Exception $e) {
                \Log::info("Error: time: " . $currdt . "  for Company Branch Id: " . $compbr->id);
                \Log::info($e);
            }
            // Fetch Current BACnet(distech control) data ends

            $timeList = DB::table(session('table_name'))
                ->distinct()
                ->where('company_id', session('company_id'))
                ->where('branch_id', session('branch_id'))
                ->where('status_date', $currdt)
                ->orderByDesc('status_time')
                ->take($recordCount)
                ->pluck('status_time');
            $timeList = $timeList->reverse();
            // $timeLastItem = $timeList->pop();
            $timeLastItem = $timeList->last();
            session(['timeLastItem' => $timeLastItem]);\Log::info('session time- currentdate: ' . session('timeLastItem'));

            $recordStatus = 'OFFLINE';
            if (isset($timeLastItem))
                $recordStatus = 'ONLINE';
            
            $disdevList = DB::table(session('table_name'))
                ->where('company_id', session('company_id'))
                ->where('branch_id', session('branch_id'))
                ->where('status_date', $currdt)
                ->whereIn('status_time', $timeList)
                ->orderBy('object_type')
                ->orderBy('object_name')
                ->orderBy('status_time')
                ->orderBy('id')
                ->get();
            $timeList = json_decode(json_encode($timeList), true);
            $asnList = $disdevList->unique('asn_value');
                
            // $asnList = DB::table(session('table_name'))
            //     ->distinct()
            //     ->where('company_id', session('company_id'))
            //     ->where('branch_id', session('branch_id'))
            //     ->where('status_date', $currdt)
            //     ->whereIn('status_time', $timeList)
            //     ->pluck('asn_value');
            $chartCount = 0;        
            $headerObjectNameList = array();
            $gaugeChartObjectNameList = array();
            $valueChartObjectNameList = array();
            $trendChartObjectNameList = array();
            $binaryDeviceLocationList = array();
            $allChartArray = array();
            $timeArr = array();

            foreach ($timeList as $tm)
                $timeArr[] = date(Config::get('constants.XAxisTimeFormat'), strtotime($tm));
            
            foreach ($asnList as $asnkey => $asnvalue)
            {
            // foreach ($asnList as $asn) 
            // {
                $asn = $asnvalue->asn_value;
                $disdevtempList = $disdevList->where('asn_value', $asn)->pluck('present_value');
                $objectName = $disdevList->where('asn_value', $asn)->pluck('object_name');
                $disdevPresent = $disdevList->where('asn_value', $asn)->pluck('present_value');
                $objectType = $disdevList->where('asn_value', $asn)->pluck('object_type');
                $devlocname = $disdevList->where('asn_value', $asn)->pluck(['device_location']);
                $uom = '';
                $presentValWithUOM = '';

                foreach ($devlocname as $obj)
                    $devlocationname = $obj;
                foreach ($objectName as $obj)
                    $objname = $obj;
                foreach ($objectType as $obj)
                    $objtypename = $obj;
                foreach ($disdevPresent as $ddpresent)
                    $objpresentVal = $ddpresent;

                $brdevloc = $compbrDevLocList->where('device_name', $objname)->pluck(['plot_min']);
                $plotmin = '';
                foreach ($brdevloc as $obj)
                    $plotmin = $obj;

                $brdevloc = $compbrDevLocList->where('device_name', $objname)->pluck(['plot_max']);
                $plotmax = '';
                foreach ($brdevloc as $obj)
                    $plotmax = $obj;

                $brdevloc = $compbrDevLocList->where('device_name', $objname)->pluck(['plot_bands']);
                $plotbands = '';
                foreach ($brdevloc as $obj)
                    $plotbands = $obj;
                
                $brdevloc = $compbrDevLocList->where('device_name', $objname)->pluck(['show_in_header']);
                foreach ($brdevloc as $obj)
                    $showinheader = $obj;

                $brdevloc = $compbrDevLocList->where('device_name', $objname)->pluck(['device_description']);
                foreach ($brdevloc as $obj)
                    $devDescription = $obj;

                $brdevloc = $compbrDevLocList->where('device_name', $objname)->pluck(['device_uom']);
                foreach ($brdevloc as $obj)
                    $uom = $obj;

                // if (strlen(floor($objpresentVal)) > 2)
                //     $objpresentVal = $objpresentVal / 1000;

                $plotBandsArray = array();
                if (!empty($plotbands)) {
                    $guageplotexploded = explode(';', $plotbands);
                    for ($i = 0; $i < count($guageplotexploded); $i += 1) {
                        $gaugebandexploded = explode(',', $guageplotexploded[$i]);
                        if (count($gaugebandexploded) > 2)
                            $plotBandsArray[] = ['from' => $gaugebandexploded[0], 'to' => $gaugebandexploded[1], 'color' => $gaugebandexploded[2]];
                    }
                }
                
                $presentValWithUOM = $objpresentVal;
                $actualname = str_replace("_", " ", $objname);
                $actualname = str_ireplace(" sensor", "", $actualname);
                $objtemp = $actualname;
                if ($branchSettingsChangeCase == Config::get('constants.BS_Change_Case_Y')) {
                    $actualname = ucwords($actualname);
                    $objtemp = ucwords($actualname);
                }

                $brdevlocclubbed = $compbrDevLocList->where('device_name', $objname)->pluck(['is_clubbed']);
                $brdevclubbed = '';
                foreach ($brdevlocclubbed as $obj)
                    $brdevclubbed = $obj;

                if ($brdevclubbed == '1') {
                    $explodeName = strpbrk($objtemp, "1234567890");
                    if ($explodeName != '')
                    {
                        $exploded = explode($explodeName, $actualname);
                        $clubbedName = trim($exploded[0], " ");
                    }
                    else
                        $clubbedName = $objtemp;
                }
                else
                    $clubbedName = $objtemp;
                $clubbedIndex = array_search($clubbedName, array_column($headerObjectNameList, "deviceName"));
                $binClubbedIndex = array_search($clubbedName, array_column($binaryDeviceLocationList, "deviceName"));

                if ($binClubbedIndex !== false) {
                    $binaryDeviceLocationList[$binClubbedIndex]["deviceLocation"] = $binaryDeviceLocationList[$binClubbedIndex]["deviceLocation"] . ',' . $devlocationname;
                    $binaryDeviceLocationList[$binClubbedIndex]["name"] = $binaryDeviceLocationList[$binClubbedIndex]["name"] . ',' . $objname;
                    $binaryDeviceLocationList[$binClubbedIndex]["count"] = intval($binaryDeviceLocationList[$binClubbedIndex]["count"]) + 1;

                    if ($objtypename == Config::get('constants.api_binary_input') || $objtypename == Config::get('constants.api_binary_value') || $objtypename == Config::get('constants.api_binary_output')) {
                        if (strtolower($objpresentVal) == 'active' || strtolower($objpresentVal) == 'true' || strtolower($objpresentVal) == 'on') {
                            $binaryDeviceLocationList[$binClubbedIndex]["activevalue"] = intval($binaryDeviceLocationList[$binClubbedIndex]["activevalue"]) + 1;
                            $binaryDeviceLocationList[$binClubbedIndex]["activeval"] = $binaryDeviceLocationList[$binClubbedIndex]["activeval"] . ',' . '1';
                        }
                        else
                            $binaryDeviceLocationList[$binClubbedIndex]["activeval"] = $binaryDeviceLocationList[$binClubbedIndex]["activeval"] . ',' . '0';
                    }
                }
                else
                {
                    if ($objtypename == Config::get('constants.api_binary_input') || $objtypename == Config::get('constants.api_binary_value') || $objtypename == Config::get('constants.api_binary_output')) {
                        if (strtolower($objpresentVal) == 'active' || strtolower($objpresentVal) == 'true' || strtolower($objpresentVal) == 'on')
                            $binaryDeviceLocationList[] = ["name" => $objname, "actualname" => $actualname, "deviceName" => $clubbedName, "deviceLocation" => $devlocationname, "count" => 1, "activevalue" => 1, "activeval" => '1'];
                        else
                            $binaryDeviceLocationList[] = ["name" => $objname, "actualname" => $actualname, "deviceName" => $clubbedName, "deviceLocation" => $devlocationname, "count" => 1, "activevalue" => 0, "activeval" => '0'];
                    }
                }

                // $uom = '';
                // if (strpos(strtolower($objtemp), "temp") || strpos(strtolower($objtemp), "old"))
                //     $uom = Config::get('constants.uom_degree');
                // elseif (strpos(strtolower($objtemp), "kwh") || strpos(strtolower($objtemp), "energy"))
                //     $uom = Config::get('constants.uom_kwh');
                // elseif (strpos(strtolower($objtemp), "kw") || strpos(strtolower($objtemp), "power"))
                //     $uom = Config::get('constants.uom_kw');
                $presentValWithUOM = number_format(floatval($objpresentVal), 2) . $uom;
                $activeval = 1;
                        
                // if ($clubbedIndex !== false) {
                //     // if (count($plotBandsArray) > 0) {
                //     //     if ($objpresentVal < $plotBandsArray[0]['from'] || $objpresentVal > $plotBandsArray[count($plotBandsArray) - 1]['to'])
                //     //         $activeval = 0;
                //     //     else if ($objpresentVal >= $plotBandsArray[0]['from'] && $objpresentVal <= $plotBandsArray[0]['to'])
                //     //         $activeval = 0;
                //     //     else if ($objpresentVal >= $plotBandsArray[count($plotBandsArray) - 1]['from'] && $objpresentVal <= $plotBandsArray[count($plotBandsArray) - 1]['to'])
                //     //         $activeval = 0;
                //     // }
                //     // else if ($plotmin != 0 || $plotmax != 0) {
                //     //     if ($objpresentVal > $plotmin || $objpresentVal > $plotmax)
                //     //         $activeval = 0;
                //     // }
                //     $headerObjectNameList[$clubbedIndex]["count"] = intval($headerObjectNameList[$clubbedIndex]["count"]) + 1;
                //     $headerObjectNameList[$clubbedIndex]["activevalue"] = intval($headerObjectNameList[$clubbedIndex]["activevalue"]) + $activeval;
                // }
                // else
                // {
                //     $brdevloc1 = $compbrDevLocList->where('device_name', $objname)->pluck(['show_in_header']);
                //     $brdevlocname1 = 0;
                //     foreach ($brdevloc1 as $obj1)
                //         $brdevlocname1 = $obj1;
                //     if ($brdevlocname1 == 1) {
                //         // if (count($plotBandsArray) > 0) {
                //         //     if ($objpresentVal < $plotBandsArray[0]['from'] || $objpresentVal > $plotBandsArray[count($plotBandsArray) - 1]['to'])
                //         //         $activeval = 0;
                //         //     else if ($objpresentVal >= $plotBandsArray[0]['from'] && $objpresentVal <= $plotBandsArray[0]['to'])
                //         //         $activeval = 0;
                //         //     else if ($objpresentVal >= $plotBandsArray[count($plotBandsArray) - 1]['from'] && $objpresentVal <= $plotBandsArray[count($plotBandsArray) - 1]['to'])
                //         //         $activeval = 0;
                //         // }
                //         // else if ($plotmin != 0 || $plotmax != 0) {
                //         //     if ($objpresentVal > $plotmin || $objpresentVal > $plotmax)
                //         //         $activeval = 0;
                //         // }
                //         headerObjectNameList[] = ["name" => $objname, "actualname" => $actualname, "deviceName" => $clubbedName, 
                //             "count" => 1, "activevalue" => $activeval, "presentvalue" => $presentValWithUOM, "type" => $objtypename,
                //             "plotmin" => $plotmin, "plotmax" => $plotmax, "currvalue" => $objpresentVal];
                //     }
                // }

                if ($clubbedIndex !== false) {
                    if (count($plotBandsArray) > 0) {
                        if ($objpresentVal < $plotBandsArray[0]['from'] || $objpresentVal > $plotBandsArray[count($plotBandsArray) - 1]['to'])
                            $activeval = 0;
                        else if ($objpresentVal >= $plotBandsArray[0]['from'] && $objpresentVal <= $plotBandsArray[0]['to'])
                            $activeval = 0;
                        else if ($objpresentVal >= $plotBandsArray[count($plotBandsArray) - 1]['from'] && $objpresentVal <= $plotBandsArray[count($plotBandsArray) - 1]['to'])
                            $activeval = 0;
                    }
                    else if ($plotmin != 0 || $plotmax != 0) {
                        if ($objpresentVal > $plotmin || $objpresentVal > $plotmax)
                            $activeval = 0;
                    }
                    $headerObjectNameList[$clubbedIndex]["count"] = intval($headerObjectNameList[$clubbedIndex]["count"]) + 1;
                    $headerObjectNameList[$clubbedIndex]["activevalue"] = intval($headerObjectNameList[$clubbedIndex]["activevalue"]) + $activeval;
                }
                else
                {
                    $brdevloc1 = $compbrDevLocList->where('device_name', $objname)->pluck(['show_in_header']);
                    $brdevlocname1 = 0;
                    foreach ($brdevloc1 as $obj1)
                        $brdevlocname1 = $obj1;
                    if ($brdevlocname1 == 1) {
                        if (count($plotBandsArray) > 0) {
                            if ($objpresentVal < $plotBandsArray[0]['from'] || $objpresentVal > $plotBandsArray[count($plotBandsArray) - 1]['to'])
                                $activeval = 0;
                            else if ($objpresentVal >= $plotBandsArray[0]['from'] && $objpresentVal <= $plotBandsArray[0]['to'])
                                $activeval = 0;
                            else if ($objpresentVal >= $plotBandsArray[count($plotBandsArray) - 1]['from'] && $objpresentVal <= $plotBandsArray[count($plotBandsArray) - 1]['to'])
                                $activeval = 0;
                        }
                        else if ($plotmin != 0 || $plotmax != 0) {
                            if ($objpresentVal > $plotmin || $objpresentVal > $plotmax)
                                $activeval = 0;
                        }

                        $headerObjectNameList[] = ["name" => $objname, "actualname" => $actualname, "deviceName" => $clubbedName, 
                            "count" => 1, "activevalue" => $activeval, "presentvalue" => $presentValWithUOM, "type" => $objtypename,
                            "plotmin" => $plotmin, "plotmax" => $plotmax, "currvalue" => $objpresentVal, "uom" => $uom];
                    }
                }

                $brdevloc = $compbrDevLocList->where('device_name', $objname)->where('chart_type', Config::get('constants.chart_gauge'))->pluck(['device_location']);
                $brdevlocname = '';
                foreach ($brdevloc as $obj)
                    $brdevlocname = $obj;
                if ($brdevlocname != '')
                {
                    $chartCount += 1;
                    $gaugeChartObjectNameList[] = ['actualname' => $actualname, 'name' => $objname, 'deviceLocation' => $brdevlocname, 'presentvalue' => 0];
                }

                $brdevloc = $compbrDevLocList->where('device_name', $objname)->where('chart_type', Config::get('constants.chart_value'))->pluck(['device_location']);
                $brdevlocname = '';
                foreach ($brdevloc as $obj)
                    $brdevlocname = $obj;
                if ($brdevlocname != '')
                    $valueChartObjectNameList[] = ['actualname' => $actualname, 'name' => $objname, 'deviceLocation' => $brdevlocname, 'presentvalue' => 0];

                $brdevloc = $compbrDevLocList->where('device_name', $objname)->where('chart_type', Config::get('constants.chart_trend'))->pluck(['device_location']);
                $brdevlocname = '';
                foreach ($brdevloc as $obj)
                    $brdevlocname = $obj;
                if ($brdevlocname != '')
                {
                    $chartCount += 1;
                    $valueChartObjectNameList[] = ['actualname' => $actualname, 'name' => $objname, 'deviceLocation' => $brdevlocname, 'presentvalue' => 0];
                    $trendChartObjectNameList[] = ['actualname' => $actualname, 'name' => $objname, 'deviceLocation' => $brdevlocname, 'presentvalue' => 0];
                }

                // For AllCharts Array
                $disdevtempList = $disdevList->where('asn_value', $asn);
                $deviceTimeData = array();
                $isnumericData = false;
                    
                foreach ($timeList as $tm) {
                    $disdevtimetempList = $disdevtempList->where('status_time', $tm)->pluck('present_value');
                    foreach ($disdevtimetempList as $ddev) {
                        if (is_numeric($ddev)) {
                            if (floatval($ddev) != 0)
                                $isnumericData = true;

                            // if (strlen(floor($ddev)) > 2)
                            //     $ddev = $ddev / 1000;
                            $roundedValue = substr($ddev, 0, ((strpos($ddev, '.') + 1) + 2));
                            $deviceTimeData[] = ["category" => date(Config::get('constants.XAxisTimeFormat'), strtotime($tm)), "devdata" => $ddev, "roundedData" => $roundedValue, "categoryVal" => date(Config::get('constants.XAxisTimeFormat'), strtotime($tm))];
                        }
                    }
                }

                if ($isnumericData) {
                    $brdevloc = $compbrDevLocList->where('device_name', $objname)->pluck(['chart_type']);
                    $charttype = '';
                    foreach ($brdevloc as $obj)
                        $charttype = $obj;
                        
                    $allChartArray[] = ['actualname' => $actualname, "objectName" => $objname, "deviceType" => $objtypename, "deviceName" => $objtemp, "presentValue" => $presentValWithUOM,
                        "charttype" => $charttype, "plotmin" => $plotmin, "plotmax" => $plotmax, "plotbands" => $plotBandsArray, "deviceTimeData" => $deviceTimeData, 
                        "deviceLocation" => $devlocationname, "showinheader" => $showinheader, "currvalue" => $objpresentVal, "uom" => $uom, 'devDescription' => $devDescription];
                }
            }

            array_multisort(array_column($allChartArray, 'deviceType'),  SORT_ASC,
                array_column($allChartArray, 'deviceName'), SORT_ASC,
                $allChartArray);

            return json_encode([
                'recordStatus' => $recordStatus,
                'timeArr' => $timeArr, 
                'refreshTime' => $branchSettingsRefreshTime,            
                'allChartArray' => json_encode($allChartArray),
                'headerObjectNameList' => $headerObjectNameList,
                'binaryDeviceLocationList' => $binaryDeviceLocationList,
                'gaugeChartObjectNameList' => $gaugeChartObjectNameList,
                'valueChartObjectNameList' => $valueChartObjectNameList,
                'trendChartObjectNameList' => $trendChartObjectNameList,
                'weatherArray' => $weatherArray,
            ]);
        }
        else
        {
            $request->session()->flush();
            return redirect()->route('distechlogin')->with('unsuccess', 'Session Timeout');
        }
    }

    public function fetchinitialdata()
    {
        $brcompbranchid = session('branch_id');
        if (isset($brcompbranchid))
        {
            ini_set('max_execution_time', Config::get('constants.ExecutionTimeOut'));
            $recordCount = Config::get('constants.GetDashboardDistechRecords');
            $currentdate = Carbon::now('Asia/colombo');
            $currdt = date('Y-m-d H:i:s', strtotime($currentdate));
            $compbr = CompanyBranch::where('id', session('branch_id'))->first();
            $compbrSettings = BranchSettings::where('branch_id', session('branch_id'))
                ->where('field', Config::get('constants.BS_Refresh_Time'))->first();
            $branchSettingsRefreshTime = intval($compbrSettings->value) * 60000;
            $compbrSettings = BranchSettings::where('branch_id', session('branch_id'))
                ->where('field', Config::get('constants.BS_Change_Case'))->first();
            $branchSettingsChangeCase = $compbrSettings->value;
            $compbrDevLocList = session('compbrDevLocList');

            // Get Weather Report
            $weathertemp = 'NA';
            $weatherstatus = 'NA';
            $weatherArray = array();
            $loclatitude = session('latitude');
            $loclongitude = session('longitude');
            if (isset($loclatitude) && isset($loclongitude)) {
                $weatherapi = Config::get('constants.weather_api') . 'lon=' . $loclongitude . '&lat=' . $loclatitude . Config::get('constants.weather_api_query');
                try {
                    $client = new Client();
                    $response = $client->request('GET', $weatherapi, 
                        [
                            'http_errors' => false, // For Exception Handling
                            'timeout' => Config::get('constants.guzzleTimeOut'),
                            'verify' => false,
                        ]);
            
                    if ($response->getStatusCode() == 200) {                
                        $serviceResult = $response->getBody()->getContents();
                        $serviceResultjson = json_decode($serviceResult, true);
                        if (isset($serviceResultjson)) {
                            foreach($serviceResultjson as $srkey => $srvalue) {
                                if ($srkey == 'dataseries')
                                {
                                    foreach($srvalue as $srkey1 => $srvalue1) {                            
                                        $weathertemp = $srvalue1['temp2m']['min'] . '/' . $srvalue1['temp2m']['max'];
                                        $weatherstatus = $srvalue1['weather'];
                                        $weatherArray[] = ['temp' => $weathertemp, 'status' => $weatherstatus, 'uom' => Config::get('constants.uom_degree')];
                                    }
                                }
                            }
                        }
                        else {
                            $weathertemp = 'NA';
                            $weatherstatus = 'NA';
                            $weatherArray[] = ['temp' => $weathertemp, 'status' => $weatherstatus, 'uom' => Config::get('constants.uom_degree')];
                        }
                    }
                }
                catch (\Exception $e) {
                    $weathertemp = 'NA';
                    $weatherstatus = 'NA';
                    $weatherArray[] = ['temp' => $weathertemp, 'status' => $weatherstatus, 'uom' => ''];
                    \Log::info("Error:");
                    \Log::info($e);
                }

                // For current temperature
                // $weatherapi = Config::get('constants.weather_api') . 'lon=' . $loclongitude . '&lat=' . $loclatitude . Config::get('constants.weather_api_query_civil');
                // try {
                //     $client = new Client();
                //     $response = $client->request('GET', $weatherapi, 
                //         [
                //             'http_errors' => false, // For Exception Handling
                //             'timeout' => Config::get('constants.guzzleTimeOut'),
                //             'verify' => false,
                //         ]);
            
                //     if ($response->getStatusCode() == 200) {                
                //         $serviceResult = $response->getBody()->getContents();
                //         $serviceResultjson = json_decode($serviceResult, true);
                //         if (isset($serviceResultjson)) {
                //             foreach($serviceResultjson as $srkey => $srvalue) {
                //                 if ($srkey == 'dataseries')
                //                 {
                //                     foreach($srvalue as $srkey1 => $srvalue1) {                            
                //                         $weathertemp = $srvalue1['temp2m'];
                //                         $weatherstatus = $srvalue1['weather'];
                //                         $weatherArray[] = ['currenttemp' => $weathertemp, 'status' => $weatherstatus, 'uom' => Config::get('constants.uom_degree')];
                //                     }
                //                 }
                //             }
                //         }
                //         else {
                //             $weathertemp = 'NA';
                //             $weatherstatus = 'NA';
                //             $weatherArray[] = ['currenttemp' => $weathertemp, 'status' => $weatherstatus, 'uom' => Config::get('constants.uom_degree')];
                //         }
                //     }
                // }
                // catch (\Exception $e) {
                //     $weathertemp = 'NA';
                //     $weatherstatus = 'NA';
                //     $weatherArray[] = ['temp' => $weathertemp, 'status' => $weatherstatus, 'uom' => ''];
                //     \Log::info("Error:");
                //     \Log::info($e);
                // }
            }
            // Weather Report ends

            $timeList = DB::table(session('table_name'))
                ->distinct()
                ->where('company_id', session('company_id'))
                ->where('branch_id', session('branch_id'))
                ->where('status_date', $currdt)
                ->orderByDesc('status_time')
                ->take($recordCount)
                ->pluck('status_time');
            $timeList = $timeList->reverse();
            // $timeLastItem = $timeList->pop();
            $timeLastItem = $timeList->last();
            session(['timeLastItem' => $timeLastItem]);

            $recordStatus = 'OFFLINE';
            if (isset($timeLastItem))
                $recordStatus = 'ONLINE';
            
            $disdevList = DB::table(session('table_name'))
                ->where('company_id', session('company_id'))
                ->where('branch_id', session('branch_id'))
                ->where('status_date', $currdt)
                ->whereIn('status_time', $timeList)
                ->orderBy('object_type')
                ->orderBy('object_name')
                ->orderBy('status_time')
                ->orderBy('id')
                ->get();
            $timeList = json_decode(json_encode($timeList), true);
            $asnList = $disdevList->unique('asn_value');
                
            // $asnList = DB::table(session('table_name'))
            //     ->distinct()
            //     ->where('company_id', session('company_id'))
            //     ->where('branch_id', session('branch_id'))
            //     ->where('status_date', $currdt)
            //     ->whereIn('status_time', $timeList)
            //     ->pluck('asn_value');
            $chartCount = 0;        
            $headerObjectNameList = array();
            $gaugeChartObjectNameList = array();
            $valueChartObjectNameList = array();
            $trendChartObjectNameList = array();
            $binaryDeviceLocationList = array();
            $allChartArray = array();
            $timeArr = array();

            foreach ($timeList as $tm)
                $timeArr[] = date(Config::get('constants.XAxisTimeFormat'), strtotime($tm));
            
            foreach ($asnList as $asnkey => $asnvalue)
            {
            // foreach ($asnList as $asn) 
            // {
                $asn = $asnvalue->asn_value;
                $disdevtempList = $disdevList->where('asn_value', $asn)->pluck('present_value');
                $objectName = $disdevList->where('asn_value', $asn)->pluck('object_name');
                $disdevPresent = $disdevList->where('asn_value', $asn)->pluck('present_value');
                $objectType = $disdevList->where('asn_value', $asn)->pluck('object_type');
                $devlocname = $disdevList->where('asn_value', $asn)->pluck(['device_location']);
                $uom = '';
                $presentValWithUOM = '';
                $headerbgcolor = '';
                $showCountValue = '';

                foreach ($devlocname as $obj)
                    $devlocationname = $obj;
                foreach ($objectName as $obj)
                    $objname = $obj;
                foreach ($objectType as $obj)
                    $objtypename = $obj;
                foreach ($disdevPresent as $ddpresent)
                    $objpresentVal = $ddpresent;

                $brdevloc = $compbrDevLocList->where('device_name', $objname)->pluck(['plot_min']);
                $plotmin = '';
                foreach ($brdevloc as $obj)
                    $plotmin = $obj;

                $brdevloc = $compbrDevLocList->where('device_name', $objname)->pluck(['plot_max']);
                $plotmax = '';
                foreach ($brdevloc as $obj)
                    $plotmax = $obj;

                $brdevloc = $compbrDevLocList->where('device_name', $objname)->pluck(['plot_bands']);
                $plotbands = '';
                foreach ($brdevloc as $obj)
                    $plotbands = $obj;
                
                $brdevloc = $compbrDevLocList->where('device_name', $objname)->pluck(['show_in_header']);
                foreach ($brdevloc as $obj)
                    $showinheader = $obj;

                $brdevloc = $compbrDevLocList->where('device_name', $objname)->pluck(['device_description']);
                foreach ($brdevloc as $obj)
                    $devDescription = $obj;

                $brdevloc = $compbrDevLocList->where('device_name', $objname)->pluck(['device_uom']);
                foreach ($brdevloc as $obj)
                    $uom = $obj;

                $brdevloc = $compbrDevLocList->where('device_name', $objname)->pluck(['header_bg_color']);
                foreach ($brdevloc as $obj)
                    $headerbgcolor = $obj;

                $brdevloc = $compbrDevLocList->where('device_name', $objname)->pluck(['show_count_value']);
                foreach ($brdevloc as $obj)
                    $showCountValue = $obj;

                // if (strlen(floor($objpresentVal)) > 2)
                //     $objpresentVal = $objpresentVal / 1000;

                $plotBandsArray = array();
                if (!empty($plotbands)) {
                    $guageplotexploded = explode(';', $plotbands);
                    for ($i = 0; $i < count($guageplotexploded); $i += 1) {
                        $gaugebandexploded = explode(',', $guageplotexploded[$i]);
                        if (count($gaugebandexploded) > 2)
                            $plotBandsArray[] = ['from' => $gaugebandexploded[0], 'to' => $gaugebandexploded[1], 'color' => $gaugebandexploded[2]];
                    }
                }
                
                $presentValWithUOM = $objpresentVal;
                $actualname = str_replace("_", " ", $objname);
                $actualname = str_ireplace(" sensor", "", $actualname);
                $objtemp = $actualname;
                if ($branchSettingsChangeCase == Config::get('constants.BS_Change_Case_Y')) {
                    $actualname = ucwords($actualname);
                    $objtemp = ucwords($actualname);
                }

                $brdevlocclubbed = $compbrDevLocList->where('device_name', $objname)->pluck(['is_clubbed']);
                $brdevclubbed = '';
                foreach ($brdevlocclubbed as $obj)
                    $brdevclubbed = $obj;

                if ($brdevclubbed == '1') {
                    $explodeName = strpbrk($objtemp, "1234567890");
                    if ($explodeName != '')
                    {
                        $exploded = explode($explodeName, $actualname);
                        $clubbedName = trim($exploded[0], " ");
                    }
                    else
                        $clubbedName = $objtemp;
                }
                else
                    $clubbedName = $objtemp;
                $clubbedIndex = array_search($clubbedName, array_column($headerObjectNameList, "deviceName"));
                $binClubbedIndex = array_search($clubbedName, array_column($binaryDeviceLocationList, "deviceName"));

                if ($binClubbedIndex !== false) {
                    $binaryDeviceLocationList[$binClubbedIndex]["deviceLocation"] = $binaryDeviceLocationList[$binClubbedIndex]["deviceLocation"] . ',' . $devlocationname;
                    $binaryDeviceLocationList[$binClubbedIndex]["name"] = $binaryDeviceLocationList[$binClubbedIndex]["name"] . ',' . $objname;
                    $binaryDeviceLocationList[$binClubbedIndex]["count"] = intval($binaryDeviceLocationList[$binClubbedIndex]["count"]) + 1;

                    if ($objtypename == Config::get('constants.api_binary_input') || $objtypename == Config::get('constants.api_binary_value') || $objtypename == Config::get('constants.api_binary_output')) {
                        if (strtolower($objpresentVal) == 'active' || strtolower($objpresentVal) == 'true' || strtolower($objpresentVal) == 'on') {
                            $binaryDeviceLocationList[$binClubbedIndex]["activevalue"] = intval($binaryDeviceLocationList[$binClubbedIndex]["activevalue"]) + 1;
                            $binaryDeviceLocationList[$binClubbedIndex]["activeval"] = $binaryDeviceLocationList[$binClubbedIndex]["activeval"] . ',' . '1';
                        }
                        else
                            $binaryDeviceLocationList[$binClubbedIndex]["activeval"] = $binaryDeviceLocationList[$binClubbedIndex]["activeval"] . ',' . '0';
                    }
                }
                else
                {
                    if ($objtypename == Config::get('constants.api_binary_input') || $objtypename == Config::get('constants.api_binary_value') || $objtypename == Config::get('constants.api_binary_output')) {
                        if (strtolower($objpresentVal) == 'active' || strtolower($objpresentVal) == 'true' || strtolower($objpresentVal) == 'on')
                            $binaryDeviceLocationList[] = ["name" => $objname, "actualname" => $actualname, "deviceName" => $clubbedName, "deviceLocation" => $devlocationname, "count" => 1, "activevalue" => 1, "activeval" => '1'];
                        else
                            $binaryDeviceLocationList[] = ["name" => $objname, "actualname" => $actualname, "deviceName" => $clubbedName, "deviceLocation" => $devlocationname, "count" => 1, "activevalue" => 0, "activeval" => '0'];
                    }
                }

                // $uom = '';
                // if (strpos(strtolower($objtemp), "temp") || strpos(strtolower($objtemp), "old"))
                //     $uom = Config::get('constants.uom_degree');
                // elseif (strpos(strtolower($objtemp), "kwh") || strpos(strtolower($objtemp), "energy"))
                //     $uom = Config::get('constants.uom_kwh');
                // elseif (strpos(strtolower($objtemp), "kw") || strpos(strtolower($objtemp), "power"))
                //     $uom = Config::get('constants.uom_kw');
                $presentValWithUOM = number_format(floatval($objpresentVal), 2) . $uom;
                $activeval = 1;
                        
                // if ($clubbedIndex !== false) {
                //     // if (count($plotBandsArray) > 0) {
                //     //     if ($objpresentVal < $plotBandsArray[0]['from'] || $objpresentVal > $plotBandsArray[count($plotBandsArray) - 1]['to'])
                //     //         $activeval = 0;
                //     //     else if ($objpresentVal >= $plotBandsArray[0]['from'] && $objpresentVal <= $plotBandsArray[0]['to'])
                //     //         $activeval = 0;
                //     //     else if ($objpresentVal >= $plotBandsArray[count($plotBandsArray) - 1]['from'] && $objpresentVal <= $plotBandsArray[count($plotBandsArray) - 1]['to'])
                //     //         $activeval = 0;
                //     // }
                //     // else if ($plotmin != 0 || $plotmax != 0) {
                //     //     if ($objpresentVal > $plotmin || $objpresentVal > $plotmax)
                //     //         $activeval = 0;
                //     // }
                //     $headerObjectNameList[$clubbedIndex]["count"] = intval($headerObjectNameList[$clubbedIndex]["count"]) + 1;
                //     $headerObjectNameList[$clubbedIndex]["activevalue"] = intval($headerObjectNameList[$clubbedIndex]["activevalue"]) + $activeval;
                // }
                // else
                // {
                //     $brdevloc1 = $compbrDevLocList->where('device_name', $objname)->pluck(['show_in_header']);
                //     $brdevlocname1 = 0;
                //     foreach ($brdevloc1 as $obj1)
                //         $brdevlocname1 = $obj1;
                //     if ($brdevlocname1 == 1) {
                //         // if (count($plotBandsArray) > 0) {
                //         //     if ($objpresentVal < $plotBandsArray[0]['from'] || $objpresentVal > $plotBandsArray[count($plotBandsArray) - 1]['to'])
                //         //         $activeval = 0;
                //         //     else if ($objpresentVal >= $plotBandsArray[0]['from'] && $objpresentVal <= $plotBandsArray[0]['to'])
                //         //         $activeval = 0;
                //         //     else if ($objpresentVal >= $plotBandsArray[count($plotBandsArray) - 1]['from'] && $objpresentVal <= $plotBandsArray[count($plotBandsArray) - 1]['to'])
                //         //         $activeval = 0;
                //         // }
                //         // else if ($plotmin != 0 || $plotmax != 0) {
                //         //     if ($objpresentVal > $plotmin || $objpresentVal > $plotmax)
                //         //         $activeval = 0;
                //         // }
                //         headerObjectNameList[] = ["name" => $objname, "actualname" => $actualname, "deviceName" => $clubbedName, 
                //             "count" => 1, "activevalue" => $activeval, "presentvalue" => $presentValWithUOM, "type" => $objtypename,
                //             "plotmin" => $plotmin, "plotmax" => $plotmax, "currvalue" => $objpresentVal];
                //     }
                // }

                if ($clubbedIndex !== false) {
                    if (count($plotBandsArray) > 0) {
                        if ($objpresentVal < $plotBandsArray[0]['from'] || $objpresentVal > $plotBandsArray[count($plotBandsArray) - 1]['to'])
                            $activeval = 0;
                        // else if ($objpresentVal >= $plotBandsArray[0]['from'] && $objpresentVal <= $plotBandsArray[0]['to'])
                        //     $activeval = 0;
                        else if ($objpresentVal >= $plotBandsArray[count($plotBandsArray) - 1]['from'] && $objpresentVal <= $plotBandsArray[count($plotBandsArray) - 1]['to'])
                            $activeval = 0;
                    }
                    else if ($plotmin != 0 || $plotmax != 0) {
                        if ($objpresentVal > $plotmin || $objpresentVal > $plotmax)
                            $activeval = 0;
                    }
                    $headerObjectNameList[$clubbedIndex]["count"] = intval($headerObjectNameList[$clubbedIndex]["count"]) + 1;
                    $headerObjectNameList[$clubbedIndex]["activevalue"] = intval($headerObjectNameList[$clubbedIndex]["activevalue"]) + $activeval;
                }
                else
                {
                    $brdevloc1 = $compbrDevLocList->where('device_name', $objname)->pluck(['show_in_header']);
                    $brdevlocname1 = 0;
                    foreach ($brdevloc1 as $obj1)
                        $brdevlocname1 = $obj1;
                    if ($brdevlocname1 == 1) {
                        if (count($plotBandsArray) > 0) {
                            if ($objpresentVal < $plotBandsArray[0]['from'] || $objpresentVal > $plotBandsArray[count($plotBandsArray) - 1]['to'])
                                $activeval = 0;
                            // else if ($objpresentVal >= $plotBandsArray[0]['from'] && $objpresentVal <= $plotBandsArray[0]['to'])
                            //     $activeval = 0;
                            else if ($objpresentVal >= $plotBandsArray[count($plotBandsArray) - 1]['from'] && $objpresentVal <= $plotBandsArray[count($plotBandsArray) - 1]['to'])
                                $activeval = 0;
                        }
                        else if ($plotmin != 0 || $plotmax != 0) {
                            if ($objpresentVal > $plotmin || $objpresentVal > $plotmax)
                                $activeval = 0;
                        }

                        $headerObjectNameList[] = ["name" => $objname, "actualname" => $actualname, "deviceName" => $clubbedName, 
                            "count" => 1, "activevalue" => $activeval, "presentvalue" => $presentValWithUOM, "type" => $objtypename,
                            "plotmin" => $plotmin, "plotmax" => $plotmax, "currvalue" => $objpresentVal, "uom" => $uom, 'countValue' => $showCountValue, 'headerbgcolor' => $headerbgcolor];
                    }
                }

                $brdevloc = $compbrDevLocList->where('device_name', $objname)->where('chart_type', Config::get('constants.chart_gauge'))->pluck(['device_location']);
                $brdevlocname = '';
                foreach ($brdevloc as $obj)
                    $brdevlocname = $obj;
                if ($brdevlocname != '')
                {
                    $chartCount += 1;
                    $gaugeChartObjectNameList[] = ['actualname' => $actualname, 'name' => $objname, 'deviceLocation' => $brdevlocname, 'presentvalue' => 0];
                }

                $brdevloc = $compbrDevLocList->where('device_name', $objname)->where('chart_type', Config::get('constants.chart_value'))->pluck(['device_location']);
                $brdevlocname = '';
                foreach ($brdevloc as $obj)
                    $brdevlocname = $obj;
                if ($brdevlocname != '')
                    $valueChartObjectNameList[] = ['actualname' => $actualname, 'name' => $objname, 'deviceLocation' => $brdevlocname, 'presentvalue' => 0];

                $brdevloc = $compbrDevLocList->where('device_name', $objname)->where('chart_type', Config::get('constants.chart_trend'))->pluck(['device_location']);
                $brdevlocname = '';
                foreach ($brdevloc as $obj)
                    $brdevlocname = $obj;
                if ($brdevlocname != '')
                {
                    $chartCount += 1;
                    $valueChartObjectNameList[] = ['actualname' => $actualname, 'name' => $objname, 'deviceLocation' => $brdevlocname, 'presentvalue' => 0];
                    $trendChartObjectNameList[] = ['actualname' => $actualname, 'name' => $objname, 'deviceLocation' => $brdevlocname, 'presentvalue' => 0];
                }

                // For AllCharts Array
                $disdevtempList = $disdevList->where('asn_value', $asn);
                $deviceTimeData = array();
                $isnumericData = false;
                    
                foreach ($timeList as $tm) {
                    $disdevtimetempList = $disdevtempList->where('status_time', $tm)->pluck('present_value');
                    foreach ($disdevtimetempList as $ddev) {
                        if (is_numeric($ddev)) {
                            if (floatval($ddev) != 0)
                                $isnumericData = true;

                            // if (strlen(floor($ddev)) > 2)
                            //     $ddev = $ddev / 1000;
                            $roundedValue = substr($ddev, 0, ((strpos($ddev, '.') + 1) + 2));
                            $deviceTimeData[] = ["category" => date(Config::get('constants.XAxisTimeFormat'), strtotime($tm)), "devdata" => $ddev, "roundedData" => $roundedValue, "categoryVal" => date(Config::get('constants.XAxisTimeFormat'), strtotime($tm))];
                        }
                    }
                }

                if ($isnumericData) {
                    $brdevloc = $compbrDevLocList->where('device_name', $objname)->pluck(['chart_type']);
                    $charttype = '';
                    foreach ($brdevloc as $obj)
                        $charttype = $obj;
                        
                    $allChartArray[] = ['actualname' => $actualname, "objectName" => $objname, "deviceType" => $objtypename, "deviceName" => $objtemp, "presentValue" => $presentValWithUOM,
                        "charttype" => $charttype, "plotmin" => $plotmin, "plotmax" => $plotmax, "plotbands" => $plotBandsArray, "deviceTimeData" => $deviceTimeData, 
                        "deviceLocation" => $devlocationname, "showinheader" => $showinheader, "currvalue" => $objpresentVal, "uom" => $uom, 'devDescription' => $devDescription, 'headerbgcolor' => $headerbgcolor];
                }
            }

            array_multisort(array_column($allChartArray, 'deviceType'),  SORT_ASC,
                array_column($allChartArray, 'deviceName'), SORT_ASC,
                $allChartArray);

            return json_encode([
                'recordStatus' => $recordStatus,
                'timeArr' => $timeArr, 
                'refreshTime' => $branchSettingsRefreshTime,            
                'allChartArray' => json_encode($allChartArray),
                'headerObjectNameList' => $headerObjectNameList,
                'binaryDeviceLocationList' => $binaryDeviceLocationList,
                'gaugeChartObjectNameList' => $gaugeChartObjectNameList,
                'valueChartObjectNameList' => $valueChartObjectNameList,
                'trendChartObjectNameList' => $trendChartObjectNameList,
                'weatherArray' => $weatherArray,
            ]);
        }
        else
        {
            $request->session()->flush();
            return redirect()->route('distechlogin')->with('unsuccess', 'Session Timeout');
        }
    }

    public function fetchdata()
    {
        ini_set('max_execution_time', Config::get('constants.ExecutionTimeOut'));
        $recordCount = Config::get('constants.GetDashboardDistechRecords');
        $currentdate = Carbon::now('Asia/colombo');
        $currdt = date('Y-m-d H:i:s', strtotime($currentdate));
        $compbr = CompanyBranch::where('id', session('branch_id'))->first();
        $compbrDevLocList = session('compbrDevLocList');
        $compbrSettings = BranchSettings::where('branch_id', session('branch_id'))
            ->where('field', Config::get('constants.BS_Change_Case'))->first();
        $branchSettingsChangeCase = $compbrSettings->value;

        // Get Weather Report
        $weathertemp = 'Sorry';
        $weatherstatus = '';
        $weatherArray = array();
        // $loclatitude = session('latitude');
        // $loclongitude = session('longitude');
        // if (isset($loclatitude) && isset($loclongitude)) {
        //     $weatherapi = Config::get('constants.weather_api') . 'lon=' . $loclongitude . '&lat=' . $loclatitude . Config::get('constants.weather_api_query');
        //     $client = new Client();
        //     $response = $client->request('GET', $weatherapi, 
        //         [
        //             'http_errors' => false, // For Exception Handling
        //             'verify' => false,
        //         ]);
        //     if ($response->getStatusCode() == 200) {
        //         $serviceResult = $response->getBody()->getContents();
        //         $serviceResultjson = json_decode($serviceResult, true);
        //         foreach($serviceResultjson as $srkey => $srvalue) {
        //             if ($srkey == 'dataseries')
        //             {
        //                 foreach($srvalue as $srkey1 => $srvalue1) {                            
        //                     $weathertemp = $srvalue1['temp2m']['min'] . '/' . $srvalue1['temp2m']['max'];
        //                     $weatherstatus = $srvalue1['weather'];
        //                     $weatherArray[] = ['temp' => $weathertemp, 'status' => $weatherstatus];
        //                 }
        //             }
        //         }
        //     }
        // }
        // // Weather Report ends

        $timeLastItemtemp = session('timeLastItem');\Log::info('session time-fetchdata: ' . $timeLastItemtemp);
        $recordStatus = 'OFFLINE';            
        if (isset($timeLastItemtemp))
        {
            $recordStatus = 'ONLINE';
            $timeList = DB::table(session('table_name'))
                ->distinct()
                ->where('company_id', session('company_id'))
                ->where('branch_id', session('branch_id'))
                ->where('status_time', '>', session('timeLastItem'))
                ->where('status_date', $currdt)
                ->orderByDesc('status_time')
                ->pluck('status_time');
            $timeList = $timeList->reverse();
            $timeLastItem = $timeList->last();
            session(['timeLastItem' => $timeLastItem]);

            $disdevList = DB::table(session('table_name'))
                ->where('company_id', session('company_id'))
                ->where('branch_id', session('branch_id'))
                ->where('status_date', $currdt)
                ->whereIn('status_time', $timeList)
                ->orderBy('object_type')
                ->orderBy('object_name')
                ->orderBy('status_time')
                ->orderBy('id')
                ->get();
            $asnList = $disdevList->unique('asn_value');

            // $asnList = DB::table(session('table_name'))
            //     ->distinct()
            //     ->where('company_id', session('company_id'))
            //     ->where('branch_id', session('branch_id'))
            //     ->where('status_date', $currdt)
            //     ->whereIn('status_time', $timeList)
            //     ->pluck('asn_value');
            $timeList = json_decode(json_encode($timeList), true);
            $timeArr = array();
            $headerObjectNameList = array();
            $gaugeChartObjectNameList = array();
            $valueChartObjectNameList = array();
            $trendChartObjectNameList = array();
            $binaryDeviceLocationList = array();
            $allChartArray = array();
            $timeArr = array();
            $XaxisTime = $currdt;
            foreach ($timeList as $tm) {
                $timeArr[] = date(Config::get('constants.XAxisTimeFormat'), strtotime($tm));
                $XaxisTime = $tm;
            }
            // foreach ($asnList as $asn) 
            // {
            foreach ($asnList as $asnkey => $asnvalue)
            {
                $asn = $asnvalue->asn_value;
                $disdevtempList = $disdevList->where('asn_value', $asn)->pluck('present_value');
                $objectName = $disdevList->where('asn_value', $asn)->pluck('object_name');
                $disdevPresent = $disdevList->where('asn_value', $asn)->pluck('present_value');
                $objectType = $disdevList->where('asn_value', $asn)->pluck('object_type');
                $devlocname = $disdevList->where('asn_value', $asn)->pluck(['device_location']);
                $uom = '';
                $presentValWithUOM = '';
                $headerbgcolor = '';
                $showCountValue = '';

                foreach ($devlocname as $obj)
                    $devlocationname = $obj;
                foreach ($objectName as $obj)
                    $objname = $obj;
                foreach ($objectType as $obj)
                    $objtypename = $obj;
                foreach ($disdevPresent as $ddpresent)
                    $objpresentVal = $ddpresent;

                $brdevloc = $compbrDevLocList->where('device_name', $objname)->pluck(['plot_min']);
                $plotmin = '';
                foreach ($brdevloc as $obj)
                    $plotmin = $obj;

                $brdevloc = $compbrDevLocList->where('device_name', $objname)->pluck(['plot_max']);
                $plotmax = '';
                foreach ($brdevloc as $obj)
                    $plotmax = $obj;
                
                $brdevloc = $compbrDevLocList->where('device_name', $objname)->pluck(['show_in_header']);
                foreach ($brdevloc as $obj)
                    $showinheader = $obj;

                // if (strlen(floor($objpresentVal)) > 2)
                //     $objpresentVal = $objpresentVal / 1000;

                $brdevloc = $compbrDevLocList->where('device_name', $objname)->pluck(['plot_bands']);
                $plotbands = '';
                foreach ($brdevloc as $obj)
                    $plotbands = $obj;

                $brdevloc = $compbrDevLocList->where('device_name', $objname)->pluck(['device_uom']);
                foreach ($brdevloc as $obj)
                    $uom = $obj;

                $brdevloc = $compbrDevLocList->where('device_name', $objname)->pluck(['header_bg_color']);
                foreach ($brdevloc as $obj)
                    $headerbgcolor = $obj;

                $brdevloc = $compbrDevLocList->where('device_name', $objname)->pluck(['show_count_value']);
                foreach ($brdevloc as $obj)
                    $showCountValue = $obj;

                $plotBandsArray = array();
                if (!empty($plotbands)) {
                    $guageplotexploded = explode(';', $plotbands);
                    for ($i = 0; $i < count($guageplotexploded); $i += 1) {
                        $gaugebandexploded = explode(',', $guageplotexploded[$i]);
                        if (count($gaugebandexploded) > 2)
                            $plotBandsArray[] = ['from' => $gaugebandexploded[0], 'to' => $gaugebandexploded[1], 'color' => $gaugebandexploded[2]];
                    }
                }
                
                $presentValWithUOM = $objpresentVal;
                $actualname = str_replace("_", " ", $objname);
                $actualname = str_ireplace(" sensor", "", $actualname);
                $objtemp = $actualname;
                if ($branchSettingsChangeCase == Config::get('constants.BS_Change_Case_Y')) {
                    $actualname = ucwords($actualname);
                    $objtemp = ucwords($actualname);
                }

                $brdevlocclubbed = $compbrDevLocList->where('device_name', $objname)->pluck(['is_clubbed']);
                $brdevclubbed = '';
                foreach ($brdevlocclubbed as $obj)
                    $brdevclubbed = $obj;

                if ($brdevclubbed == '1') {
                    $explodeName = strpbrk($objtemp, "1234567890");
                    if ($explodeName != '')
                    {
                        $exploded = explode($explodeName, $actualname);
                        $clubbedName = trim($exploded[0], " ");
                    }
                    else
                        $clubbedName = $objtemp;
                }
                else
                    $clubbedName = $objtemp;

                $clubbedIndex = array_search($clubbedName, array_column($headerObjectNameList, "deviceName"));
                $binClubbedIndex = array_search($clubbedName, array_column($binaryDeviceLocationList, "deviceName"));

                if ($binClubbedIndex !== false) {
                    $binaryDeviceLocationList[$binClubbedIndex]["deviceLocation"] = $binaryDeviceLocationList[$binClubbedIndex]["deviceLocation"] . ',' . $devlocationname;
                    $binaryDeviceLocationList[$binClubbedIndex]["name"] = $binaryDeviceLocationList[$binClubbedIndex]["name"] . ',' . $objname;
                    $binaryDeviceLocationList[$binClubbedIndex]["count"] = intval($binaryDeviceLocationList[$binClubbedIndex]["count"]) + 1;

                    if ($objtypename == Config::get('constants.api_binary_input') || $objtypename == Config::get('constants.api_binary_value') || $objtypename == Config::get('constants.api_binary_output')) {
                        if (strtolower($objpresentVal) == 'active' || strtolower($objpresentVal) == 'true' || strtolower($objpresentVal) == 'on') {
                            $binaryDeviceLocationList[$binClubbedIndex]["activevalue"] = intval($binaryDeviceLocationList[$binClubbedIndex]["activevalue"]) + 1;
                            $binaryDeviceLocationList[$binClubbedIndex]["activeval"] = $binaryDeviceLocationList[$binClubbedIndex]["activeval"] . ',' . '1';
                        }
                        else
                            $binaryDeviceLocationList[$binClubbedIndex]["activeval"] = $binaryDeviceLocationList[$binClubbedIndex]["activeval"] . ',' . '0';
                    }
                }
                else
                {
                    if ($objtypename == Config::get('constants.api_binary_input') || $objtypename == Config::get('constants.api_binary_value') || $objtypename == Config::get('constants.api_binary_output')) {
                        if (strtolower($objpresentVal) == 'active' || strtolower($objpresentVal) == 'true' || strtolower($objpresentVal) == 'on')
                            $binaryDeviceLocationList[] = ["name" => $objname, "actualname" => $actualname, "deviceName" => $clubbedName, "deviceLocation" => $devlocationname, "count" => 1, "activevalue" => 1, "activeval" => '1'];
                        else
                            $binaryDeviceLocationList[] = ["name" => $objname, "actualname" => $actualname, "deviceName" => $clubbedName, "deviceLocation" => $devlocationname, "count" => 1, "activevalue" => 0, "activeval" => '0'];
                    }
                }

                // $uom = '';
                // if (strpos(strtolower($objtemp), "temp") || strpos(strtolower($objtemp), "old"))
                //     $uom = Config::get('constants.uom_degree');
                // elseif (strpos(strtolower($objtemp), "kwh") || strpos(strtolower($objtemp), "energy"))
                //     $uom = Config::get('constants.uom_kwh');
                // elseif (strpos(strtolower($objtemp), "kw") || strpos(strtolower($objtemp), "power"))
                //     $uom = Config::get('constants.uom_kw');
                $presentValWithUOM = number_format(floatval($objpresentVal), 2) . $uom;

                $activeval = 1;
                if ($clubbedIndex !== false) {
                    if (count($plotBandsArray) > 0) {
                        if ($objpresentVal < $plotBandsArray[0]['from'] || $objpresentVal > $plotBandsArray[count($plotBandsArray) - 1]['to'])
                            $activeval = 0;
                        // else if ($objpresentVal >= $plotBandsArray[0]['from'] && $objpresentVal <= $plotBandsArray[0]['to'])
                        //     $activeval = 0;
                        else if ($objpresentVal >= $plotBandsArray[count($plotBandsArray) - 1]['from'] && $objpresentVal <= $plotBandsArray[count($plotBandsArray) - 1]['to'])
                            $activeval = 0;
                    }
                    else if ($plotmin != 0 || $plotmax != 0) {
                        if ($objpresentVal < $plotmin || $objpresentVal > $plotmax)
                            $activeval = 0;
                    }
                    $headerObjectNameList[$clubbedIndex]["count"] = intval($headerObjectNameList[$clubbedIndex]["count"]) + 1;
                    $headerObjectNameList[$clubbedIndex]["activevalue"] = intval($headerObjectNameList[$clubbedIndex]["activevalue"]) + $activeval;
                }
                else
                {
                    $brdevloc1 = $compbrDevLocList->where('device_name', $objname)->pluck(['show_in_header']);
                    $brdevlocname1 = 0;
                    foreach ($brdevloc1 as $obj1)
                        $brdevlocname1 = $obj1;
                    if ($brdevlocname1 == 1) {
                        if (count($plotBandsArray) > 0) {
                            if ($objpresentVal < $plotBandsArray[0]['from'] || $objpresentVal > $plotBandsArray[count($plotBandsArray) - 1]['to'])
                                $activeval = 0;
                            // else if ($objpresentVal >= $plotBandsArray[0]['from'] && $objpresentVal <= $plotBandsArray[0]['to'])
                            //     $activeval = 0;
                            else if ($objpresentVal >= $plotBandsArray[count($plotBandsArray) - 1]['from'] && $objpresentVal <= $plotBandsArray[count($plotBandsArray) - 1]['to'])
                                $activeval = 0;
                        }
                        else if ($plotmin != 0 || $plotmax != 0) {
                            if ($objpresentVal < $plotmin || $objpresentVal > $plotmax)
                                $activeval = 0;
                        }                        
                        $headerObjectNameList[] = ["name" => $objname, "actualname" => $actualname, "deviceName" => $clubbedName, 
                            "count" => 1, "activevalue" => $activeval, "presentvalue" => $presentValWithUOM, "type" => $objtypename,
                            "plotmin" => $plotmin, "plotmax" => $plotmax, "currvalue" => $objpresentVal, "uom" => $uom, 'countValue' => $showCountValue, 'headerbgcolor' => $headerbgcolor];
                    }
                }

                $brdevloc = $compbrDevLocList->where('device_name', $objname)->where('chart_type', Config::get('constants.chart_gauge'))->pluck(['device_location']);
                $brdevlocname = '';
                foreach ($brdevloc as $obj)
                    $brdevlocname = $obj;
                if ($brdevlocname != '')
                {
                    $gaugeChartObjectNameList[] = ['actualname' => $actualname, 'name' => $objname, 'deviceLocation' => $brdevlocname, 'presentvalue' => 0];
                }

                $brdevloc = $compbrDevLocList->where('device_name', $objname)->where('chart_type', Config::get('constants.chart_value'))->pluck(['device_location']);
                $brdevlocname = '';
                foreach ($brdevloc as $obj)
                    $brdevlocname = $obj;
                if ($brdevlocname != '')
                    $valueChartObjectNameList[] = ['actualname' => $actualname, 'name' => $objname, 'deviceLocation' => $brdevlocname, 'presentvalue' => 0];

                $brdevloc = $compbrDevLocList->where('device_name', $objname)->where('chart_type', Config::get('constants.chart_trend'))->pluck(['device_location']);
                $brdevlocname = '';
                foreach ($brdevloc as $obj)
                    $brdevlocname = $obj;
                if ($brdevlocname != '')
                {
                    $valueChartObjectNameList[] = ['actualname' => $actualname, 'name' => $objname, 'deviceLocation' => $brdevlocname, 'presentvalue' => 0];
                    $trendChartObjectNameList[] = ['actualname' => $actualname, 'name' => $objname, 'deviceLocation' => $brdevlocname, 'presentvalue' => 0];
                }

                // For AllCharts Array
                $brdevloc = $compbrDevLocList->where('device_name', $objname)->pluck(['chart_type']);
                $charttype = '';
                foreach ($brdevloc as $obj)
                    $charttype = $obj;

                $roundedValue = substr($objpresentVal, 0, ((strpos($objpresentVal, '.') + 1) + 2));
                $allChartArray[] = ['actualname' => $actualname, "objectName" => $objname, "deviceType" => $objtypename, "deviceName" => $objtemp, "presentValue" => $presentValWithUOM,
                    "charttype" => $charttype, "plotmin" => $plotmin, "plotmax" => $plotmax, "plotbands" => $plotBandsArray, "category" => date(Config::get('constants.XAxisTimeFormat'), strtotime($XaxisTime)),
                    "deviceLocation" => $devlocationname, "showinheader" => $showinheader, "currvalue" => $objpresentVal, "devdata" => $objpresentVal, "roundedData" => $roundedValue, "uom" => $uom, 
                    'countValue' => $showCountValue, 'headerbgcolor' => $headerbgcolor];
            }
            return json_encode([
                'recordStatus' => $recordStatus,
                'timeArr' => $timeArr, 
                'allChartArray' => json_encode($allChartArray),
                'headerObjectNameList' => $headerObjectNameList,
                'binaryDeviceLocationList' => $binaryDeviceLocationList,
                'gaugeChartObjectNameList' => $gaugeChartObjectNameList,
                'valueChartObjectNameList' => $valueChartObjectNameList,
                'trendChartObjectNameList' => $trendChartObjectNameList,
                'weatherArray' => $weatherArray,
            ]);
        }
        else 
            return json_encode(['recordStatus' => $recordStatus]);
        
    }

    public function toggleAC(string $deviceid, $devicevalue)
    {
        $deviceidexploded = substr($deviceid, strpos($deviceid, "_") + 1);
        $compbrDevLocList = session('compbrDevLocList');
        $brdevloc = $compbrDevLocList->where('device_name', $deviceidexploded)->pluck(['device_id']);
        $devid = '';
        foreach ($brdevloc as $obj)
            $devid = $obj;

        $brdevloc = $compbrDevLocList->where('device_name', $deviceidexploded)->pluck(['device_type']);
        $devtype = '';
        foreach ($brdevloc as $obj)
            $devtype = $obj;

        $distechurl = session('distech_deviceip') . '/api/rest/v1/protocols/bacnet/local/objects/' . $devtype . '/' . $devid . '/properties/present-value';
        if ($devicevalue == 'true')
            $devicevalue = 'Active';
        else if ($devicevalue == 'false')
            $devicevalue = 'Inactive';

        $client = new Client();
        $restformat = "json";
        $response = $client->request('POST', $distechurl, 
            [
                'verify' => false,
                'auth' => [session('distech_username'), session('distech_password')],
                'headers' => ['Content-Type' => 'application/json'],
                'json' => array('value' => $devicevalue)
            ]);
        $serviceResult = $response->getBody()->getContents();
        $serviceResultjson = json_decode($serviceResult, true);Log::info($serviceResultjson);
        if (isset($serviceResultjson))
            return json_encode(['Status' => $serviceResultjson]);
        else
            return json_encode(['Status' => 'Successfully ' . $deviceidexploded . ' ' . $devicevalue]);
    }

    public function viewtraffic()
    {
        if (is_null(session('user_id'))) return redirect()->route('logout');

        ini_set('max_execution_time', Config::get('constants.ExecutionTimeOut'));
        $recordCount = Config::get('constants.GetDistechRecords');        
        $compbrDevLocList = session('compbrDevLocList');

        $googlemapList = BranchSettings::where('branch_id', session('branch_id'))
            ->where('field', Config::get('constants.BS_Google_Maps'))->get();
        
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
        $compbrSettings = BranchSettings::where('branch_id', session('branch_id'))
            ->where('field', Config::get('constants.BS_Change_Case'))->first();
        $branchSettingsChangeCase = $compbrSettings->value;
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
            $actualname = str_ireplace(" sensor", "", $actualname);
            if ($branchSettingsChangeCase == Config::get('constants.BS_Change_Case_Y'))
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
            $objtemp = $actualname;
            if ($branchSettingsChangeCase == Config::get('constants.BS_Change_Case_Y'))
                $objtemp = ucwords($actualname);

            $brdevnamehdr = $compbrDevLocList->where('device_name', $objname)->pluck(['device_name_header']);
            $brdevnameheader = '';
            foreach ($brdevnamehdr as $obj)
                $brdevnameheader = $obj;

            $brdevloc = $compbrDevLocList->where('device_name', $objname)->pluck(['chart_type']);
            $brdevcharttype = '';
            foreach ($brdevloc as $obj)
                $brdevcharttype = $obj;
            
            $brdevlocclubbed = $compbrDevLocList->where('device_name', $objname)->pluck(['is_clubbed']);
            $brdevclubbed = '';
            foreach ($brdevlocclubbed as $obj)
                $brdevclubbed = $obj;

            $brdevlocsort = $compbrDevLocList->where('device_name', $objname)->pluck(['sort_order']);
            $brdevsort = 0;
            foreach ($brdevlocsort as $obj)
                $brdevsort = $obj;

            if ($brdevclubbed == '1') {
                $explodeName = strpbrk($brdevnameheader, "1234567890");
                if ($explodeName != '')
                {
                    $exploded = explode($explodeName, $brdevnameheader);
                    $clubbedName = trim($exploded[0], " ");
                }
                else
                    $clubbedName = $brdevnameheader;
            }
            else
                $clubbedName = $brdevnameheader;
            
            $clubbedName = str_ireplace(" sensor", "", $clubbedName);
            if ($branchSettingsChangeCase == Config::get('constants.BS_Change_Case_Y'))
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
                        $headerObjectNameList[] = ["name" => $objname, "actualname" => $actualname, "deviceName" => $clubbedName, "count" => 1,
                            "chartType" => strtolower($brdevcharttype), 'sort_order' => $brdevsort]; 
                    }
                // }
            }

            $gaugedeviceName = $compbrDevLocList->where('device_name', $objname)->where('chart_type', Config::get('constants.chart_gauge'))->pluck(['device_name']);
            $brgaugedevname = '';
            foreach ($gaugedeviceName as $obj)
                $brgaugedevname = $obj;

            $brdevloc = $compbrDevLocList->where('device_name', $objname)->where('chart_type', Config::get('constants.chart_gauge'))->pluck(['device_location']);
            $brdevlocname = '';
            foreach ($brdevloc as $obj)
                $brdevlocname = $obj;

            $brdevloc = $compbrDevLocList->where('device_name', $objname)->pluck(['img_src']);
            $brimgsrc = '';
            foreach ($brdevloc as $obj)
                $brimgsrc = $obj;

            if ($brgaugedevname != '')
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

        array_multisort(array_column($headerObjectNameList, 'sort_order'),  SORT_ASC,
            $headerObjectNameList);

        return view('traffic', [
            'refreshTime' => $branchSettingsRefreshTime,
            'chartCount' => $chartCount,
            'headerObjectNameList' => $headerObjectNameList,
            'binaryDeviceLocationList' => $binaryDeviceLocationList,
            'gaugeChartObjectNameList' => $gaugeChartObjectNameList,
            'valueChartObjectNameList' => $valueChartObjectNameList,
            'trendChartObjectNameList' => $trendChartObjectNameList,
            'googlemapList' => $googlemapList,
        ]);
    }

    public function viewkspcb()
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
        $compbrSettings = BranchSettings::where('branch_id', session('branch_id'))
            ->where('field', Config::get('constants.BS_Change_Case'))->first();
        $branchSettingsChangeCase = $compbrSettings->value;
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
            $actualname = str_ireplace(" sensor", "", $actualname);
            if ($branchSettingsChangeCase == Config::get('constants.BS_Change_Case_Y'))
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
            $objtemp = $actualname;
            if ($branchSettingsChangeCase == Config::get('constants.BS_Change_Case_Y'))
                $objtemp = ucwords($actualname);

            $brdevnamehdr = $compbrDevLocList->where('device_name', $objname)->pluck(['device_name_header']);
            $brdevnameheader = '';
            foreach ($brdevnamehdr as $obj)
                $brdevnameheader = $obj;

            $brdevloc = $compbrDevLocList->where('device_name', $objname)->pluck(['chart_type']);
            $brdevcharttype = '';
            foreach ($brdevloc as $obj)
                $brdevcharttype = $obj;

            $brdevlocclubbed = $compbrDevLocList->where('device_name', $objname)->pluck(['is_clubbed']);
            $brdevclubbed = '';
            foreach ($brdevlocclubbed as $obj)
                $brdevclubbed = $obj;

            $brdevlocsort = $compbrDevLocList->where('device_name', $objname)->pluck(['sort_order']);
            $brdevsort = 0;
            foreach ($brdevlocsort as $obj)
                $brdevsort = $obj;

            if ($brdevclubbed == '1') {
                $explodeName = strpbrk($brdevnameheader, "1234567890");
                if ($explodeName != '')
                {
                    $exploded = explode($explodeName, $brdevnameheader);
                    $clubbedName = trim($exploded[0], " ");
                }
                else
                    $clubbedName = $brdevnameheader;
            }
            else
                $clubbedName = $brdevnameheader;
            
            $clubbedName = str_ireplace(" sensor", "", $clubbedName);
            if ($branchSettingsChangeCase == Config::get('constants.BS_Change_Case_Y'))
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
                        $headerObjectNameList[] = ["name" => $objname, "actualname" => $actualname, "deviceName" => $clubbedName, "count" => 1,
                            "chartType" => strtolower($brdevcharttype), 'sort_order' => $brdevsort]; 
                    }
                // }
            }

            $gaugedeviceName = $compbrDevLocList->where('device_name', $objname)->where('chart_type', Config::get('constants.chart_gauge'))->pluck(['device_name']);
            $brgaugedevname = '';
            foreach ($gaugedeviceName as $obj)
                $brgaugedevname = $obj;

            $brdevloc = $compbrDevLocList->where('device_name', $objname)->where('chart_type', Config::get('constants.chart_gauge'))->pluck(['device_location']);
            $brdevlocname = '';
            foreach ($brdevloc as $obj)
                $brdevlocname = $obj;

            $brdevloc = $compbrDevLocList->where('device_name', $objname)->pluck(['img_src']);
            $brimgsrc = '';
            foreach ($brdevloc as $obj)
                $brimgsrc = $obj;

            if ($brgaugedevname != '')
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

        array_multisort(array_column($headerObjectNameList, 'sort_order'),  SORT_ASC,
            $headerObjectNameList);

        return view('mkspcb', [
            'refreshTime' => $branchSettingsRefreshTime,
            'chartCount' => $chartCount,
            'headerObjectNameList' => $headerObjectNameList,
            'binaryDeviceLocationList' => $binaryDeviceLocationList,
            'gaugeChartObjectNameList' => $gaugeChartObjectNameList,
            'valueChartObjectNameList' => $valueChartObjectNameList,
            'trendChartObjectNameList' => $trendChartObjectNameList,
        ]);
    }

    public function viewcontactus()
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
        $compbrSettings = BranchSettings::where('branch_id', session('branch_id'))
            ->where('field', Config::get('constants.BS_Change_Case'))->first();
        $branchSettingsChangeCase = $compbrSettings->value;
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
            $actualname = str_ireplace(" sensor", "", $actualname);
            if ($branchSettingsChangeCase == Config::get('constants.BS_Change_Case_Y'))
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
            $objtemp = $actualname;
            if ($branchSettingsChangeCase == Config::get('constants.BS_Change_Case_Y'))
                $objtemp = ucwords($actualname);

            $brdevnamehdr = $compbrDevLocList->where('device_name', $objname)->pluck(['device_name_header']);
            $brdevnameheader = '';
            foreach ($brdevnamehdr as $obj)
                $brdevnameheader = $obj;

            $brdevloc = $compbrDevLocList->where('device_name', $objname)->pluck(['chart_type']);
            $brdevcharttype = '';
            foreach ($brdevloc as $obj)
                $brdevcharttype = $obj;
            
            $brdevlocclubbed = $compbrDevLocList->where('device_name', $objname)->pluck(['is_clubbed']);
            $brdevclubbed = '';
            foreach ($brdevlocclubbed as $obj)
                $brdevclubbed = $obj;

            $brdevlocsort = $compbrDevLocList->where('device_name', $objname)->pluck(['sort_order']);
            $brdevsort = 0;
            foreach ($brdevlocsort as $obj)
                $brdevsort = $obj;

            if ($brdevclubbed == '1') {
                $explodeName = strpbrk($brdevnameheader, "1234567890");
                if ($explodeName != '')
                {
                    $exploded = explode($explodeName, $brdevnameheader);
                    $clubbedName = trim($exploded[0], " ");
                }
                else
                    $clubbedName = $brdevnameheader;
            }
            else
                $clubbedName = $brdevnameheader;
            
            $clubbedName = str_ireplace(" sensor", "", $clubbedName);
            if ($branchSettingsChangeCase == Config::get('constants.BS_Change_Case_Y'))
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
                        $headerObjectNameList[] = ["name" => $objname, "actualname" => $actualname, "deviceName" => $clubbedName, "count" => 1,
                            "chartType" => strtolower($brdevcharttype), 'sort_order' => $brdevsort]; 
                    }
                // }
            }

            $gaugedeviceName = $compbrDevLocList->where('device_name', $objname)->where('chart_type', Config::get('constants.chart_gauge'))->pluck(['device_name']);
            $brgaugedevname = '';
            foreach ($gaugedeviceName as $obj)
                $brgaugedevname = $obj;

            $brdevloc = $compbrDevLocList->where('device_name', $objname)->where('chart_type', Config::get('constants.chart_gauge'))->pluck(['device_location']);
            $brdevlocname = '';
            foreach ($brdevloc as $obj)
                $brdevlocname = $obj;

            $brdevloc = $compbrDevLocList->where('device_name', $objname)->pluck(['img_src']);
            $brimgsrc = '';
            foreach ($brdevloc as $obj)
                $brimgsrc = $obj;

            if ($gaugedeviceName != '')
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

        array_multisort(array_column($headerObjectNameList, 'sort_order'),  SORT_ASC,
            $headerObjectNameList);

        return view('contactus', [
            'refreshTime' => $branchSettingsRefreshTime,
            'chartCount' => $chartCount,
            'headerObjectNameList' => $headerObjectNameList,
            'binaryDeviceLocationList' => $binaryDeviceLocationList,
            'gaugeChartObjectNameList' => $gaugeChartObjectNameList,
            'valueChartObjectNameList' => $valueChartObjectNameList,
            'trendChartObjectNameList' => $trendChartObjectNameList,
        ]);
    }

    public function exportCSV(string $devicename)
    {
        $devname = str_replace("rpt_", "", $devicename);
        $fileName = 'dashboard_' . str_replace(" ", "", $devname) . '.csv';
        $currentdate = Carbon::now('Asia/colombo');
        $currdt = date('Y-m-d', strtotime($currentdate));
        $compbrDevLocList = session('compbrDevLocList');
        $brdevloc = $compbrDevLocList->where('device_name', $devname)->pluck(['trends_calculation']);
        $trendscalc = '';
        foreach ($brdevloc as $obj1)
            $trendscalc = $obj1;

        $dataList = DB::table('trends_device_status')
            ->select('status_hour', 'object_type', 'object_name', 'device_uom', 
                DB::raw("TRUNC(AVG(present_value::numeric), 2) AS average"), 
                DB::raw("TRUNC(SUM(present_value::numeric), 2) AS total"))                
            ->where('status_date', $currdt)
            ->where('branch_id', session('branch_id'))
            ->where('object_name', $devname)
            ->groupBy('status_hour', 'object_type', 'object_name', 'device_uom')
            ->orderByRaw("status_hour, object_name")->get();
        // $dataList = DB::select('select distinct tds.status_hour, tds.object_type, tds.object_name, ' .
        //             ' TRUNC(AVG(tds.present_value::numeric), 2) AS average, ' .
        //             ' TRUNC(SUM(tds.present_value::numeric), 2) AS total, tds.device_uom ' .
        //             ' FROM trends_device_status tds' .
        //             // ' INNER JOIN branch_device_location bdl ON bdl.branch_id = tds.branch_id ' .
        //             // ' AND bdl.device_name::text = tds.object_name::text ' .
        //             // ' WHERE bdl.show_in_trends = 1 AND tds.status_date = \'' . $currdt . '\'' .
        //             ' WHERE tds.status_date = \'' . $currdt . '\' ' .
        //             ' AND tds.branch_id = ' . session('branch_id') .
        //             ' AND tds.object_name = \'' . $devname . '\' ' .
        //             ' GROUP BY tds.status_hour, tds.object_type, tds.object_name, tds.device_uom ' .
        //             ' ORDER BY tds.status_hour, tds.object_name');
        $deviceNamewithUOM = '';
        foreach ($dataList as $data) {
            $deviceNamewithUOM = str_replace("_", " ", $devname) . ' (' . trim($data->device_uom) . ')';
            break;
        }
        $headers = array(
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        );

        $isTotalColumn = false;
        if ($trendscalc == Config::get('constants.TrendsCalculation'))
            $columns = array('TIME', 'AVERAGE');
        else {
            $columns = array('TIME', 'AVERAGE', 'TOTAL');
            $isTotalColumn = true;
        }

        $strposition = 0;
        $callback = function() use($dataList, $columns, $currdt, $deviceNamewithUOM, $isTotalColumn) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Today\'s Report - ' . $currdt]);
            fputcsv($file, [$deviceNamewithUOM]);
            fputcsv($file, $columns);

            foreach ($dataList as $data) {
                $strposition = strpos($data->status_hour, " ");
                $row['TIME']  = substr($data->status_hour, $strposition + 1);
                $row['AVERAGE']    = $data->average;

                if ($isTotalColumn) {
                    $row['TOTAL']    = $data->total;
                    fputcsv($file, array($row['TIME'], $row['AVERAGE'], $row['TOTAL']));
                }
                else {
                    fputcsv($file, array($row['TIME'], $row['AVERAGE']));
                }
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
