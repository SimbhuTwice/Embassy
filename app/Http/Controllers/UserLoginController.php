<?php

namespace App\Http\Controllers;

use App\User;
use App\RoleMaster;
use App\BranchUser;
use App\CompanyBranch;
use App\BranchDeviceLocation;
use App\State;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Config;
use Illuminate\Support\Facades\Log;

class UserLoginController extends Controller
{
    public function distechlogin()
    {
        if (!is_null(session('user_id')))
            User::where("id", session('user_id'))->update(['is_logged_in' => 0]);
        return view('auth.login');
    }

    public function getlocation()
    {
        return view('auth.getlocation');
    }

    public function showlogin()
    {
        return view('auth.login');
    }

    public function loginWithLocation(Request $request)
    {
        $this->validate($request, [
            'user_id' => ['required'],
            'password' => ['required'],
            'comp_branch_id' => ['required'],
        ], 
        [
            'user_id.required' => 'Email ID is required',
            'password.required' => 'Password is required',
            'comp_branch_id.required' => 'Location is required',
        ]);

        $usr = User::join('role_master', 'role_master.id', 'users.role_id')
            // ->leftjoin('company_branch', 'company_branch.id', 'users.branch_id')
            ->where('users.is_active', 1)
            ->where('email', $request->user_id)
            ->select('users.*', 'role_master.is_admin', 'role_master.is_branch_admin')
            ->first();

        if (isset($usr))
        {
            if (Hash::check($request->password, $usr->password))
            {
                $compbr = CompanyBranch::where('id', $request->comp_branch_id)->first();
                session(['is_admin' => $usr->is_admin, 
                    'is_branch_admin' => $usr->is_branch_admin, 
                    'user_id' => $usr->id, 
                    'user_name' => $usr->user_name,
                    'branch_id' => $compbr->id,
                    'actual_branch_id' => $usr->branch_id,
                    'table_name' => $compbr->table_name,
                    'company_id' => $usr->company_id,
                    'branch_name' => $compbr->branch_name,
                ]);
                if ($usr->is_verified == 0)
                    return redirect()->route('verify-auth');
                else
                {
                    $branchList = DB::table('branch_users')
                        ->join('company_branch', 'company_branch.id', 'branch_users.branch_id')
                        ->join('company_master', 'company_master.id', 'company_branch.company_id')
                        ->where('user_id', $usr->id)
                        ->select('company_branch.id', DB::raw("company_branch.branch_name AS branchname"), DB::raw("CONCAT(company_master.company_name, ' - ', company_branch.branch_name) AS name"))
                        ->get();
                    session(['branchList' => $branchList]);
                    return redirect()->route('dashboard');
                }
            }
            else
                return redirect()->route('login-auth')->with('unsuccess', 'Password is incorrect');
        }
        else
        {
            return redirect()->route('login-auth')->with('unsuccess', 'User ID/Password is incorrect');
        }
    }

