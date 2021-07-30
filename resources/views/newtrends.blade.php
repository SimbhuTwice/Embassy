@extends('layouts.newmasterlayoutv2')

@section('headcontent')
         
    <input type="hidden" id="hdnRefreshTime" value="{{ $refreshTime }}" />
    <input type="hidden" id="hdnChartCount" value="{{ $chartCount }}" />

    <div class="box" id="header_mydash">
        <div class="box-head">
            <h5><span id="spMyDash"></span></h5>
        </div>
        <div class="box-bottom">
            <p>My Dash</p>
        </div>
    </div>
    @if ($chartCount == 0)
        @for ($i = 0; $i < 3; $i++)
        <div class="box2">
            <div class="box-head">
                <h5>0/0</h5>
            </div>
            <div class="alrt-desc">
                <p>&nbsp;</p>
            </div>
            <div class="box-bottom">
                <p>&nbsp;</p>
            </div>
        </div>
        @endfor

        @for ($i = 0; $i < 3; $i++)
        <div class="box">
            <div class="box-head">
                <h5>0.00</h5>
            </div>
            <div class="box-bottom">
                <p>&nbsp;</p>
            </div>
        </div>
        @endfor
    @endIf
    @foreach($headerObjectNameList as $chartObj)
        <div class="box2" id="header_{{ $chartObj['name'] }}">
            <div class="box-head">
                <h5><span id="sphead_{{ $chartObj['name'] }}"></span><span class="spheaderuom" id="spheaduom_{{ $chartObj['name'] }}"></span></h5>
            </div>
            <div class="alrt-desc" id="desc_{{ $chartObj['name'] }}">
                <p><span id="spheadstatus_{{ $chartObj['name'] }}"></p>
            </div>
            <div class="box-bottom">
                <p>{{ $chartObj['deviceName'] }}</p>
            </div>
        </div>
    @endforeach
    <div class="box2 weather-box" id="header_weather">
        <div class="box-head">
            <!-- <img id="imgWeather" class="weather-style" onerror="weather_error();" /> -->
            <h5><span id="spWeather"></span><span class="spheaderuom" id="spWeatherUOM"></span></h5>
        </div>
        <div class="alrt-desc">
            <p><span id="spWeatherDesc"></p>
        </div>
        <div class="box-bottom">
            <p>Weather</p>
        </div>
    </div>
@endsection

@section('content')
    <div class="trends-sect">
        <input type="hidden" id="hdnChartNos" value="{{ $chartnoCount }}" />
    @foreach($newTrendDeviceList as $trendchartObj)
        <div class="row" id="div_{{ $trendchartObj['chartno'] }}">
            <div class="col-sm-2 pr-0">
                <div class="select">
                    <select name="slc_range_type" id="slcrangetype_{{ $trendchartObj['chartno'] }}" autofocus="autofocus" tabindex="1"
                        onclick="slcrangetype_clicked(this.id)" onchange="slcrangetype_changed(this.id)">
                        @foreach($reportRangeList as $rr)
                            <option value="{{ $rr->range_name }}" @if($rr->range_name == $defaultRangeType) selected @endif>{{ $rr->range_name }}</option>
                        @endforeach
                    </select>
                </div>
                <!-- <div class="select">
                    <select name="slc_interval" id="slc_interval_{{ $trendchartObj['chartno'] }}" autofocus="autofocus" tabindex="1">
                        @foreach($reportIntervalList as $rr)
                            <option value="{{ $rr->interval_value }}">{{ $rr->interval_name }}</option>
                        @endforeach
                    </select>
                </div> -->
            </div>
            <div class="col-sm-4">
                <div class="multipleSelection">            
                    <div class="selectBox" onclick="showCheckboxes(checkBoxes_{{ $trendchartObj['chartno'] }}.id)" id="multiselect_{{ $trendchartObj['chartno'] }}">            
                        <select id='slc_DeviceNames' name="slc_DeviceNames" class="form-control">
                            <option value="">Select Devices</option>
                        </select>
                        <div class="overSelect"></div>
                    </div>
                </div>
                <div id="checkBoxes_{{ $trendchartObj['chartno'] }}" class="checkBoxes">
                @foreach($trendchartObj['trends'] as $chartObj)
                    @if ($chartObj['is_multi_select'] == 1)
                        <div class="custom-control custom-checkbox pt-2">
                            <input type="checkbox" class="custom-control-input" name="{{ $trendchartObj['chartno'] }}btn"
                                 id="{{ $trendchartObj['chartno'] }}btn_{{ $chartObj['device_name'] }}">
                            <label class="custom-control-label multiselect-label" for="{{ $trendchartObj['chartno'] }}btn_{{ $chartObj['device_name'] }}">
                                {{ str_replace('_', ' ', $chartObj['device_name']) }}</label>
                        </div>
                    @else
                        <div class="custom-control custom-radio pt-2">
                            <input type="radio" class="custom-control-input" name="{{ $trendchartObj['chartno'] }}btn"
                                 id="{{ $trendchartObj['chartno'] }}btn_{{ $chartObj['device_name'] }}">
                            <label class="custom-control-label multiselect-label" for="{{ $trendchartObj['chartno'] }}btn_{{ $chartObj['device_name'] }}">
                                {{ str_replace('_', ' ', $chartObj['device_name']) }}</label>
                        </div>
                    @endif
                @endforeach
                </div>
            </div>
            <div class="col-sm-3">
                <div class="btn trn-btn1">
                    <a href="#" id="trends_{{ $trendchartObj['chartno'] }}" onclick="displayTrends(this.id);">View Trends</a>
                </div>
            </div>

            <div class="col-sm-3 pr-0">
                <div class="btn trn-btn2">
                    <input type="hidden" id="hdnDeviceName" />
                    <a href="#" id="export_{{ $trendchartObj['chartno'] }}" onclick="exportReport(this.id);">Export as CSV</a>
                </div>
            </div>
        </div>
        <div class="big-box">
            <div class="text" id="trendsnodata_{{ $trendchartObj['chartno'] }}">
                <p>Select above criteria to view or export trends</p>
            </div>
            <div class="col-sm-12 graph-box p-0" id="trendchart_{{ $trendchartObj['chartno'] }}"></div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="box-rect"></div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">&nbsp;</div>
        </div>
    @endforeach
    </div>
@endsection

