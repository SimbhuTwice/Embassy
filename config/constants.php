<?php

return array(
    
    // 'distechurl'        => 'http://103.249.206.84:4000/api/rest/v1/',
    // 'distechUsername'   => 'admin',
    // 'distechPassword'   => 'Admin@1234',

    'admin_userid'      => '1',
    'tableNamePrefix'   => 'compbranch_distech_',
    'weather_api' => 'http://www.7timer.info/bin/api.pl?',
    'weather_api_query' => '&product=civillight&output=json',
    'weather_api_query_civil' => '&product=civil&output=json',

    'ExecutionTimeOut'  => 0,
    'GetDashboardDistechRecords' => 90,//72,//15,
    'GetDistechRecords' => 10,
    'guzzleTimeOut'     => 15, 

    // Branch Settings
    'BS_Refresh_Time'   => 'Graph Refresh Time (in Minutes)',
    'BS_Send_Mail'      => 'Send Mail (1/0)',
    'BS_Google_Maps'    => 'Google Maps',
    'BS_Change_Case'    => 'Change Device Name Case',
    'BS_Change_Case_Y'  => 'Y',

    'DefaultRefreshTime'=> '5',
    'Dashboard_Header_Count'    => 'count',
    'Dashboard_Header_Value'    => 'value',

    'XAxisTimeFormat'   => 'H:i',

    // HotBreads
    'api_analog_input'  => 'analog-input',
    'api_analog_value'  => 'analog-value',
    'api_binary_input'  => 'binary-input',
    'api_binary_value'  => 'binary-value',
    'api_binary_output' => 'binary-output',

    // Manyata Embassy
    'api_auth_key_embassy' => '029D39BED4FD7ED944608CFE80C59E73EC505340494CCD0787B6DCA7CDEA78EB',
    'api_pmten' => 'getpmten',
    'api_pmtwofive' => 'getpmtwofive',

    'chart_gauge' => 'Gauge',
    'chart_value' => 'Value',
    'chart_trend' => 'Trend',
    'chart_none' => 'None',

    'uom_degree'        => ' &deg;C',
    'uom_kw'            => ' kW',
    'uom_kwh'           => ' MWh',

    'Trends_calc_average' => 'average',

    'Reports_Current' => 'Today',
    'Reports_Weekly' => 'Weekly',
    'Reports_Monthly' => 'Monthly',
    'Reports_Yearly' => 'Yearly',

    'Reports_Interval_10mins' => '0',
    'Reports_Interval_Hourly' => '1',
    'Reports_Interval_Daily' => '2',
    'Reports_Interval_Weekly' => '3',
    'Reports_Interval_Monthly' => '4',

    'TrendsCalculation' => 'regular',

    // Icons
    'icon_fridge'            => '<i class="material-icons fa-2x">kitchen</i>',
    'icon_freezer'           => '<i class="material-icons fa-2x">kitchen</i>',
    'icon_sensors'           => '<i class="material-icons fa-2x">sensors</i>',
    'icon_temperature'       => '<i class="material-icons fa-2x">thermostat</i>',
    'icon_energyMeter'       => '<i class="material-icons fa-2x">bolt</i>',
    'icon_acUnit'            => '<i class="material-icons fa-2x">ac_unit</i>',
    'icon_storage'           => '<i class="material-icons fa-2x">storage</i>',
    'icon_others'            => '<i class="material-icons fa-2x">device_hub</i>',
    
    'CC_MailId'         => 'selvi_rosy@yahoo.co.in',
    'CC_UserName'       => 'RosaliyaJ',

    // CompanyName
    'hotbreads'     => 'Hot Breads',
    'manyata'       => 'Embassy',
);
?>
