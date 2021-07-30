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
use App\ResourceLookupMaster;
// use App\RoleUser;
use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Config;
use Illuminate\Support\Facades\Log;

class MKSPCBController extends Controller
{
    public function create()
    {
        if (is_null(session('user_id'))) return redirect()->route('logout');
        
        $resourceMasterList = ResourceLookupMaster::where('is_active', 1)
            ->where('company_id', session('company_id'))
            ->where('branch_id', session('branch_id'))
            ->orderBy('resource_no')
            ->orderBy('sort_order')
            ->get();
        $resourceNameList = $resourceMasterList->unique('resource_name');
        $resourceList = array();

        foreach ($resourceNameList as $resourcekey => $resourcevalue)
        {
            $resrc = $resourcevalue->resource_name;
            $resrcActList = $resourceMasterList->where('resource_name', $resrc)->pluck(['resource_act_year']);
            // $resrcGrpList = $resourceMasterList->where('resource_name', $resrc)->pluck(['resource_group']);
            $resrcGrpList = $resourceMasterList->where('resource_name', $resrc)->unique('resource_group');
            $resrcNoList = $resourceMasterList->where('resource_name', $resrc)->pluck(['resource_no']);

            foreach ($resrcActList as $obj)
                $resrcAct = $obj;
            foreach ($resrcNoList as $obj)
                $resrcNo = $obj;

            $resourceGroupList = array();
            // foreach ($resrcGrpList as $obj) {
            foreach ($resrcGrpList as $resrcGrpkey => $resrcGrpvalue) {
                $resrcGrp = $resrcGrpvalue->resource_group;
                $resrcGrpNoList = $resourceMasterList->where('resource_name', $resrc)->pluck(['resource_group_no']);
                $resrcItemList = $resourceMasterList->where('resource_name', $resrc)
                    ->where('resource_group', $resrcGrp)
                    ->pluck(['resource_item']);
                $resrcGrpUOMList = $resourceMasterList->where('resource_name', $resrc)->pluck(['resource_item_uom']);

                foreach ($resrcGrpNoList as $obj1)
                    $resrcGrpNo = $obj1;
                foreach ($resrcGrpUOMList as $obj1)
                    $resrcGrpUOM = $obj1;

                $resourceItemList = array();
                $count = 0;
                foreach ($resrcItemList as $obj1) {
                    $count += 1;
                    $resrcItem = $obj1;
                    $resrcItemUOMList = $resourceMasterList->where('resource_name', $resrc)
                        ->where('resource_group', $resrcGrp)
                        ->where('resource_item', $resrcItem)
                        ->pluck(['resource_item_uom']);
                    $resrcItemNoList = $resourceMasterList->where('resource_name', $resrc)
                        ->where('resource_group', $resrcGrp)
                        ->where('resource_item', $resrcItem)
                        ->pluck(['id']);

                    foreach ($resrcItemNoList as $obj2)
                        $resrcItemId = $obj2;
                    foreach ($resrcItemUOMList as $obj1)
                        $resrcItemUOM = $obj1;

                    $resourceItemList[] = ['resourceItemName' => $resrcItem, 'resourceItemId' => $resrcItemId, 'resourceItemUOM' => $resrcItemUOM];
                }
                $resourceGroupList[] = ['resourceGroupName' => $resrcGrp, 'resourceGroupNo' => $resrcGrpNo, 'itemCount' => $count, 
                    'resourceGrpItemList' => $resourceItemList, 'resourceGroupUOM' => $resrcGrpUOM];
            }
            $resourceList[] = ['resourceName' => $resrc, 'resourceAct' => $resrcAct, 'resourceNo' => $resrcNo, 'resourceGroupList' => $resourceGroupList];
        }
        return view('kspcbcreate', ['resourceList' => $resourceList,]);
    }

    public function store(Request $request)
    {
        if (is_null(session('user_id'))) return redirect()->route('logout');
        
        $resourceMasterList = ResourceLookupMaster::where('is_active', 1)
            ->where('company_id', session('company_id'))
            ->where('branch_id', session('branch_id'))
            ->orderBy('resource_no')
            ->orderBy('sort_order')
            ->get();

        $yr = date('Y', strtotime($request->get('txtissuedate')));
		$mn = date('m', strtotime($request->get('txtissuedate')));
		$dy = date('d', strtotime($request->get('txtissuedate')));
		$joindate = Carbon::create($yr, $mn, $dy, 0);
		$sdtstr = $yr . '-' . $mn . '-' . $dy;
		$issuedt = Carbon::parse($sdtstr);

        $yr = date('Y', strtotime($request->get('txtvaliditydate')));
		$mn = date('m', strtotime($request->get('txtvaliditydate')));
		$dy = date('d', strtotime($request->get('txtvaliditydate')));
		$joindate = Carbon::create($yr, $mn, $dy, 0);
		$sdtstr = $yr . '-' . $mn . '-' . $dy;
		$validdt = Carbon::parse($sdtstr);
        $insertData = array();

        $yr = date('Y', strtotime($request->get('txtissuedate')));
        $mn = date('F', strtotime($request->get('txtissuedate')));
        $monthyr = $mn . '-' . $yr;

        foreach ($resourceMasterList as $resourceMaster) {
            $controlname = 'txtresrcitem_' . $resourceMaster->id;
            $controlValue = 0;
            if(!is_null($request->get($controlname)))
                $controlValue = $request->get($controlname);

            $insertData[] = [
                'company_id' => session('company_id'),
                'branch_id' => session('branch_id'),
                'resource_no' => $resourceMaster->resource_no,
                'resource_name' => $resourceMaster->resource_name,
                'resource_act_year' => $resourceMaster->resource_act_year,
                'resource_group_no' => $resourceMaster->resource_group_no,
                'resource_group' => $resourceMaster->resource_group,
                'resource_item' => $resourceMaster->resource_item,
                'resource_item_uom' => $resourceMaster->resource_item_uom,
                'sort_order' => $resourceMaster->sort_order,
                'consent_month' => $monthyr,
                'consent_date' => $issuedt,
                'consent_no' => $request->txtconsentno,
                'consent_validity' => $validdt,
                'resource_item_value' => $controlValue, 
                'created_by' => session('user_id'),
            ];
        }
        DB::table('resource_lookup')->insert($insertData);
        return redirect()->route('mkspcbcreate')->with('success', 'State has been successfully added');
    }
}