@section('script')
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/highcharts-more.js"></script>
    <script src="https://code.highcharts.com/modules/exporting.js"></script>
    <script src="https://code.highcharts.com/modules/export-data.js"></script>
    <script src="https://code.highcharts.com/modules/accessibility.js"></script>

    <script>
        var d = new Date();
        // var tday = d.getDate() + '-' + (d.getMonth() + 1) + '-' + d.getFullYear();
        var hrs = d.getHours();
        var timeArray = [], headerObj = [];
        var refreshTime = 120000;
        clearInterval(refreshTime);
        refreshTime = document.getElementById("hdnRefreshTime").value;
        setInterval(function() { autoRefresh(); }, refreshTime);
        var red = '#ed1b24', green = '#00a652', white = '#ffffff';
        var rgbRed = "rgb(237, 27, 36)", rgbGreen = "rgb(0, 166, 82)", rgblightblue = "rgb(231, 232, 234)", rgbwhite = "rgb(255, 255, 255)";
        // containerBinOut = document.createElement('div');
        // containerBinOut.setAttribute("id", "divBinOutput");

        var a = setInterval(function () { blinkHeaderColor() }, 500);
        function blinkHeaderColor() {
            for (var cnt = 0; cnt < headerObj.length; cnt ++)
            {
                if (document.getElementById("header_" + headerObj[cnt].name)) {
                    if (document.getElementById("header_" + headerObj[cnt].name).style.backgroundColor == red || document.getElementById("header_" + headerObj[cnt].name).style.backgroundColor == rgbRed)
                        document.getElementById("header_" + headerObj[cnt].name).style.backgroundColor = rgblightblue;
                    else if (document.getElementById("header_" + headerObj[cnt].name).style.backgroundColor == rgblightblue)
                        document.getElementById("header_" + headerObj[cnt].name).style.backgroundColor = red;
                }

                if (document.getElementById("sphead_" + headerObj[cnt].name)) {
                    if (document.getElementById("sphead_" + headerObj[cnt].name).style.color == red || document.getElementById("sphead_" + headerObj[cnt].name).style.color == rgbRed)
                        document.getElementById("sphead_" + headerObj[cnt].name).style.color = rgblightblue;
                    else if (document.getElementById("sphead_" + headerObj[cnt].name).style.color == rgblightblue)
                        document.getElementById("sphead_" + headerObj[cnt].name).style.color = red;
                }

                if (document.getElementById("spheaduom_" + headerObj[cnt].name)) {
                    if (document.getElementById("spheaduom_" + headerObj[cnt].name).style.color == red || document.getElementById("spheaduom_" + headerObj[cnt].name).style.color == rgbRed)
                        document.getElementById("spheaduom_" + headerObj[cnt].name).style.color = rgblightblue;
                    else if (document.getElementById("spheaduom_" + headerObj[cnt].name).style.color == rgblightblue)
                        document.getElementById("spheaduom_" + headerObj[cnt].name).style.color = red;
                }

                if (document.getElementById("spheadstatus_" + headerObj[cnt].name)) {
                    if (document.getElementById("spheadstatus_" + headerObj[cnt].name).style.color == red || document.getElementById("spheadstatus_" + headerObj[cnt].name).style.color == rgbRed)
                        document.getElementById("spheadstatus_" + headerObj[cnt].name).style.color = rgblightblue;
                    else if (document.getElementById("spheadstatus_" + headerObj[cnt].name).style.color == rgblightblue)
                        document.getElementById("spheadstatus_" + headerObj[cnt].name).style.color = red;
                }
            }
        }

        function fixHeaderColor() {
            clearInterval(a);
        }

        function weather_error() {
            this.onerror=null;
            if (hrs > 6 && hrs < 19)
                this.src='images/day.svg';
            else
                this.src='images/night.svg';
        }
    
        $.ajax({
            type: "GET",
            url : 'device/fetchinitialdata',
            dataType: "json",
            error: function() {},
            success: function (json) {console.log(json);
                document.querySelector("#preloader").style.visibility = "visible";
                refreshTime = json.refreshTime;
                for (var i = 0; i < json.weatherArray.length; i++) {
                    document.getElementById("spWeather").innerHTML = json.weatherArray[i].temp;
                    document.getElementById("spWeatherUOM").innerHTML = json.weatherArray[i].uom;
                    document.getElementById("spWeatherDesc").innerHTML = json.weatherArray[i].status;
                    // document.getElementById("imgWeather").src = 'images/' + json.weatherArray[i].status.toLowerCase() + '.svg';
                    document.getElementById("header_weather").style.backgroundImage = "url('images/" + json.weatherArray[i].status.toLowerCase() + ".svg')";
                    break;
                }
                
                document.getElementById("spMyDash").innerHTML = json.recordStatus;
                if (json.recordStatus == 'OFFLINE')
                {
                    // document.getElementById("header_mydash").className.replace(/\bsuccess-box\b/g, "");
                    // document.getElementById("header_mydash").className = "box alert-box";
                    document.getElementById("header_mydash").style.backgroundColor  = red;
                }
                else
                    document.getElementById("header_mydash").style.backgroundColor  = green;

                // Set header
                if (parseInt(document.getElementById("hdnChartCount").value) > 0) {
                for (var i = 0; i < json.headerObjectNameList.length; i++) {
                    document.getElementById("sphead_" + json.headerObjectNameList[i].name).innerHTML = json.headerObjectNameList[i].activevalue + "/" + json.headerObjectNameList[i].count;
                    if (json.headerObjectNameList[i].activevalue == json.headerObjectNameList[i].count)
                        document.getElementById("spheadstatus_" + json.headerObjectNameList[i].name).innerHTML = "NORMAL";
                    else
                        document.getElementById("spheadstatus_" + json.headerObjectNameList[i].name).innerHTML = "ALERT";

                    if ((json.headerObjectNameList[i].name.toLowerCase().indexOf("room") >= 0) || (json.headerObjectNameList[i].name.toLowerCase().indexOf("active") >= 0))
                    {
                        // document.getElementById("sphead_" + json.headerObjectNameList[i].name).innerHTML = json.headerObjectNameList[i].presentvalue;
                        document.getElementById("sphead_" + json.headerObjectNameList[i].name).innerHTML = parseFloat(json.headerObjectNameList[i].currvalue).toFixed(2);
                        document.getElementById("spheaduom_" + json.headerObjectNameList[i].name).innerHTML = json.headerObjectNameList[i].uom;
                        document.getElementById("spheadstatus_" + json.headerObjectNameList[i].name).innerHTML = '';
                    }

                    if (json.headerObjectNameList[i].name.toLowerCase().indexOf("room") >= 0 ||
                        json.headerObjectNameList[i].name.toLowerCase().indexOf("active") >= 0){
                        document.getElementById("header_" + json.headerObjectNameList[i].name).className = "box lb";
                        document.getElementById("desc_" + json.headerObjectNameList[i].name).remove();
                    }
                    else {
                        if (parseInt(json.headerObjectNameList[i].activevalue) < parseInt(json.headerObjectNameList[i].count)) {
                            document.getElementById("header_" + json.headerObjectNameList[i].name).style.backgroundColor  = red;
                            document.getElementById("sphead_" + json.headerObjectNameList[i].name).style.color = rgblightblue;

                            if (document.getElementById("spheaduom_" + json.headerObjectNameList[i].name))
                                document.getElementById("spheaduom_" + json.headerObjectNameList[i].name).style.color = rgblightblue;

                            if (document.getElementById("spheadstatus_" + json.headerObjectNameList[i].name))
                                document.getElementById("spheadstatus_" + json.headerObjectNameList[i].name).style.color = rgblightblue;
                            // document.getElementById("header_" + json.headerObjectNameList[i].name).className += " alert-box";
                        }
                            
                    }
                    headerObj.push({name: json.headerObjectNameList[i].name, count: json.headerObjectNameList[i].count});
                }
                // Set header ends

                var jsonify = JSON.parse(json.allChartArray);
                var currval = 0, valuechecked = false;
                for (var i = 0; i < jsonify.length; i++) {
                    if (jsonify[i].objectName.toLowerCase().indexOf("room") >= 0 || jsonify[i].charttype.toLowerCase().indexOf("trend") >= 0) {
                        if (jsonify[i].objectName.toLowerCase().indexOf("room") >= 0) {
                            if (jsonify[i].showinheader == '1') {
                                if (hrs >= 10 && hrs < 20) {
                                    currval = parseFloat(jsonify[i].currvalue);
                                    // currval = parseFloat(jsonify[i].roundedData);;
                                    if (jsonify[i].plotbands.length > 0) {
                                        if (currval < parseFloat(jsonify[i].plotbands[0].from) || currval > parseFloat(jsonify[i].plotbands[jsonify[i].plotbands.length - 1].to)) {
                                            if (document.getElementById("header_" + jsonify[i].objectName))
                                                document.getElementById("header_" + jsonify[i].objectName).style.backgroundColor = red;
                                            
                                            if (document.getElementById("sphead_" + jsonify[i].objectName))
                                                document.getElementById("sphead_" + jsonify[i].objectName).style.color = rgblightblue;

                                            if (document.getElementById("spheaduom_" + jsonify[i].objectName))
                                                document.getElementById("spheaduom_" + jsonify[i].objectName).style.color = rgblightblue;

                                            if (document.getElementById("spheadstatus_" + jsonify[i].objectName))
                                                document.getElementById("spheadstatus_" + jsonify[i].objectName).style.color = rgblightblue;
                                            valuechecked = true;
                                        }

                                        if (!valuechecked) {
                                            if (currval >= parseFloat(jsonify[i].plotbands[0].from) && currval <= parseFloat(jsonify[i].plotbands[0].to)) {
                                                if (document.getElementById("header_" + jsonify[i].objectName))
                                                    document.getElementById("header_" + jsonify[i].objectName).style.backgroundColor = jsonify[i].plotbands[jsonify[i].plotbands[0]].color.trim();
                                                
                                                if (document.getElementById("sphead_" + jsonify[i].objectName))
                                                    document.getElementById("sphead_" + jsonify[i].objectName).style.color = rgblightblue;

                                                if (document.getElementById("spheaduom_" + jsonify[i].objectName))
                                                    document.getElementById("spheaduom_" + jsonify[i].objectName).style.color = rgblightblue;

                                                if (document.getElementById("spheadstatus_" + jsonify[i].objectName))
                                                    document.getElementById("spheadstatus_" + jsonify[i].objectName).style.color = rgblightblue;
                                                valuechecked = true;
                                            }
                                        }

                                        if (!valuechecked) {
                                            if (currval >= parseFloat(jsonify[i].plotbands[jsonify[i].plotbands.length - 1].from) && currval <= parseFloat(jsonify[i].plotbands[jsonify[i].plotbands.length - 1].to)) {
                                                if (document.getElementById("header_" + jsonify[i].objectName))
                                                    document.getElementById("header_" + jsonify[i].objectName).style.backgroundColor = jsonify[i].plotbands[jsonify[i].plotbands.length - 1].color.trim();

                                                if (document.getElementById("sphead_" + jsonify[i].objectName))
                                                    document.getElementById("sphead_" + jsonify[i].objectName).style.color = rgblightblue;

                                                if (document.getElementById("spheaduom_" + jsonify[i].objectName))
                                                    document.getElementById("spheaduom_" + jsonify[i].objectName).style.color = rgblightblue;

                                                if (document.getElementById("spheadstatus_" + jsonify[i].objectName))
                                                    document.getElementById("spheadstatus_" + jsonify[i].objectName).style.color = rgblightblue;
                                                valuechecked = true;
                                            }
                                        }
                                    }               
                                }
                                if (!valuechecked) {
                                    if (document.getElementById("header_" + jsonify[i].objectName))
                                        document.getElementById("header_" + jsonify[i].objectName).style.backgroundColor  = green;

                                    if (document.getElementById("sphead_" + jsonify[i].objectName))
                                        document.getElementById("sphead_" + jsonify[i].objectName).removeAttribute("style");

                                    if (document.getElementById("spheaduom_" + jsonify[i].objectName))
                                        document.getElementById("spheaduom_" + jsonify[i].objectName).removeAttribute("style");

                                    if (document.getElementById("spheadstatus_" + jsonify[i].objectName))
                                        document.getElementById("spheadstatus_" + jsonify[i].objectName).removeAttribute("style");
                                }
                            }
                        }
                    }

                    currval = 0, valuechecked = false;
                    if (jsonify[i].charttype.toLowerCase().indexOf("gauge") >= 0) {
                        if (jsonify[i].objectName.toLowerCase().indexOf("room") >= 0) continue;

                        var isMultipleDevice = false;
                        for (var cnt = 0; cnt < headerObj.length; cnt ++)
                        {
                            if (jsonify[i].objectName == headerObj[cnt].name && headerObj[cnt].count > 1)
                                isMultipleDevice = true;
                        }

                        if (jsonify[i].showinheader == '1') {
                            currval = parseFloat(jsonify[i].currvalue);
                            // currval = parseFloat(jsonify[i].roundedData);;
                            if (jsonify[i].plotbands.length > 0) {
                                if (currval < parseFloat(jsonify[i].plotbands[0].from) || currval > parseFloat(jsonify[i].plotbands[jsonify[i].plotbands.length - 1].to)) {
                                    if (!isMultipleDevice) {
                                        if (document.getElementById("header_" + jsonify[i].objectName))
                                            document.getElementById("header_" + jsonify[i].objectName).style.backgroundColor = red;

                                        if (document.getElementById("sphead_" + jsonify[i].objectName))
                                            document.getElementById("sphead_" + jsonify[i].objectName).style.color = rgblightblue;

                                        if (document.getElementById("spheaduom_" + jsonify[i].objectName))
                                            document.getElementById("spheaduom_" + jsonify[i].objectName).style.color = rgblightblue;

                                        if (document.getElementById("spheadstatus_" + jsonify[i].objectName))
                                            document.getElementById("spheadstatus_" + jsonify[i].objectName).style.color = rgblightblue;
                                    }
                                    valuechecked = true;
                                }

                                if (!valuechecked) {
                                    if (currval >= parseFloat(jsonify[i].plotbands[0].from) && currval <= parseFloat(jsonify[i].plotbands[0].to)) {
                                        if (!isMultipleDevice) {
                                            if (document.getElementById("header_" + jsonify[i].objectName))
                                                document.getElementById("header_" + jsonify[i].objectName).style.backgroundColor = jsonify[i].plotbands[jsonify[i].plotbands[0]].color.trim();
                                            
                                            if (document.getElementById("sphead_" + jsonify[i].objectName))
                                                document.getElementById("sphead_" + jsonify[i].objectName).style.color = rgblightblue;

                                            if (document.getElementById("spheaduom_" + jsonify[i].objectName))
                                                document.getElementById("spheaduom_" + jsonify[i].objectName).style.color = rgblightblue;

                                            if (document.getElementById("spheadstatus_" + jsonify[i].objectName))
                                                document.getElementById("spheadstatus_" + jsonify[i].objectName).style.color = rgblightblue;
                                        }
                                        valuechecked = true;
                                    }
                                }

                                if (!valuechecked) {
                                    if (currval >= parseFloat(jsonify[i].plotbands[jsonify[i].plotbands.length - 1].from) && currval <= parseFloat(jsonify[i].plotbands[jsonify[i].plotbands.length - 1].to)) {
                                        if (!isMultipleDevice) {
                                            if (document.getElementById("header_" + jsonify[i].objectName))
                                                document.getElementById("header_" + jsonify[i].objectName).style.backgroundColor = jsonify[i].plotbands[jsonify[i].plotbands.length - 1].color.trim();

                                            if (document.getElementById("sphead_" + jsonify[i].objectName))
                                                document.getElementById("sphead_" + jsonify[i].objectName).style.color = rgblightblue;

                                            if (document.getElementById("spheaduom_" + jsonify[i].objectName))
                                                document.getElementById("spheaduom_" + jsonify[i].objectName).style.color = rgblightblue;

                                            if (document.getElementById("spheadstatus_" + jsonify[i].objectName))
                                                document.getElementById("spheadstatus_" + jsonify[i].objectName).style.color = rgblightblue;
                                        }
                                        valuechecked = true;
                                    }
                                }
                            }
                        }
                        if (!valuechecked) {
                            if (!isMultipleDevice) {
                                if (document.getElementById("header_" + jsonify[i].objectName))
                                    document.getElementById("header_" + jsonify[i].objectName).removeAttribute("style");

                                if (document.getElementById("sphead_" + jsonify[i].objectName))
                                    document.getElementById("sphead_" + jsonify[i].objectName).removeAttribute("style");

                                if (document.getElementById("spheaduom_" + jsonify[i].objectName))
                                    document.getElementById("spheaduom_" + jsonify[i].objectName).removeAttribute("style");

                                if (document.getElementById("spheadstatus_" + jsonify[i].objectName))
                                    document.getElementById("spheadstatus_" + jsonify[i].objectName).removeAttribute("style");
                            }
                        }
                    }

                    currval = 0, valuechecked = false;
                    if (jsonify[i].charttype.toLowerCase().indexOf("trend") >= 0) {
                        if (jsonify[i].showinheader == '1') {
                            var isMultipleDevice = false;
                            for (var cnt = 0; cnt < headerObj.length; cnt ++)
                            {
                                if (jsonify[i].objectName == headerObj[cnt].name && headerObj[cnt].count > 1)
                                    isMultipleDevice = true;
                            }

                            if (!isMultipleDevice) {
                                currval = parseFloat(jsonify[i].currvalue);
                                if (jsonify[i].plotbands.length > 0) {
                                    if (currval < parseFloat(jsonify[i].plotbands[0].from) || currval > parseFloat(jsonify[i].plotbands[jsonify[i].plotbands.length - 1].to)) {
                                        if (document.getElementById("header_" + jsonify[i].objectName))
                                            document.getElementById("header_" + jsonify[i].objectName).style.backgroundColor = red;

                                        if (document.getElementById("sphead_" + jsonify[i].objectName))
                                            document.getElementById("sphead_" + jsonify[i].objectName).style.color = rgblightblue;

                                        if (document.getElementById("spheaduom_" + jsonify[i].objectName))
                                            document.getElementById("spheaduom_" + jsonify[i].objectName).style.color = rgblightblue;

                                        if (document.getElementById("spheadstatus_" + jsonify[i].objectName))
                                            document.getElementById("spheadstatus_" + jsonify[i].objectName).style.color = rgblightblue;
                                        valuechecked = true;
                                    }

                                    if (!valuechecked) {
                                        if (currval >= parseFloat(jsonify[i].plotbands[0].from) && currval <= parseFloat(jsonify[i].plotbands[0].to)) {
                                            if (document.getElementById("header_" + jsonify[i].objectName))
                                                document.getElementById("header_" + jsonify[i].objectName).style.backgroundColor = jsonify[i].plotbands[jsonify[i].plotbands[0]].color.trim();

                                            if (document.getElementById("sphead_" + jsonify[i].objectName))
                                                document.getElementById("sphead_" + jsonify[i].objectName).style.color = rgblightblue;

                                            if (document.getElementById("spheaduom_" + jsonify[i].objectName))
                                                document.getElementById("spheaduom_" + jsonify[i].objectName).style.color = rgblightblue;

                                            if (document.getElementById("spheadstatus_" + jsonify[i].objectName))
                                                document.getElementById("spheadstatus_" + jsonify[i].objectName).style.color = rgblightblue;
                                            valuechecked = true;
                                        }
                                    }

                                    if (!valuechecked) {
                                        if (currval >= parseFloat(jsonify[i].plotbands[jsonify[i].plotbands.length - 1].from) && currval <= parseFloat(jsonify[i].plotbands[jsonify[i].plotbands.length - 1].to)) {
                                            if (document.getElementById("header_" + jsonify[i].objectName))
                                                document.getElementById("header_" + jsonify[i].objectName).style.backgroundColor = jsonify[i].plotbands[jsonify[i].plotbands.length - 1].color.trim();

                                            if (document.getElementById("sphead_" + jsonify[i].objectName))
                                                document.getElementById("sphead_" + jsonify[i].objectName).style.color = rgblightblue;

                                            if (document.getElementById("spheaduom_" + jsonify[i].objectName))
                                                document.getElementById("spheaduom_" + jsonify[i].objectName).style.color = rgblightblue;

                                            if (document.getElementById("spheadstatus_" + jsonify[i].objectName))
                                                document.getElementById("spheadstatus_" + jsonify[i].objectName).style.color = rgblightblue;
                                            valuechecked = true;
                                        }
                                    }
                                }
                                else if (jsonify[i].plotmin != 0 || jsonify[i].plotmax != 0) {
                                    if (jsonify[i].currvalue < jsonify[i].plotmin || jsonify[i].currvalue > jsonify[i].plotmax) {
                                        document.getElementById("header_" + jsonify[i].objectName).style.backgroundColor = red;

                                        if (document.getElementById("sphead_" + jsonify[i].objectName))
                                            document.getElementById("sphead_" + jsonify[i].objectName).style.color = rgblightblue;

                                        if (document.getElementById("spheaduom_" + jsonify[i].objectName))
                                            document.getElementById("spheaduom_" + jsonify[i].objectName).style.color = rgblightblue;

                                        if (document.getElementById("spheadstatus_" + jsonify[i].objectName))
                                            document.getElementById("spheadstatus_" + jsonify[i].objectName).style.color = rgblightblue;
                                        valuechecked = true;
                                    }
                                }
                                if (!valuechecked) {
                                    if (document.getElementById("header_" + jsonify[i].objectName))
                                        document.getElementById("header_" + jsonify[i].objectName).style.backgroundColor = green;

                                    if (document.getElementById("sphead_" + jsonify[i].objectName))
                                        document.getElementById("sphead_" + jsonify[i].objectName).removeAttribute("style");

                                    if (document.getElementById("spheaduom_" + jsonify[i].objectName))
                                        document.getElementById("spheaduom_" + jsonify[i].objectName).removeAttribute("style");

                                    if (document.getElementById("spheadstatus_" + jsonify[i].objectName))
                                        document.getElementById("spheadstatus_" + jsonify[i].objectName).removeAttribute("style");
                                }
                            }
                        }
                    }

                    currval = 0, valuechecked = false;
                    if (jsonify[i].charttype.toLowerCase().indexOf("value") >= 0) {
                        if (jsonify[i].showinheader == '1') {
                            currval = parseFloat(jsonify[i].currvalue);
                            if (jsonify[i].plotbands.length > 0) {
                                if (currval < parseFloat(jsonify[i].plotbands[0].from) || currval > parseFloat(jsonify[i].plotbands[jsonify[i].plotbands.length - 1].to)) {
                                    if (document.getElementById("header_" + jsonify[i].objectName))
                                        document.getElementById("header_" + jsonify[i].objectName).style.backgroundColor = red;

                                    if (document.getElementById("sphead_" + jsonify[i].objectName))
                                        document.getElementById("sphead_" + jsonify[i].objectName).style.color = rgblightblue;

                                    if (document.getElementById("spheaduom_" + jsonify[i].objectName))
                                        document.getElementById("spheaduom_" + jsonify[i].objectName).style.color = rgblightblue;

                                    if (document.getElementById("spheadstatus_" + jsonify[i].objectName))
                                        document.getElementById("spheadstatus_" + jsonify[i].objectName).style.color = rgblightblue;
                                    valuechecked = true;
                                }

                                if (!valuechecked) {
                                    if (currval >= parseFloat(jsonify[i].plotbands[0].from) && currval <= parseFloat(jsonify[i].plotbands[0].to)) {
                                        if (document.getElementById("header_" + jsonify[i].objectName))
                                            document.getElementById("header_" + jsonify[i].objectName).style.backgroundColor = jsonify[i].plotbands[jsonify[i].plotbands[0]].color.trim();

                                            if (document.getElementById("sphead_" + jsonify[i].objectName))
                                                document.getElementById("sphead_" + jsonify[i].objectName).style.color = rgblightblue;

                                        if (document.getElementById("spheaduom_" + jsonify[i].objectName))
                                            document.getElementById("spheaduom_" + jsonify[i].objectName).style.color = rgblightblue;

                                        if (document.getElementById("spheadstatus_" + jsonify[i].objectName))
                                            document.getElementById("spheadstatus_" + jsonify[i].objectName).style.color = rgblightblue;
                                        valuechecked = true;
                                    }
                                }

                                if (!valuechecked) {
                                    if (currval >= parseFloat(jsonify[i].plotbands[jsonify[i].plotbands.length - 1].from) && currval <= parseFloat(jsonify[i].plotbands[jsonify[i].plotbands.length - 1].to)) {
                                        if (document.getElementById("header_" + jsonify[i].objectName))
                                            document.getElementById("header_" + jsonify[i].objectName).style.backgroundColor = jsonify[i].plotbands[jsonify[i].plotbands.length - 1].color.trim();

                                        if (document.getElementById("sphead_" + jsonify[i].objectName))
                                            document.getElementById("sphead_" + jsonify[i].objectName).style.color = rgblightblue;

                                        if (document.getElementById("spheaduom_" + jsonify[i].objectName))
                                            document.getElementById("spheaduom_" + jsonify[i].objectName).style.color = rgblightblue;

                                        if (document.getElementById("spheadstatus_" + jsonify[i].objectName))
                                            document.getElementById("spheadstatus_" + jsonify[i].objectName).style.color = rgblightblue;
                                        valuechecked = true;
                                    }
                                }
                            }
                            else if (jsonify[i].plotmin != 0 || jsonify[i].plotmax != 0) {
                                if (jsonify[i].currvalue < jsonify[i].plotmin || jsonify[i].currvalue > jsonify[i].plotmax) {
                                    document.getElementById("header_" + jsonify[i].objectName).style.backgroundColor = red;

                                    if (document.getElementById("sphead_" + jsonify[i].objectName))
                                        document.getElementById("sphead_" + jsonify[i].objectName).style.color = rgblightblue;

                                    if (document.getElementById("spheaduom_" + jsonify[i].objectName))
                                        document.getElementById("spheaduom_" + jsonify[i].objectName).style.color = rgblightblue;

                                    if (document.getElementById("spheadstatus_" + jsonify[i].objectName))
                                        document.getElementById("spheadstatus_" + jsonify[i].objectName).style.color = rgblightblue;
                                    valuechecked = true;
                                }
                            }

                            if (!valuechecked) {
                                if (document.getElementById("header_" + jsonify[i].objectName)) {
                                    // document.getElementById("header_" + jsonify[i].objectName).removeAttribute("style");
                                    document.getElementById("header_" + jsonify[i].objectName).style.backgroundColor = green;

                                    if (document.getElementById("sphead_" + jsonify[i].objectName))
                                        document.getElementById("sphead_" + jsonify[i].objectName).removeAttribute("style");

                                    if (document.getElementById("spheaduom_" + jsonify[i].objectName))
                                        document.getElementById("spheaduom_" + jsonify[i].objectName).removeAttribute("style");

                                    if (document.getElementById("spheadstatus_" + jsonify[i].objectName))
                                        document.getElementById("spheadstatus_" + jsonify[i].objectName).removeAttribute("style");
                                }
                            }
                        }
                    }
                }
            }
            document.querySelector("#preloader").style.visibility = "hidden";
        }
        });

        function autoRefresh() {
            $.ajax({
                type: "GET",
                url : 'device/fetchdata',
                dataType: "json",
                error: function() {},
                success: function (json) {console.log(json);
                    if (document.getElementById("hdnChartCount").value == 0 || json.recordStatus.toLowerCase() == 'offline')
                            window.location.reload(true);
                    document.getElementById("spMyDash").innerHTML = json.recordStatus;
                    if (json.recordStatus.toLowerCase() == 'online') {
                        document.getElementById("header_mydash").style.backgroundColor  = green;

                        // Set header
                        headerObj = [];
                        for (var i = 0; i < json.headerObjectNameList.length; i++) {
                            document.getElementById("sphead_" + json.headerObjectNameList[i].name).innerHTML = json.headerObjectNameList[i].activevalue + "/" + json.headerObjectNameList[i].count;
                            if ((json.headerObjectNameList[i].name.toLowerCase().indexOf("room") >= 0) || (json.headerObjectNameList[i].name.toLowerCase().indexOf("active") >= 0))
                            {
                                // document.getElementById("sphead_" + json.headerObjectNameList[i].name).innerHTML = json.headerObjectNameList[i].presentvalue;
                                document.getElementById("sphead_" + json.headerObjectNameList[i].name).innerHTML = parseFloat(json.headerObjectNameList[i].currvalue).toFixed(2);
                                document.getElementById("spheaduom_" + json.headerObjectNameList[i].name).innerHTML = json.headerObjectNameList[i].uom;
                                if (json.headerObjectNameList[i].activevalue == json.headerObjectNameList[i].count) {
                                    document.getElementById("header_" + json.headerObjectNameList[i].name).style.backgroundColor  = green;
                                    document.getElementById("sphead_" + json.headerObjectNameList[i].name).removeAttribute("style");
                                    if (document.getElementById("spheaduom_" + json.headerObjectNameList[i].name))
                                        document.getElementById("spheaduom_" + json.headerObjectNameList[i].name).removeAttribute("style");

                                    if (document.getElementById("spheadstatus_" + json.headerObjectNameList[i].name))
                                        document.getElementById("spheadstatus_" + json.headerObjectNameList[i].name).removeAttribute("style");
                                }
                                else {
                                    document.getElementById("header_" + json.headerObjectNameList[i].name).style.backgroundColor  = red;
                                    document.getElementById("sphead_" + json.headerObjectNameList[i].name).style.color = rgblightblue;
                                    if (document.getElementById("spheaduom_" + json.headerObjectNameList[i].name))
                                        document.getElementById("spheaduom_" + json.headerObjectNameList[i].name).style.color = rgblightblue;

                                    if (document.getElementById("spheadstatus_" + json.headerObjectNameList[i].name))
                                        document.getElementById("spheadstatus_" + json.headerObjectNameList[i].name).style.color = rgblightblue;
                                }
                            }
                            else {
                                if (json.headerObjectNameList[i].activevalue == json.headerObjectNameList[i].count) {
                                    document.getElementById("spheadstatus_" + json.headerObjectNameList[i].name).innerHTML = "NORMAL";
                                    document.getElementById("header_" + json.headerObjectNameList[i].name).removeAttribute("style");

                                    document.getElementById("sphead_" + json.headerObjectNameList[i].name).removeAttribute("style");
                                    if (document.getElementById("spheaduom_" + json.headerObjectNameList[i].name))
                                        document.getElementById("spheaduom_" + json.headerObjectNameList[i].name).removeAttribute("style");

                                    if (document.getElementById("spheadstatus_" + json.headerObjectNameList[i].name))
                                        document.getElementById("spheadstatus_" + json.headerObjectNameList[i].name).removeAttribute("style");
                                }
                                else {
                                    document.getElementById("spheadstatus_" + json.headerObjectNameList[i].name).innerHTML = "ALERT";
                                    document.getElementById("header_" + json.headerObjectNameList[i].name).style.backgroundColor  = red;
                                    document.getElementById("sphead_" + json.headerObjectNameList[i].name).style.color = rgblightblue;
                                    if (document.getElementById("spheaduom_" + json.headerObjectNameList[i].name))
                                        document.getElementById("spheaduom_" + json.headerObjectNameList[i].name).style.color = rgblightblue;

                                    if (document.getElementById("spheadstatus_" + json.headerObjectNameList[i].name))
                                        document.getElementById("spheadstatus_" + json.headerObjectNameList[i].name).style.color = rgblightblue;
                                }
                            }
                            headerObj.push({name: json.headerObjectNameList[i].name, count: json.headerObjectNameList[i].count});
                        }
                        // Set header ends

                        var jsonify = JSON.parse(json.allChartArray);
                        var currval = 0, valuechecked = false;
                        for (var i = 0; i < jsonify.length; i++) {
                            if (jsonify[i].objectName.toLowerCase().indexOf("room") >= 0 || jsonify[i].charttype.toLowerCase().indexOf("trend") >= 0) {
                                if (jsonify[i].objectName.toLowerCase().indexOf("room") >= 0) {
                                    currval = parseFloat(jsonify[i].currvalue);
                                    if (jsonify[i].showinheader == '1') {
                                        if (hrs >= 10 && hrs < 20) {
                                            if (jsonify[i].plotbands.length > 0) {
                                                if (currval < parseFloat(jsonify[i].plotbands[0].from) || currval > parseFloat(jsonify[i].plotbands[jsonify[i].plotbands.length - 1].to)) {
                                                    if (document.getElementById("header_" + jsonify[i].objectName))
                                                        document.getElementById("header_" + jsonify[i].objectName).style.backgroundColor = red;

                                                    document.getElementById("sphead_" + jsonify[i].objectName).style.color = rgblightblue;
                                                    if (document.getElementById("spheaduom_" + jsonify[i].objectName))
                                                        document.getElementById("spheaduom_" + jsonify[i].objectName).style.color = rgblightblue;

                                                    if (document.getElementById("spheadstatus_" + jsonify[i].objectName))
                                                        document.getElementById("spheadstatus_" + jsonify[i].objectName).style.color = rgblightblue;
                                                    valuechecked = true;
                                                }

                                                if (!valuechecked) {
                                                    if (currval >= parseFloat(jsonify[i].plotbands[0].from) && currval <= parseFloat(jsonify[i].plotbands[0].to)) {
                                                        if (document.getElementById("header_" + jsonify[i].objectName))
                                                            document.getElementById("header_" + jsonify[i].objectName).style.backgroundColor = jsonify[i].plotbands[jsonify[i].plotbands[0]].color.trim();

                                                        document.getElementById("sphead_" + jsonify[i].objectName).style.color = rgblightblue;
                                                        if (document.getElementById("spheaduom_" + jsonify[i].objectName))
                                                            document.getElementById("spheaduom_" + jsonify[i].objectName).style.color = rgblightblue;

                                                        if (document.getElementById("spheadstatus_" + jsonify[i].objectName))
                                                            document.getElementById("spheadstatus_" + jsonify[i].objectName).style.color = rgblightblue;
                                                        valuechecked = true;
                                                    }
                                                }

                                                if (!valuechecked) {
                                                    if (currval >= parseFloat(jsonify[i].plotbands[jsonify[i].plotbands.length - 1].from) && currval <= parseFloat(jsonify[i].plotbands[jsonify[i].plotbands.length - 1].to)) {
                                                        if (document.getElementById("header_" + jsonify[i].objectName))
                                                            document.getElementById("header_" + jsonify[i].objectName).style.backgroundColor = jsonify[i].plotbands[jsonify[i].plotbands.length - 1].color.trim();

                                                        document.getElementById("sphead_" + jsonify[i].objectName).style.color = rgblightblue;
                                                        if (document.getElementById("spheaduom_" + jsonify[i].objectName))
                                                            document.getElementById("spheaduom_" + jsonify[i].objectName).style.color = rgblightblue;

                                                        if (document.getElementById("spheadstatus_" + jsonify[i].objectName))
                                                            document.getElementById("spheadstatus_" + jsonify[i].objectName).style.color = rgblightblue;
                                                        valuechecked = true;
                                                    }
                                                }
                                            }
                                        }
                                        if (!valuechecked) {
                                            if (document.getElementById("header_" + jsonify[i].objectName))
                                                document.getElementById("header_" + jsonify[i].objectName).style.backgroundColor  = green;

                                            document.getElementById("sphead_" + jsonify[i].objectName).removeAttribute("style");
                                            if (document.getElementById("spheaduom_" + jsonify[i].objectName))
                                                document.getElementById("spheaduom_" + jsonify[i].objectName).removeAttribute("style");

                                            if (document.getElementById("spheadstatus_" + jsonify[i].objectName))
                                                document.getElementById("spheadstatus_" + jsonify[i].objectName).removeAttribute("style");
                                        }
                                    }
                                }
                            }

                            currval = 0, valuechecked = false;
                            if (jsonify[i].charttype.toLowerCase().indexOf("gauge") >= 0) {
                                if (jsonify[i].objectName.toLowerCase().indexOf("room") >= 0) continue;

                                currval = parseFloat(jsonify[i].currvalue);
                                var isMultipleDevice = false;
                                for (var cnt = 0; cnt < headerObj.length; cnt ++)
                                {
                                    if (jsonify[i].objectName == headerObj[cnt].name && headerObj[cnt].count > 1)
                                        isMultipleDevice = true;
                                }

                                if (jsonify[i].showinheader == '1') {
                                    // currval = parseFloat(jsonify[i].currvalue);
                                    currval = parseFloat(jsonify[i].roundedData);;
                                    if (jsonify[i].plotbands.length > 0) {
                                        if (currval < parseFloat(jsonify[i].plotbands[0].from) || currval > parseFloat(jsonify[i].plotbands[jsonify[i].plotbands.length - 1].to)) {
                                            if (!isMultipleDevice) {
                                                if (document.getElementById("header_" + jsonify[i].objectName))
                                                    document.getElementById("header_" + jsonify[i].objectName).style.backgroundColor = red;

                                                if (document.getElementById("sphead_" + jsonify[i].objectName))
                                                    document.getElementById("sphead_" + jsonify[i].objectName).style.color = rgblightblue;

                                                if (document.getElementById("spheaduom_" + jsonify[i].objectName))
                                                    document.getElementById("spheaduom_" + jsonify[i].objectName).style.color = rgblightblue;

                                                if (document.getElementById("spheadstatus_" + jsonify[i].objectName))
                                                    document.getElementById("spheadstatus_" + jsonify[i].objectName).style.color = rgblightblue;
                                            }
                                            valuechecked = true;
                                        }

                                        if (!valuechecked) {
                                            if (currval >= parseFloat(jsonify[i].plotbands[0].from) && currval <= parseFloat(jsonify[i].plotbands[0].to)) {
                                                if (!isMultipleDevice) {
                                                    if (document.getElementById("header_" + jsonify[i].objectName))
                                                        document.getElementById("header_" + jsonify[i].objectName).style.backgroundColor = jsonify[i].plotbands[jsonify[i].plotbands[0]].color.trim();

                                                    if (document.getElementById("sphead_" + jsonify[i].objectName))
                                                        document.getElementById("sphead_" + jsonify[i].objectName).style.color = rgblightblue;

                                                    if (document.getElementById("spheaduom_" + jsonify[i].objectName))
                                                        document.getElementById("spheaduom_" + jsonify[i].objectName).style.color = rgblightblue;

                                                    if (document.getElementById("spheadstatus_" + jsonify[i].objectName))
                                                        document.getElementById("spheadstatus_" + jsonify[i].objectName).style.color = rgblightblue;
                                                }
                                                valuechecked = true;
                                            }
                                        }

                                        if (!valuechecked) {
                                            if (currval >= parseFloat(jsonify[i].plotbands[jsonify[i].plotbands.length - 1].from) && currval <= parseFloat(jsonify[i].plotbands[jsonify[i].plotbands.length - 1].to)) {
                                                if (!isMultipleDevice) {
                                                    if (document.getElementById("header_" + jsonify[i].objectName))
                                                        document.getElementById("header_" + jsonify[i].objectName).style.backgroundColor = jsonify[i].plotbands[jsonify[i].plotbands.length - 1].color.trim();

                                                    if (document.getElementById("sphead_" + jsonify[i].objectName))
                                                        document.getElementById("sphead_" + jsonify[i].objectName).style.color = rgblightblue;

                                                    if (document.getElementById("spheaduom_" + jsonify[i].objectName))
                                                        document.getElementById("spheaduom_" + jsonify[i].objectName).style.color = rgblightblue;

                                                    if (document.getElementById("spheadstatus_" + jsonify[i].objectName))
                                                        document.getElementById("spheadstatus_" + jsonify[i].objectName).style.color = rgblightblue;
                                                }
                                                valuechecked = true;
                                            }
                                        }
                                    }
                                    if (!valuechecked) {
                                        if (!isMultipleDevice) {
                                            if (document.getElementById("header_" + jsonify[i].objectName))
                                                document.getElementById("header_" + jsonify[i].objectName).removeAttribute("style");

                                            if (document.getElementById("sphead_" + jsonify[i].objectName))
                                                document.getElementById("sphead_" + jsonify[i].objectName).removeAttribute("style");

                                            if (document.getElementById("spheaduom_" + jsonify[i].objectName))
                                                document.getElementById("spheaduom_" + jsonify[i].objectName).removeAttribute("style");

                                            if (document.getElementById("spheadstatus_" + jsonify[i].objectName))
                                                document.getElementById("spheadstatus_" + jsonify[i].objectName).removeAttribute("style");
                                        }
                                    }
                                }
                            }

                            currval = 0, valuechecked = false;
                            if (jsonify[i].charttype.toLowerCase().indexOf("trend") >= 0) {
                                currval = parseFloat(jsonify[i].currvalue);
                                if (jsonify[i].showinheader == '1') {
                                    currval = parseFloat(jsonify[i].currvalue);
                                    if (jsonify[i].plotbands.length > 0) {
                                        if (currval < parseFloat(jsonify[i].plotbands[0].from) || currval > parseFloat(jsonify[i].plotbands[jsonify[i].plotbands.length - 1].to)) {
                                            if (document.getElementById("header_" + jsonify[i].objectName))
                                                document.getElementById("header_" + jsonify[i].objectName).style.backgroundColor = red;

                                            if (document.getElementById("sphead_" + jsonify[i].objectName))
                                                document.getElementById("sphead_" + jsonify[i].objectName).style.color = rgblightblue;

                                            if (document.getElementById("spheaduom_" + jsonify[i].objectName))
                                                document.getElementById("spheaduom_" + jsonify[i].objectName).style.color = rgblightblue;

                                            if (document.getElementById("spheadstatus_" + jsonify[i].objectName))
                                                document.getElementById("spheadstatus_" + jsonify[i].objectName).style.color = rgblightblue;
                                            valuechecked = true;
                                        }

                                        if (!valuechecked) {
                                            if (currval >= parseFloat(jsonify[i].plotbands[0].from) && currval <= parseFloat(jsonify[i].plotbands[0].to)) {
                                                if (document.getElementById("header_" + jsonify[i].objectName))
                                                    document.getElementById("header_" + jsonify[i].objectName).style.backgroundColor = jsonify[i].plotbands[jsonify[i].plotbands[0]].color.trim();

                                                if (document.getElementById("sphead_" + jsonify[i].objectName))
                                                    document.getElementById("sphead_" + jsonify[i].objectName).style.color = rgblightblue;

                                                if (document.getElementById("spheaduom_" + jsonify[i].objectName))
                                                    document.getElementById("spheaduom_" + jsonify[i].objectName).style.color = rgblightblue;

                                                if (document.getElementById("spheadstatus_" + jsonify[i].objectName))
                                                    document.getElementById("spheadstatus_" + jsonify[i].objectName).style.color = rgblightblue;
                                                valuechecked = true;
                                            }
                                        }

                                        if (!valuechecked) {
                                            if (currval >= parseFloat(jsonify[i].plotbands[jsonify[i].plotbands.length - 1].from) && currval <= parseFloat(jsonify[i].plotbands[jsonify[i].plotbands.length - 1].to)) {
                                                if (document.getElementById("header_" + jsonify[i].objectName))
                                                    document.getElementById("header_" + jsonify[i].objectName).style.backgroundColor = jsonify[i].plotbands[jsonify[i].plotbands.length - 1].color.trim();

                                                if (document.getElementById("sphead_" + jsonify[i].objectName))
                                                    document.getElementById("sphead_" + jsonify[i].objectName).style.color = rgblightblue;

                                                if (document.getElementById("spheaduom_" + jsonify[i].objectName))
                                                    document.getElementById("spheaduom_" + jsonify[i].objectName).style.color = rgblightblue;

                                                if (document.getElementById("spheadstatus_" + jsonify[i].objectName))
                                                    document.getElementById("spheadstatus_" + jsonify[i].objectName).style.color = rgblightblue;
                                                valuechecked = true;
                                            }
                                        }
                                    }
                                    else if (jsonify[i].plotmin != 0 || jsonify[i].plotmax != 0) {
                                        if (jsonify[i].currvalue < jsonify[i].plotmin || jsonify[i].currvalue > jsonify[i].plotmax) {
                                            document.getElementById("header_" + jsonify[i].objectName).style.backgroundColor = red;

                                            if (document.getElementById("sphead_" + jsonify[i].objectName))
                                                document.getElementById("sphead_" + jsonify[i].objectName).style.color = rgblightblue;

                                            if (document.getElementById("spheaduom_" + jsonify[i].objectName))
                                                document.getElementById("spheaduom_" + jsonify[i].objectName).style.color = rgblightblue;

                                            if (document.getElementById("spheadstatus_" + jsonify[i].objectName))
                                                document.getElementById("spheadstatus_" + jsonify[i].objectName).style.color = rgblightblue;
                                            valuechecked = true;
                                        }
                                    }
                                    if (!valuechecked) {
                                        if (document.getElementById("header_" + jsonify[i].objectName))
                                            document.getElementById("header_" + jsonify[i].objectName).style.backgroundColor = green;

                                        if (document.getElementById("sphead_" + jsonify[i].objectName))
                                            document.getElementById("sphead_" + jsonify[i].objectName).removeAttribute("style");

                                        if (document.getElementById("spheaduom_" + jsonify[i].objectName))
                                            document.getElementById("spheaduom_" + jsonify[i].objectName).removeAttribute("style");

                                        if (document.getElementById("spheadstatus_" + jsonify[i].objectName))
                                            document.getElementById("spheadstatus_" + jsonify[i].objectName).removeAttribute("style");
                                    }
                                }
                            }

                            currval = 0, valuechecked = false;
                            if (jsonify[i].charttype.toLowerCase().indexOf("value") >= 0) {
                                if (jsonify[i].showinheader == '1') {
                                    currval = parseFloat(jsonify[i].currvalue);
                                    if (jsonify[i].plotbands.length > 0) {
                                        if (currval < parseFloat(jsonify[i].plotbands[0].from) || currval > parseFloat(jsonify[i].plotbands[jsonify[i].plotbands.length - 1].to)) {
                                            if (document.getElementById("header_" + jsonify[i].objectName))
                                                document.getElementById("header_" + jsonify[i].objectName).style.backgroundColor = red;

                                            if (document.getElementById("sphead_" + jsonify[i].objectName))
                                                document.getElementById("sphead_" + jsonify[i].objectName).style.color = rgblightblue;

                                            if (document.getElementById("spheaduom_" + jsonify[i].objectName))
                                                document.getElementById("spheaduom_" + jsonify[i].objectName).style.color = rgblightblue;

                                            if (document.getElementById("spheadstatus_" + jsonify[i].objectName))
                                                document.getElementById("spheadstatus_" + jsonify[i].objectName).style.color = rgblightblue;
                                            valuechecked = true;
                                        }

                                        if (!valuechecked) {
                                            if (currval >= parseFloat(jsonify[i].plotbands[0].from) && currval <= parseFloat(jsonify[i].plotbands[0].to)) {
                                                if (document.getElementById("header_" + jsonify[i].objectName))
                                                    document.getElementById("header_" + jsonify[i].objectName).style.backgroundColor = jsonify[i].plotbands[jsonify[i].plotbands[0]].color.trim();

                                                if (document.getElementById("sphead_" + jsonify[i].objectName))
                                                    document.getElementById("sphead_" + jsonify[i].objectName).style.color = rgblightblue;

                                                if (document.getElementById("spheaduom_" + jsonify[i].objectName))
                                                    document.getElementById("spheaduom_" + jsonify[i].objectName).style.color = rgblightblue;

                                                if (document.getElementById("spheadstatus_" + jsonify[i].objectName))
                                                    document.getElementById("spheadstatus_" + jsonify[i].objectName).style.color = rgblightblue;
                                                valuechecked = true;
                                            }
                                        }

                                        if (!valuechecked) {
                                            if (currval >= parseFloat(jsonify[i].plotbands[jsonify[i].plotbands.length - 1].from) && currval <= parseFloat(jsonify[i].plotbands[jsonify[i].plotbands.length - 1].to)) {
                                                if (document.getElementById("header_" + jsonify[i].objectName))
                                                    document.getElementById("header_" + jsonify[i].objectName).style.backgroundColor = jsonify[i].plotbands[jsonify[i].plotbands.length - 1].color.trim();

                                                if (document.getElementById("sphead_" + jsonify[i].objectName))
                                                    document.getElementById("sphead_" + jsonify[i].objectName).style.color = rgblightblue;

                                                if (document.getElementById("spheaduom_" + jsonify[i].objectName))
                                                    document.getElementById("spheaduom_" + jsonify[i].objectName).style.color = rgblightblue;

                                                if (document.getElementById("spheadstatus_" + jsonify[i].objectName))
                                                    document.getElementById("spheadstatus_" + jsonify[i].objectName).style.color = rgblightblue;
                                                valuechecked = true;
                                            }
                                        }
                                    }
                                    else if (jsonify[i].plotmin != 0 || jsonify[i].plotmax != 0) {
                                        if (jsonify[i].currvalue < jsonify[i].plotmin || jsonify[i].currvalue > jsonify[i].plotmax) {
                                            document.getElementById("header_" + jsonify[i].objectName).style.backgroundColor = red;

                                        if (document.getElementById("sphead_" + jsonify[i].objectName))
                                            document.getElementById("sphead_" + jsonify[i].objectName).style.color = rgblightblue;

                                        if (document.getElementById("spheaduom_" + jsonify[i].objectName))
                                            document.getElementById("spheaduom_" + jsonify[i].objectName).style.color = rgblightblue;

                                        if (document.getElementById("spheadstatus_" + jsonify[i].objectName))
                                            document.getElementById("spheadstatus_" + jsonify[i].objectName).style.color = rgblightblue;
                                            valuechecked = true;
                                        }
                                    }

                                    if (!valuechecked) {
                                        if (document.getElementById("header_" + jsonify[i].objectName)) {
                                            // document.getElementById("header_" + jsonify[i].objectName).removeAttribute("style");
                                            document.getElementById("header_" + jsonify[i].objectName).style.backgroundColor = green;

                                            if (document.getElementById("sphead_" + jsonify[i].objectName))
                                                document.getElementById("sphead_" + jsonify[i].objectName).removeAttribute("style");

                                            if (document.getElementById("spheaduom_" + jsonify[i].objectName))
                                                document.getElementById("spheaduom_" + jsonify[i].objectName).removeAttribute("style");

                                            if (document.getElementById("spheadstatus_" + jsonify[i].objectName))
                                                document.getElementById("spheadstatus_" + jsonify[i].objectName).removeAttribute("style");
                                        }
                                    }
                                }
                            }

                            if (jsonify[i].charttype.toLowerCase().indexOf("none") >= 0) continue;
                        }
                    }
                    else 
                        document.getElementById("header_mydash").style.backgroundColor  = red;
            },
            cache:false,
            });
        }

        // $('select[name="slc_range_type"]').bind('change', function() {
        //     var rangetype = jQuery(this).val();
        //     if (rangetype) {
        //         jQuery.ajax({
        //             url : '/getReportRangeInterval/' + rangetype,
        //             type : "GET",
        //             dataType : "json",
        //             success:function(data) {console.log(data);
        //                 jQuery('select[name="slc_interval"]').empty();
        //                 if (data != null) {
        //                     // $('select[name="slc_interval"]').append('<option value="">Select City*</option>');
        //                     jQuery.each(data, function(key, value){
        //                         $('select[name="slc_interval"]').append('<option value="'+ value.interval_value +'">'+ value.interval_name +'</option>');
        //                     });
        //                 }
        //             }
        //         });
        //     }
        //     else
        //     {
        //         $('select[name="slc_interval"]').empty();
        //     }
        //     clearChart();
        // });

        // $('select[name="slc_interval"]').bind('change', function() {
        //     clearChart();
        // });

        // $('select[name="slc_range_type"]').bind('click', function() {
        //     // document.getElementById("checkBoxes").style.display = "none";
        //     setMultiselectStyle();
        // });

        // $('select[name="slc_interval"]').bind('click', function() {
        //     // document.getElementById("checkBoxes").style.display = "none";
        //     setMultiselectStyle();
        // });

        $('a').bind('click', function() {
            // document.getElementById("checkBoxes").style.display = "none";
            setMultiselectStyle();
        });

        function slcrangetype_clicked(id) {
            setMultiselectStyle();
        }

        function slcrangetype_changed(id) {
            clearChart(id);
        }

        // $('div').bind('click', function() {
        //     $inputs.not(this).prop('disabled', true);
        //     document.getElementById("checkBoxes").style.display = "none";
        //     show = true;
        // });

        var chartNoCount = document.getElementById("hdnChartNos").value;
        var showArray = [];
        for (var s = 0; s < parseInt(chartNoCount); s++)
            showArray.push(true);

        $.ajax({
            type: "GET",
            // url : 'device/drawgraph/' + $('select[name="slc_range_type"]')[0].value + '/' + $('select[name="slc_interval"]')[0].value,
            url : 'device/drawgraph/' + $('select[name="slc_range_type"]')[0].value,
            dataType: "json",
            error: function() {},
            success: function (json) {console.log(json);
                for (var s = 1; s <= parseInt(chartNoCount); s++) {
                    var splineoptions = {
                        chart: {
                            renderTo: 'trendchart_' + s,
                            type: 'spline',
                            backgroundColor: json.chartColorList[s - 1].chartcolor,
                        },
                        credits: { enabled: false },
                        title: {
                            text: '',
                            align: 'left'
                        },
                        xAxis: {
                            gridLineWidth: 1,
                            labels: {
                                step: 30,
                                style: {
                                    fontSize: '10px'
                                },
                                rotation: 315
                            },
                            title: {
                                text: 'Time'
                            },
                            categories: []
                        },
                        yAxis: {
                            gridLineWidth: 1,
                            title: {
                                text: ''
                            },
                            allowDecimals: false,
                        },
                        legend: {
                            enabled: false
                        },
                        series: [{
                            type: 'line',
                            name: 'Random data',
                            data: []
                        }],
                        lang: {
                            noData: "Select above criteria to view or export trends"
                        },
                        noData: {
                            style: {
                                fontWeight: 'bold',
                                fontSize: '15px',
                                color: '#303030'
                            }
                        },
                        plotOptions: {
                            spline: {
                                shadow: true,
                            }
                        },
                        exporting: { 
                            enabled: false,
                            buttons: {
                                contextButton: {
                                    menuItems: ['viewFullscreen', 'exitFullscreen']
                                }
                            }
                        },
                    };
                    var chart = new Highcharts.Chart(splineoptions);
                    chart.reflow();
                    chart.series[0].remove();
                }
            }
        });

        var deviceIds = [];
        function displaygraph(event, devid) {
            event.preventDefault();

            var charts = Highcharts.charts;
            if (deviceIds.length > 0 && deviceIds.indexOf(devid) >= 0) {
                var arrayIndex = deviceIds.indexOf(devid);
                if (arrayIndex > -1)
                    deviceIds.splice(arrayIndex, 1);

                devname = devid.replace("btn_", "");
                series = charts[0].get(devname);
                if(series)
                    series.remove();
            }
            else {
                deviceIds.push(devid);
                $.ajax({
                    type: "GET",
                    // url : 'device/drawgraphdata/' + devid + '/' + $('select[name="slc_range_type"]')[0].value + '/' + $('select[name="slc_interval"]')[0].value,
                    url : 'device/drawgraphdata/' + devid + '/' + $('select[name="slc_range_type"]')[0].value,
                    dataType: "json",
                    error: function() {},
                    success: function (json) {console.log(json);
                        var timeArray = [], val1 = [], devname = '', devactualname = '';
                        var rangetype = $('select[name="slc_range_type"]')[0].value;
                        var index = 0, res = '';
                        devname = devid.replace("btn_", "");
                        devactualname = devname.replaceAll("_", " ");

                        if (rangetype != "Today")
                            timeArray.push(json.timeArr);

                        for (var i = 0; i < json.dataArr.length; i++) {
                            if (rangetype == "Today") {
                                index = json.dataArr[i].status_hour.indexOf(" ");
                                res = json.dataArr[i].status_hour.substring(index + 1);
                                lastindex = res.lastIndexOf(":");
                                res = res.substring(0, lastindex);
                                timeArray.push(res);
                            }
                            val1.push({name: json.dataArr[i].status_date, y: parseFloat(json.dataArr[i].average)});
                        }

                        xaxisstep = 1;
                        if (json.dataArr.length >= 2500) 
                            xaxisstep = 150;
                        else if (json.dataArr.length >= 2000) 
                            xaxisstep = 100;
                        else if (json.dataArr.length >= 1500) 
                            xaxisstep = 80;
                        else if (json.dataArr.length >= 1000) 
                            xaxisstep = 60;
                        else if (json.dataArr.length >= 500) 
                            xaxisstep = 40;
                        else if (json.dataArr.length >= 300) 
                            xaxisstep = 20;
                        else if (json.dataArr.length >= 100) 
                            xaxisstep = 10;
                        else if (json.dataArr.length >= 50) 
                            xaxisstep = 5;
                        else if (json.dataArr.length >= 20) 
                            xaxisstep = 2;

                        charts[0].xAxis[0].update({
                            categories: timeArray,
                            labels: {
                                step: xaxisstep,
                            }
                        });
                        charts[0].options.legend.enabled = true;
                        charts[0].addSeries({
                            id: devname,
                            name: devactualname,
                            data: val1
                        });
                    }
                });
            }
        }

        function clearChart(btnid) {
            var index = btnid.indexOf("_");
            var chartno = btnid.substring(index + 1);
            var charts = Highcharts.charts;
            var chartName = "trendchart_" + chartno;

            charts.forEach(function(chart, index) {                        
                if (chart.renderTo.id === chartName) {
                    while (chart.series.length) 
                        chart.series[0].remove();
                    chart.yAxis[0].update({
                            title: {
                                text: ''
                            }
                        });
                }
            });
            document.getElementById("trendsnodata_" + chartno).style.display = "block";
        }

        function getDeviceIds(btnid) {
            var index = btnid.indexOf("_");
            var chartno = btnid.substring(index + 1);
            var newDeviceIds = [];
            for (var s = 0; s < document.getElementsByName(chartno + "btn").length; s++) {
                var elementid = document.getElementsByName(chartno + "btn")[s];
                var index = elementid.id.indexOf("_");
                var devicename = elementid.id.substring(index + 1);
                if (elementid.checked) {                    
                    newDeviceIds.push(devicename);
                }
                else {
                    var arrayIndex = newDeviceIds.indexOf(devicename);
                    if (arrayIndex > -1)
                        newDeviceIds.splice(arrayIndex, 1);
                }
            }
            return newDeviceIds;
        }

        function displayTrends(btnid) {
            setMultiselectStyle();
            clearChart(btnid);
            var newDeviceIds = getDeviceIds(btnid);
            if (newDeviceIds.length == 0) {
                alert('Please select device(s) before displaying trends');
                return false;
            }
            var devicenames = '';
            for (var i = 0; i < newDeviceIds.length; i++)
                devicenames += newDeviceIds[i] + ",";

            var index = btnid.indexOf("_");
            var chartno = btnid.substring(index + 1);
            var rangetype = document.getElementById("slcrangetype_" + chartno).value;
            document.getElementById("trendsnodata_" + chartno).style.display = "none";

            $.ajax({
                type: "GET",
                // url : 'device/drawgraphdata/' + devid + '/' + $('select[name="slc_range_type"]')[0].value + '/' + $('select[name="slc_interval"]')[0].value,
                url : 'device/drawgraphdatanew/' + devicenames.slice(0, -1) + '/' + rangetype,
                dataType: "json",
                error: function() {},
                success: function (json) {console.log(json);
                    var timeArray = [], val1 = [], devname = '', devactualname = '';
                    var index = 0, res = '', uom = '';
                    if (rangetype != "Today") {
                        for (var i = 0; i < json.timeArr.length; i++) 
                            timeArray.push(json.timeArr[i]);
                    }

                    var timeArrayPushed = false;
                    for (var j = 0; j < newDeviceIds.length; j++) {
                        for (var i = 0; i < json.dataArr.length; i++) {
                            if (newDeviceIds[j] == json.dataArr[i].object_name) {
                                if (rangetype == "Today") {
                                    index = json.dataArr[i].status_hour.indexOf(" ");
                                    res = json.dataArr[i].status_hour.substring(index + 1);
                                    lastindex = res.lastIndexOf(":");
                                    res = res.substring(0, lastindex);
                                    timeArray.push(res);
                                }
                                uom = json.dataArr[i].device_uom;
                                timeArrayPushed = true;
                            }
                        }
                        if (timeArrayPushed) break;
                    }

                    var charts = Highcharts.charts;
                    var chartName = "trendchart_" + chartno;
                    xaxisstep = 1;
                    charts.forEach(function(chart, index) {                        
                        if (chart.renderTo.id === chartName) {
                            chart.xAxis[0].update({
                                categories: timeArray,
                                labels: {
                                    step: xaxisstep,
                                }
                            });
                            chart.options.legend.enabled = true;
                            chart.yAxis[0].update({
                                title: {
                                    text: uom
                                }
                            });
                        }
                    });

                    for (var j = 0; j < newDeviceIds.length; j++) {
                        val1 = [];
                        for (var i = 0; i < json.dataArr.length; i++) {
                            if (newDeviceIds[j] == json.dataArr[i].object_name) {
                                val1.push({name: json.dataArr[i].status_date, y: parseFloat(json.dataArr[i].average)});
                            }
                        }
                        devname = newDeviceIds[j].replace("btn_", "");
                        devactualname = devname.replaceAll("_", " ");
                        charts.forEach(function(chart, index) {                        
                            if (chart.renderTo.id === chartName) {
                                chart.addSeries({
                                    id: devname,
                                    name: devactualname,
                                    data: val1
                                });
                            }
                        });
                    }                    
                },
                complete:function(data) {
                    if (parseInt(chartno) > 1) {
                        $('html, body').animate({
                            // scrollTop: $("#div_" + chartno).offset({ top: $("#div_" + chartno).offset().top, left: $("#div_" + chartno).offset().left })
                            scrollTop: $("#div_" + chartno).offset().top
                        }, 1000);
                    }
                }
            });
        }

        function exportReport(btnid) {
            setMultiselectStyle();
            var newDeviceIds = getDeviceIds(btnid);
            if (newDeviceIds.length == 0) {
                alert('Please select device(s) before exporting trends');
                return false;
            }
            
            var devicenames = '';
            for (var i = 0; i < newDeviceIds.length; i++)
                devicenames += newDeviceIds[i] + ",";
            var index = btnid.indexOf("_");
            var chartno = btnid.substring(index + 1);
            var rangetype = document.getElementById("slcrangetype_" + chartno).value;
            // // window.open(window.location.href = 'reporttrends/' + devicename + '/' + $('select[name="slc_range_type"]')[0].value + '/' + $('select[name="slc_interval"]')[0].value);
            window.open(window.location.href = 'reporttrends/' + devicenames.slice(0, -1) + '/' + rangetype);
        }

        function setMultiselectStyle() {
            for(var i=0; i < document.getElementsByClassName('checkBoxes').length; i++)
                document.getElementsByClassName('checkBoxes')[i].style.display = "none";
            showArray = [];
            for (var s = 0; s < chartNoCount; s++)
                showArray.push(true);
        }

        function showCheckboxes(checkboxesid) {
            var index = checkboxesid.indexOf("_");
            var result = checkboxesid.substring(index + 1);

            if (showArray[result - 1]) {
                document.getElementById(checkboxesid).style.display = "block";
                showArray[result - 1] = false;
            } 
            else {
                document.getElementById(checkboxesid).style.display = "none";
                showArray[result - 1] = true;
            }
        }
    </script>
@endsection