    public function login(Request $request)
    {
        $this->validate($request, [
            'user_id' => ['required'],
            'password' => ['required'],
            // 'comp_branch_id' => ['required'],
        ], 
        [
            'user_id.required' => 'Email ID is required',
            'password.required' => 'Password is required',
            // 'comp_branch_id.required' => 'Location is required',
        ]);

        $usr = User::join('role_master', 'role_master.id', 'users.role_id')
            // ->leftjoin('company_branch', 'company_branch.id', 'users.branch_id')
            ->where('users.is_active', 1)
            ->where('email', $request->user_id)
            ->select('users.*', 'role_master.is_admin', 'role_master.is_branch_admin')
            ->first();

        if (isset($usr))
        {
            if (Hash::check($request->password, $usr->password))
            {
                if ($usr->is_logged_in == 1) {
                    $errors = ['You have already logged in'];
                    return redirect()->route('distechlogin')->withErrors($errors);
                }
                
                if(\Auth::attempt([
                    'email' => $request->user_id,
                    'password' => $request->password
                ]))
                    User::where("id", $usr->id)->update(['is_logged_in' => 1]);

                // $compbr = CompanyBranch::where('id', $request->comp_branch_id)->first();
                session(['is_admin' => $usr->is_admin, 
                    'is_branch_admin' => $usr->is_branch_admin, 
                    'user_id' => $usr->id, 
                    'user_name' => $usr->user_name,
                    // 'branch_id' => $compbr->id,
                    'actual_branch_id' => $usr->branch_id,
                    // 'table_name' => $compbr->table_name,
                    'company_id' => $usr->company_id,
                    // 'branch_name' => $compbr->branch_name,
                ]);
                $request->session()->put('user_id', $usr->id);
                if ($usr->is_verified == 0)
                    return redirect()->route('verify-auth');
                else
                {
                    $branchList = DB::table('branch_users')
                        ->join('company_branch', 'company_branch.id', 'branch_users.branch_id')
                        ->join('company_master', 'company_master.id', 'company_branch.company_id')
                        ->where('user_id', $usr->id)
                        ->select('branch_users.id', DB::raw("company_branch.id AS branchid"), 'company_branch.state_id', 
                            DB::raw("company_branch.branch_name AS branchname"), 
                            DB::raw("CONCAT(company_master.company_name, ' - ', company_branch.branch_name) AS name"),
                            'company_master.company_name')
                        ->orderBy('branch_users.id')
                        ->get();
                    
                    $defaultStateIdList = $branchList->pluck(['state_id']);
                    $defaultStateId = 0;
                    foreach ($defaultStateIdList as $obj) {
                        $defaultStateId = $obj;
                        break;
                    }

                    $companyNameList = $branchList->pluck(['company_name']);
                    $companyName = '';
                    foreach ($companyNameList as $obj) {
                        $companyName = $obj;
                        break;
                    }
                    session(['companyName' => $companyName]);

                    $stateIdArrayList = $branchList->pluck(['state_id']);
                    $stateIdList = json_decode(json_encode($stateIdArrayList), true);
                    session(['branchList' => $branchList]);
                    // return redirect()->route('dashboard');
                    $states = State::where('is_active', 1)
                        ->whereIn('id', $stateIdList)
                        ->select('state_name', 'id')
                        ->get();

                    $defaultStateBranchList = $branchList->whereIn("state_id", $stateIdArrayList);
                    return view('auth.companyselectionmap', ['states' => $states, 'defaultStateId' => $defaultStateId, 'branchList' => $defaultStateBranchList]);
                }
            }
            else
            {
                $errors = ['Password is incorrect'];
                return redirect()->route('distechlogin')->withErrors($errors);
                // return redirect()->route('login-auth')->with('unsuccess', 'Password is incorrect');
            }
        }
        else
        {
            $errors = ['User ID/Password is incorrect'];
            return redirect()->route('distechlogin')->withErrors($errors);
            // return redirect()->route('login-auth')->with('unsuccess', 'User ID/Password is incorrect');
        }
    }

    public function showverify()
    {
        return view('auth.verify');
    }

    public function verify(Request $request)
    {
        $this->validate($request, [
            'password' => ['required', 'string', 'min:8', 'regex:/^.*(?=.{8,})(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[\d\x])(?=.*[!@#$%&_]).*$/', 'confirmed'],
            // 'confirm_password' => ['required_with:password', 'same:password'],
        ], 
        [
            'password.required' => 'Password is required', 
        ]);

        $currentdate = Carbon::now('Asia/colombo');
        $verifydate = date('Y-m-d H:i', strtotime($currentdate));

        $usr = User::where('is_active', 1)
            ->where('id', session('user_id'))
            ->first();
        $usr->password = Hash::make($request->password);
        $usr->email_verified_at = $verifydate;
        $usr->is_verified = 1;
        $usr->save();

        $branchList = DB::table('branch_users')
            ->join('company_branch', 'company_branch.id', 'branch_users.branch_id')
            ->join('company_master', 'company_master.id', 'company_branch.company_id')
            ->where('user_id', $usr->id)
            ->select('branch_users.id', DB::raw("company_branch.id AS branchid"), 'company_branch.state_id', DB::raw("company_branch.branch_name AS branchname"), DB::raw("CONCAT(company_master.company_name, ' - ', company_branch.branch_name) AS name"))
            ->orderBy('branch_users.id')
            ->get();
        
        $defaultStateIdList = $branchList->pluck(['state_id']);
        $defaultStateId = 0;
        foreach ($defaultStateIdList as $obj) {
            $defaultStateId = $obj;
            break;
        }

        $stateIdArrayList = $branchList->pluck(['state_id']);
        $stateIdList = json_decode(json_encode($stateIdArrayList), true);
        session(['branchList' => $branchList]);
        // return redirect()->route('dashboard');
        $states = State::where('is_active', 1)
            ->whereIn('id', $stateIdList)
            ->select('state_name', 'id')
            ->get();

        $defaultStateBranchList = $branchList->whereIn("state_id", $stateIdArrayList);
        return view('auth.companyselectionmap', ['states' => $states, 'defaultStateId' => $defaultStateId, 'branchList' => $defaultStateBranchList]);
    }

