<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', 'UserLoginController@distechlogin')->name('distechlogin');
Route::get('/getcurrentlocation', 'UserLoginController@getlocation')->name('getlocation');
Route::get('/login', 'UserLoginController@showlogin')->name('logging-auth');
Route::get('/login/getLocations/{userid}', 'UserLoginController@showBranches');
Route::post('/logging', 'UserLoginController@login')->name('login-auth');
Route::get('/verify', 'UserLoginController@showverify')->name('verify-auth');
Route::post('/verifing', 'UserLoginController@verify')->name('verifing-auth');
Route::get('/logout', 'UserLoginController@logout')->name('logout');
Route::get('/companyselection', 'UserLoginController@showCompanySelection')->name('company-selection');
Route::get('/getBranches/{stateid}', 'UserLoginController@getBranches');
Route::post('/companyselect', 'UserLoginController@postCompanySelection')->name('company-select');
Route::get('/companymapselect/{branchid}', 'UserLoginController@postCompanyMapSelection')->name('companymapselect');

// Default Module
Route::get('/dashboard', 'NewDashboardController@index')->name('dashboard');
Route::get('/device/fetchcurrentdata', 'NewDashboardController@fetchcurrentdata');
Route::get('/device/fetchinitialdata', 'NewDashboardController@fetchinitialdata');
Route::get('/device/fetchdata', 'NewDashboardController@fetchdata');
Route::get('/device/fetchpresentvalue/{deviceName}', 'NewDashboardController@fetchPresentValue');
Route::get('/device/fetchgraphvalue/{deviceName}', 'NewDashboardController@fetchGraphValue');
Route::get('/dashboardadmin/getbranches/{companyid}', 'CompanyBranchController@getBranches');
Route::get('/dashboardadmin/getdata/{companyid}/{branchid}', 'NewDashboardController@getDeviceData');
Route::get('/device/fetchdataadmin', 'NewDashboardController@fetchdataadmin');
Route::get('changeDashboard/{branchid}', 'NewDashboardController@changeDashboard');
Route::get('/device/toggleac/{deviceid}/{devicevalue}', 'NewDashboardController@toggleAC')->name('toggle-ac');
Route::get('/reportdash/{deviceid}', 'NewDashboardController@exportCSV');

Route::get('/contactus', 'NewDashboardController@viewcontactus')->name('contactus');
Route::get('/traffic', 'NewDashboardController@viewtraffic')->name('traffic');
Route::get('/kspcb', 'NewDashboardController@viewkspcb')->name('mkspcb');

Route::get('/kspcbcreate', 'MKSPCBController@create')->name('mkspcbcreate');
Route::post('/kspcbstore', 'MKSPCBController@store')->name('mkspcbstore');

Route::get('/trends', 'TrendsController@viewtrends')->name('trends');
Route::get('getReportRangeInterval/{rangetype}', 'TrendsController@getReportRangeInterval');
// Route::get('device/drawgraph/{rangetype}/{intervalvalue}', 'TrendsController@drawgraph');
Route::get('device/drawgraph/{rangetype}', 'TrendsController@drawgraph');
// Route::get('device/drawgraphdata/{deviceName}/{rangetype}/{intervalvalue}', 'TrendsController@drawgraphdata');
Route::get('device/drawgraphdata/{deviceName}/{rangetype}', 'TrendsController@drawgraphdata');
Route::get('device/drawgraphdatanew/{deviceName}/{rangetype}', 'TrendsController@drawgraphdatanew');
// Route::get('/reporttrends/{deviceids}/{rangetype}/{intervalvalue}', 'TrendsController@exportCSV');
Route::get('/reporttrends/{deviceids}/{rangetype}', 'TrendsController@exportCSV');