    public function showCompanySelection()
    {
        if (is_null(session('user_id'))) return redirect()->route('logout');

        $usr = User::where('is_active', 1)
            ->where('id', session('user_id'))
            ->first();

        $branchList = DB::table('branch_users')
            ->join('company_branch', 'company_branch.id', 'branch_users.branch_id')
            ->join('company_master', 'company_master.id', 'company_branch.company_id')
            ->where('user_id', $usr->id)
            ->select('branch_users.id', DB::raw("company_branch.id AS branchid"), 'company_branch.state_id', DB::raw("company_branch.branch_name AS branchname"), DB::raw("CONCAT(company_master.company_name, ' - ', company_branch.branch_name) AS name"))
            ->orderBy('branch_users.id')
            ->get();
        
        $defaultStateIdList = $branchList->pluck(['state_id']);
        $defaultStateId = 0;
        foreach ($defaultStateIdList as $obj) {
            $defaultStateId = $obj;
            break;
        }

        $stateIdArrayList = $branchList->pluck(['state_id']);
        $stateIdList = json_decode(json_encode($stateIdArrayList), true);
        $states = State::where('is_active', 1)
            ->whereIn('id', $stateIdList)
            ->select('state_name', 'id')
            ->get();

        $defaultStateBranchList = $branchList->whereIn("state_id", $stateIdArrayList);
        return view('auth.companyselectionmap', ['states' => $states, 'defaultStateId' => $defaultStateId, 'branchList' => $defaultStateBranchList]);
    }

    public function getBranches(int $state_id)
    {
        $branchList = DB::table('branch_users')
            ->join('company_branch', 'company_branch.id', 'branch_users.branch_id')
            ->join('states', 'states.id', 'company_branch.state_id')
            ->join('company_master', 'company_master.id', 'company_branch.company_id')
            ->where('user_id', session('user_id'))
            ->where('states.id', $state_id)
            ->select('company_branch.id', 'company_branch.city', DB::raw("company_branch.branch_name AS branchname"), DB::raw("CONCAT(company_master.company_name, ' - ', company_branch.branch_name) AS name"))
            ->get();
        session(['branchList' => $branchList]);
        return json_encode($branchList);
    }

    public function postCompanyMapSelection(int $branchid)
    {
        $compbr = CompanyBranch::where('id', $branchid)->first();
        session([
            'branch_id' => $compbr->id,
            'table_name' => $compbr->table_name,
            'branch_name' => $compbr->branch_name,
            'latitude' => $compbr->loclatitude,
            'longitude' => $compbr->loclongitude,
            'distech_deviceip' => $compbr->distech_deviceip,
            'distech_username' => $compbr->distech_username,
            'distech_password' => $compbr->distech_password
        ]);

        $compbrDevLocList = BranchDeviceLocation::where('branch_id', $branchid)->orderBy('sort_order', 'asc')->get();
        session(['compbrDevLocList' => $compbrDevLocList]);
        // if (session('companyName') == Config::get('constants.hotbreads'))
        //     return redirect()->route('hbdashboard');
        // else if (session('companyName') == Config::get('constants.manyata'))
        //     return redirect()->route('mdashboard');
        // else
            return redirect()->route('dashboard');
    }

    public function postCompanySelection(Request $request)
    {
        $this->validate($request, [
            'state_id' => ['required'],
            'city' => ['required'],
        ], 
        [
            'state_id.required' => 'State is required', 
            'city.required' => 'City is required',
        ]);
        $compbr = CompanyBranch::where('id', $request->city)->first();
        session([
            'branch_id' => $compbr->id,
            'table_name' => $compbr->table_name,
            'branch_name' => $compbr->branch_name,
            'latitude' => $compbr->loclatitude,
            'longitude' => $compbr->loclongitude,
            'distech_deviceip' => $compbr->distech_deviceip,
            'distech_username' => $compbr->distech_username,
            'distech_password' => $compbr->distech_password
        ]);

        $compbrDevLocList = BranchDeviceLocation::where('branch_id', $request->city)->orderBy('sort_order', 'asc')->get();
        session(['compbrDevLocList' => $compbrDevLocList]);
        // if (session('companyName') == Config::get('constants.hotbreads'))
        //     return redirect()->route('hbdashboard');
        // else if (session('companyName') == Config::get('constants.manyata'))
        //     return redirect()->route('mdashboard');
        // else
            return redirect()->route('dashboard');
    }

    public function logout(Request $request)
    {
        if (!is_null(session('user_id')))
            User::where("id", session('user_id'))->update(['is_logged_in' => 0]);
        \Auth::logout();
        $request->session()->flush();
        return redirect()->route('distechlogin')->with('unsuccess', 'Logged out successfully');
    }

    public function showBranches(string $userid)
    {
        $usr = User::where('is_active', 1)
                ->where('email', $userid)
                ->first();
        $branchList = array();
        if (isset($usr))
        {
            $branchList = DB::table('branch_users')
                ->join('company_branch', 'company_branch.id', 'branch_users.branch_id')
                ->join('company_master', 'company_master.id', 'company_branch.company_id')
                ->where('user_id', $usr->id)
                ->select('company_branch.id', DB::raw("company_branch.branch_name AS branchname"), DB::raw("CONCAT(company_master.company_name, ' - ', company_branch.branch_name) AS name"))
                ->get();
        }
        return json_encode($branchList);
    }
}
