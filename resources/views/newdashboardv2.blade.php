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
        <a href="#{{ $chartObj['chartType'] }}ch_{{ $chartObj['name'] }}">
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
        </a>
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
    <!-- <div class="main-second-layer">
        <div class="row">
            <div class="col-sm-3">
                <div class="small-box2">
                    <div class="box-head2">
                        <div class="row">
                        <div class="col-sm-9 box-head-title">
                            <h5><span id="spBinOutputHead"></span></h5>
                        </div>
                        <div class="col-sm-3 box-head-icon pl-2">
                            <img class="icon" src="images/sun_ico.png" alt="">
                        </div>
                        </div>
                    </div>
                    <div class="box-body2">
                        <div id="dvBinOutputBody"></div>
                    </div>
                </div>
            </div>
            <div class="col-sm-3" id="gaugech_ROOM_TEMP">
                <div class="small-box2">
                    <div class="box-head2">
                        <div class="row">
                            <div class="col-sm-9 box-head-title">
                                <h5><span id="spRoomTemp"></span></h5>
                            </div>
                            <div class="col-sm-3 box-head-icon pl-2">
                                <img class="icon" src="images/tem_ico.png" alt="">
                            </div>
                        </div>
                    </div>
                    <div class="box-body2">
                        <div class="row split-ac">
                            <div class="col-sm-8 pr-0">
                                <p><span id="spRoomTempLoc"></span></p>
                            </div>
                            <div class="col-sm-4">
                                <img class="icon" src="images/bell_gr.png" alt="" id="imgRoomTemp">
                            </div>
                        </div>
                        <div class="chart-sect" id="gauge_room"></div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="small-box-graph">
                    <div class="row">
                        <div class="col-sm-12 graph-box" id="trend_room"></div>
                    </div>
                </div>
            </div>
        </div>   
    </div> -->
    
    <div class="main-third-layer">
        <div class="row" id="divParentGauge">
            @foreach($gaugeChartObjectNameList as $chartObj)
                <div class="col-sm-3" id="gaugech_{{ $chartObj['name'] }}">
                    <div class="small-box3">
                        <div class="box-head3">
                            <div class="row">
                                <div class="col-sm-12 box-head-title">
                                    <h5>{{ $chartObj['actualname'] }}</h5>
                                </div>
                            </div>
                        </div>
                        <div class="box-body3">
                            <div class="row row-sec">
                                <div class="col-sm-8 pr-0">
                                    <p>{{ $chartObj['deviceLocation'] }}</p>
                                </div>
                                <div class="col-sm-4">
                                    <img class="icon" src="images/bell_gr.png" alt="" id="gaugech_img_{{ $chartObj['name'] }}">
                                </div>
                            </div>
                            <div class="chart-sect" id="parentGauge_{{ $chartObj['name'] }}">

                            </div>
                            <div class="chart-value">
                                <h3><span id="spGaugeValue_{{ $chartObj['name'] }}"></span><span class="spuom" id="spGaugeUOM_{{ $chartObj['name'] }}"></span></h3>
                            </div>
                            <div class="chart-desc">
                                <p id="pGaugeDesc_{{ $chartObj['name'] }}"></p>
                            </div>
                            <div class="report-btn">
                                <a href="#" id="rpt_{{ $chartObj['name'] }}" onclick="reportDash(this.id);">View Report</a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach

            <div class="col-sm-3 mt-1" id="divParentValue">
                @foreach($valueChartObjectNameList as $chartObj)
                    <div class="small-box4" id="trendch_{{ $chartObj['name'] }}">
                        <div class="box-head4">
                            <div class="row">
                                <div class="col-sm-12 box-head-title">
                                    <h5>{{ $chartObj['actualname'] }}</h5>
                                </div>
                            </div>
                        </div>
                        <div class="box-body4">
                            <div class="chart-value">
                                <h3><span id="spValue_{{ $chartObj['name'] }}"></span> <small><span class="spuom" id="spValueUOM_{{ $chartObj['name'] }}"></span></small></h3>
                            </div>
                            <div class="chart-desc">
                                <p id="pValueDesc_{{ $chartObj['name'] }}"></p>
                            </div>
                            <div class="report-btn">
                            `   <a href="#" id="rpt_{{ $chartObj['name'] }}" onclick="reportDash(this.id);">View Report</a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="main-forth-layer">
        <div class="row">
            @foreach($trendChartObjectNameList as $chartObj)
                <div class="col-sm-3" id="trendch_{{ $chartObj['name'] }}">
                    <div class="small-box4">
                        <div class="box-head4">
                            <div class="row">
                                <div class="col-sm-9 box-head-title">
                                    <h5>{{ $chartObj['actualname'] }}</h5>
                                </div>
                                <div class="col-sm-3 box-head-icon">
                                    <img class="icon" src="{{ $chartObj['imgsrc'] }}" alt="">
                                </div>
                            </div>
                        </div>
                        <div class="box-body4 store">
                            <p class="energy"><span id="spValue_{{ $chartObj['name'] }}"></span><span class="spuom" id="spValueUOM_{{ $chartObj['name'] }}"></span></p>
                            <p id="pValueDesc_{{ $chartObj['name'] }}"></p>
                            <div class="report-btn">
                                <a href="#" id="rpt_{{ $chartObj['name'] }}" onclick="reportDash(this.id);">View Report</a>
                            </div>
                        </div>                        
                    </div>
                </div>
            @endforeach

            @foreach($trendChartObjectNameList as $chartObj)
                <div class="col-sm-6">
                    <div class="small-box-graph">
                        <div class="row">
                        <div class="col-sm-12 graph-box" id="dvGraph_{{ $chartObj['name'] }}">
                            <!-- <img src="images/graph2.jpg" alt="" style="width: 400px; height: 180px;"> -->
                        </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection

@section('script')
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/highcharts-more.js"></script>
    <script src="https://code.highcharts.com/modules/exporting.js"></script>
    <script src="https://code.highcharts.com/modules/export-data.js"></script>
    <script src="https://code.highcharts.com/modules/accessibility.js"></script>

    <script>
        // TimePicker
        var twelveHour = $('.timepicker-12-hr').wickedpicker({ title: ''});
        // twelveHour.wickedpicker('setTime', 0, "14:00"); // 0 is the index of the timepicker. Use 0 if only one

        var d = new Date();
        // var tday = d.getDate() + '-' + (d.getMonth() + 1) + '-' + d.getFullYear();
        var hrs = d.getHours();

        // Math.ceil(x/5)*5;

        var timeArray = [], headerObj = [];
        var refreshTime = 120000;
        clearInterval(refreshTime);
        refreshTime = document.getElementById("hdnRefreshTime").value;
        var autoupdate = setInterval(function() { autoRefresh(); }, refreshTime);
        var red = '#c23927', green = '#00a652', white = '#ffffff', blue = '#0065b3';
        var rgbRed = "rgb(194, 57, 39)", rgbGreen = "rgb(0, 166, 82)", rgblightblue = "rgb(231, 232, 234)", rgbwhite = "rgb(255, 255, 255)";
        // containerBinOut = document.createElement('div');
        // containerBinOut.setAttribute("id", "divBinOutput");

        var a = setInterval(function () { blinkHeaderColor() }, 500);
        function blinkHeaderColor() {
            if (document.getElementById("header_mydash").style.backgroundColor == red || document.getElementById("header_mydash").style.backgroundColor == rgbRed)
                document.getElementById("header_mydash").style.backgroundColor = rgblightblue;
            else if (document.getElementById("header_mydash").style.backgroundColor == rgblightblue)
                document.getElementById("header_mydash").style.backgroundColor = red;

            if (document.getElementById("spMyDash").style.color == red || document.getElementById("spMyDash").style.color == rgbRed)
                document.getElementById("spMyDash").style.color = rgblightblue;
            else if (document.getElementById("spMyDash").style.color == rgblightblue)
                document.getElementById("spMyDash").style.color = red;                
            
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
                    if (json.weatherArray[i].status.toLowerCase() == "na")
                        document.getElementById("spWeatherDesc").style.color = blue;

                    break;
                }
                
                document.getElementById("spMyDash").innerHTML = json.recordStatus;
                if (json.recordStatus == 'OFFLINE')
                {
                    // document.getElementById("header_mydash").className.replace(/\bsuccess-box\b/g, "");
                    // document.getElementById("header_mydash").className = "box alert-box";
                    document.getElementById("header_mydash").style.backgroundColor  = red;
                    document.getElementById("spMyDash").style.color = rgblightblue;
                }
                else
                    document.getElementById("header_mydash").style.backgroundColor  = green;

                // Set header
                if (parseInt(document.getElementById("hdnChartCount").value) > 0) {
                for (var i = 0; i < json.headerObjectNameList.length; i++) {
                    if (json.headerObjectNameList[i].countValue == 'count')
                        document.getElementById("sphead_" + json.headerObjectNameList[i].name).innerHTML = json.headerObjectNameList[i].activevalue + "/" + json.headerObjectNameList[i].count;
                    else
                        document.getElementById("sphead_" + json.headerObjectNameList[i].name).innerHTML = json.headerObjectNameList[i].currvalue;
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

                    document.getElementById("header_" + json.headerObjectNameList[i].name).style.backgroundColor  = json.headerObjectNameList[i].headerbgcolor;
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

                // // Set AC Location
                // var controlStr = '';
                // for (var i = 0; i < json.binaryDeviceLocationList.length; i++) {
                //     if (json.binaryDeviceLocationList[i].deviceName.toLowerCase().indexOf("ac") >= 0) {
                //         document.getElementById("spBinOutputHead").innerHTML = json.binaryDeviceLocationList[i].deviceName;
                //         var aclocarr = json.binaryDeviceLocationList[i].deviceLocation.split(",");
                //         var aclocvalarr = json.binaryDeviceLocationList[i].activeval.split(",");
                //         var acobjname = json.binaryDeviceLocationList[i].name.split(",");

                //         controlStr = '';
                //         for (var j = 0; j < aclocarr.length; j++) {
                //             var checked = '';
                //             if (aclocvalarr[j] == '1')
                //                 checked = 'checked';
                //             controlStr += '<div class="row split-ac">';
                //             controlStr += '<div class="col-sm-8 pr-0">';
                //             controlStr += '<p>' + aclocarr[j] + '</p>';
                //             controlStr += '</div>';
                //             controlStr += '<div class="col-sm-4 swit-sect1">';
                //             controlStr += '<div>';
                //             controlStr += '<input type="checkbox" class="checkbox" id="acswitch_' + acobjname[j] + '" ' + checked + ' onclick="toggleac(this.id, this.checked);" />';                                  
                //             controlStr += '<label for="acswitch_' + acobjname[j] + '" class="switch">';
                //             controlStr += '<span class="switch__circle">';
                //             controlStr += '<span class="switch__circle-inner"></span>';
                //             controlStr += '</span>';
                //             controlStr += '<span class="switch__left">OFF</span><span class="switch__right">ON</span>';
                //             controlStr += ' </label>';
                //             controlStr += '</div>';
                //             controlStr += '</div>';
                //             // controlStr += '<div class="col-sm-4 swit-sec"">';
                //             // controlStr += '<div class="swit-btn" id="acswitch_' + acobjname[j] + '">';
                //             // controlStr += '<input type="checkbox" class="switch_1" ' + checked + ' />';
                //             // controlStr += '</div>';
                //             // controlStr += '</div>';
                //             controlStr += '</div>';
                //         }
                //         $('#dvBinOutputBody').append(controlStr);
                //     }
                // }                
                // // Set AC Location ends

                var jsonify = JSON.parse(json.allChartArray);                
                var val1 = [], gaugeVal1 = 0, plotbands = [], currval = 0, valuechecked = false, actualGaugeVal = 0;
                // for (var i = 0; i < json.timeArr.length; i++)
                //     timeArray.push(json.timeArr[i]);

                for (var i = 0; i < jsonify.length; i++) {
                    val1 = [], gaugeVal1 = 0, plotbands = [], currval = 0, valuechecked = false;
                    if (jsonify[i].objectName.toLowerCase().indexOf("room") >= 0 || jsonify[i].charttype.toLowerCase().indexOf("trend") >= 0) {
                        if (jsonify[i].objectName.toLowerCase().indexOf("room") >= 0) {
                            document.getElementById("spRoomTemp").innerHTML = jsonify[i].actualname;
                            document.getElementById("spRoomTempLoc").innerHTML = "" + jsonify[i].deviceLocation;
                            var timeDataIndex = 0;
                            if (jsonify[i].deviceTimeData.length - 15 > 0)
                                timeDataIndex = jsonify[i].deviceTimeData.length - 15;
                            for (var j = timeDataIndex; j < jsonify[i].deviceTimeData.length; j++) {
                                timeArray.push(jsonify[i].deviceTimeData[j].categoryVal);
                                val1.push({name: jsonify[i].deviceTimeData[j].categoryVal, y: parseFloat(jsonify[i].deviceTimeData[j].devdata)});
                                gaugeVal1 = parseFloat(jsonify[i].deviceTimeData[j].roundedData);
                            }
                            actualGaugeVal = parseFloat(gaugeVal1).toFixed(2);
                            var chartName = "gauge_room";
                            var minplot = jsonify[i].plotmin;
                            var maxplot = jsonify[i].plotmax;

                            if (gaugeVal1 < parseFloat(jsonify[i].plotbands[0].from) || gaugeVal1 > parseFloat(jsonify[i].plotbands[jsonify[i].plotbands.length - 1].to))
                                gaugeVal1 = parseFloat(jsonify[i].plotbands[jsonify[i].plotbands.length - 1].to);

                            if (jsonify[i].showinheader == '1') {
                                if (hrs >= 10 && hrs < 20) {
                                    // currval = parseFloat(jsonify[i].currvalue);
                                    currval = gaugeVal1;
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
                                            document.getElementById("imgRoomTemp").src = 'images/bell_red_ani.gif';
                                            valuechecked = true;
                                        }

                                        // if (!valuechecked) {
                                        //     if (currval >= parseFloat(jsonify[i].plotbands[0].from) && currval <= parseFloat(jsonify[i].plotbands[0].to)) {
                                        //         if (document.getElementById("header_" + jsonify[i].objectName))
                                        //             document.getElementById("header_" + jsonify[i].objectName).style.backgroundColor = jsonify[i].plotbands[0].color.trim();
                                                
                                        //         if (document.getElementById("sphead_" + jsonify[i].objectName))
                                        //             document.getElementById("sphead_" + jsonify[i].objectName).style.color = rgblightblue;

                                        //         if (document.getElementById("spheaduom_" + jsonify[i].objectName))
                                        //             document.getElementById("spheaduom_" + jsonify[i].objectName).style.color = rgblightblue;

                                        //         if (document.getElementById("spheadstatus_" + jsonify[i].objectName))
                                        //             document.getElementById("spheadstatus_" + jsonify[i].objectName).style.color = rgblightblue;
                                        //         document.getElementById("imgRoomTemp").src = 'images/bell_red_ani.gif';
                                        //         valuechecked = true;
                                        //     }
                                        // }

                                        if (!valuechecked) {
                                            for (var a = 0; a < jsonify[i].plotbands.length - 1; a++) {
                                                if (currval >= parseFloat(jsonify[i].plotbands[a].from) && currval <= parseFloat(jsonify[i].plotbands[a].to)) {
                                                    if (document.getElementById("header_" + jsonify[i].objectName))
                                                        // document.getElementById("header_" + jsonify[i].objectName).style.backgroundColor = jsonify[i].headerbgcolor;
                                                        document.getElementById("header_" + jsonify[i].objectName).style.backgroundColor = jsonify[i].plotbands[a].color.trim();
                                                    
                                                    if (document.getElementById("sphead_" + jsonify[i].objectName))
                                                        document.getElementById("sphead_" + jsonify[i].objectName).removeAttribute("style");

                                                    if (document.getElementById("spheaduom_" + jsonify[i].objectName))
                                                        document.getElementById("spheaduom_" + jsonify[i].objectName).removeAttribute("style");

                                                    if (document.getElementById("spheadstatus_" + jsonify[i].objectName))
                                                        document.getElementById("spheadstatus_" + jsonify[i].objectName).removeAttribute("style");
                                                    valuechecked = true;
                                                    break;
                                                }
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
                                                document.getElementById("imgRoomTemp").src = 'images/bell_red_ani.gif';
                                                valuechecked = true;
                                            }
                                        }
                                    }               
                                }
                                if (!valuechecked) {
                                    if (document.getElementById("header_" + jsonify[i].objectName))
                                        document.getElementById("header_" + jsonify[i].objectName).style.backgroundColor  = jsonify[i].headerbgcolor;
                                        // document.getElementById("header_" + jsonify[i].objectName).style.backgroundColor  = green;

                                    if (document.getElementById("sphead_" + jsonify[i].objectName))
                                        document.getElementById("sphead_" + jsonify[i].objectName).removeAttribute("style");

                                    if (document.getElementById("spheaduom_" + jsonify[i].objectName))
                                        document.getElementById("spheaduom_" + jsonify[i].objectName).removeAttribute("style");

                                    if (document.getElementById("spheadstatus_" + jsonify[i].objectName))
                                        document.getElementById("spheadstatus_" + jsonify[i].objectName).removeAttribute("style");
                                    document.getElementById("imgRoomTemp").src = 'images/bell_gr.png';
                                }
                            }

                            var yaxisarr = [{
                                min: parseFloat(minplot),
                                max: parseFloat(maxplot),
                                showFirstLabel: true,
                                showLastLabel: true,
                                /* minorTickInterval: 'auto',
                                minorTickWidth: 1,
                                minorTickLength: 30,
                                minorTickPosition: 'inside',
                                minorTickColor: '#666',
                                tickAmount: null, */
                                tickPixelInterval: 15,
                                tickWidth: 2,
                                tickPosition: 'inside',
                                tickLength: 15,
                                tickColor: '#666',
                                labels: {
                                    step: 3,//2,
                                    rotation: 'auto',
                                    distance: 5
                                },
                                title: {
                                    // text: '\xB0C'
                                },
                                from: parseFloat(minplot),
                                to: parseFloat(maxplot),
                                plotBands: [],
                            }];

                            for (var j = 0; j < jsonify[i].plotbands.length; j++)
                                yaxisarr[0].plotBands.push({from: parseFloat(jsonify[i].plotbands[j].from), to: parseFloat(jsonify[i].plotbands[j].to), color: jsonify[i].plotbands[j].color.trim(), innerRadius: '45%', outerRadius: '100%'});
                            yaxisarr[0].plotBands.push({thickness: 80});
                            var gaugeOptions = {
                                chart: {
                                    renderTo: chartName,
                                    type: 'gauge',
                                    plotBackgroundColor: '#E4E4E4',
                                    plotBackgroundImage: null,
                                    plotBorderWidth: 0,
                                    plotShadow: false,
                                    margin: [0, 0, 0, 0],
                                    spacingTop: 0,
                                    spacingBottom: 0,
                                    spacingLeft: 0,
                                    spacingRight: 0,
                                    width: 200,
                                    height: 215,
                                },
                                credits: { enabled: false },
                                title: {
                                    text: '', //jsonify[i].deviceName,
                                },
                                pane: {
                                    startAngle: -100,
                                    endAngle: 100,
                                    // size: '95%',
                                    center: ['49%', '47%'],
                                    background: {backgroundColor: 'transparent', borderWidth: 0},
                                },
                                yAxis: yaxisarr,
                                plotOptions: {
                                    gauge: {
                                        dial: {
                                            radius: '70%',
                                            backgroundColor: 'black',
                                            baseWidth: 10,
                                            topWidth: 1,
                                            baseLength: '3%', // of radius
                                            rearLength: '0%'
                                        }, 
                                        pivot: {
                                            radius: 5,
                                            backgroundColor: 'black'
                                        }
                                    }
                                },
                                exporting: { enabled: false },
                                series: [{
                                    name: jsonify[i].deviceName,
                                    data: [gaugeVal1],
                                    tooltip: {
                                        // pointFormat: jsonify[i].deviceName + ': ' + actualGaugeVal + ' \xB0C',
                                        valueSuffix: '\xB0C'
                                    },
                                    dataLabels: {
                                        enabled: false, // to display value below gauge
                                        borderWidth: 0,
                                        // style: {
                                        //     fontWeight:'bold',
                                        //     fontSize: '22px'
                                        // },
                                        formatter: function () {
                                            var s;
                                            s = '<span style="font-size: 20px;">' + this.point.y + '</span>';
                                            // s += '<span style="font-size: 20px;">' + ' \xB0C' + '</span>';
                                            return s;
                                        },
                                    }
                                }]
                            };
                            var chart = new Highcharts.Chart(gaugeOptions);
                            // chart.reflow();

                            chartName = "trend_room";
                            var splineoptions = {
                                chart: {
                                    renderTo: chartName,
                                    type: 'spline',
                                    backgroundColor: '#e2f4fe', //'#FCFFC5',
                                    // width: 300,
                                    // height: 110,
                                    // plotBackgroundColor: '#E4E4E4',
                                    // margin: [0, 0, 0, 0],
                                    // spacingTop: 0,
                                    spacingBottom: 0,
                                    spacingLeft: 0,
                                    // spacingRight: 0,
                                },
                                credits: { enabled: false },
                                title: {
                                    // useHTML:true,
                                    // text: '<div class="">' + jsonify[i].deviceName + " - " + tday + '</div>'
                                    text: '', //jsonify[i].deviceName,
                                    align: 'left'
                                },
                                xAxis: {
                                    gridLineWidth: 1,
                                    // type: 'category',
                                    // uniqueNames: false,
                                    labels: {
                                        // step: 5,
                                        // staggerLines: 2
                                        style: {
                                            // color: 'red',
                                            fontSize: '10px'
                                        }
                                    },
                                    title: {
                                        text: 'Time'
                                    },
                                    categories: timeArray
                                },
                                yAxis: {
                                    gridLineWidth: 1,
                                    title: {
                                        text: 'Temperature'
                                    },
                                    // labels: {
                                    //     rotation: 270
                                    // },
                                    // labels: {
                                    //     format: '{value:.2f}'
                                    // }
                                    allowDecimals: false,
                                },
                                legend: {
                                    enabled: false
                                },
                                series: [{
                                    name: jsonify[i].deviceName,
                                    data: val1,
                                    // shadow: {
                                    //     width: 10,
                                    //     offsetX: 0,
                                    //     offsetY: 0
                                    // }
                                }],
                                plotOptions: {
                                    spline: {
                                        shadow: true,
                                    }
                                },
                                exporting: { enabled: false },
                            };
                            chart = new Highcharts.Chart(splineoptions);
                            chart.reflow();
                        }                     
                    }

                    val1 = [], gaugeVal1 = 0, plotbands = [], currval = 0, valuechecked = false;
                    if (jsonify[i].charttype.toLowerCase().indexOf("gauge") >= 0) {
                        if (jsonify[i].objectName.toLowerCase().indexOf("room") >= 0) continue;

                        if (jsonify[i].deviceTimeData.length > 0)
                            gaugeVal1 = parseFloat(jsonify[i].deviceTimeData[jsonify[i].deviceTimeData.length - 1].roundedData);
                        else
                            gaugeVal1 = parseFloat(jsonify[i].currvalue);

                        var isMultipleDevice = false;
                        for (var cnt = 0; cnt < headerObj.length; cnt ++)
                        {
                            if (jsonify[i].objectName == headerObj[cnt].name && headerObj[cnt].count > 1)
                                isMultipleDevice = true;
                        }

                        if (jsonify[i].showinheader == '1') {
                            // currval = parseFloat(jsonify[i].currvalue);
                            currval = gaugeVal1;
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
                                    document.getElementById("spGaugeValue_" + jsonify[i].objectName).style.color = red;
                                    document.getElementById("spGaugeUOM_" + jsonify[i].objectName).style.color = red;
                                    document.getElementById("gaugech_img_" + jsonify[i].objectName).src = 'images/bell_red_ani.gif';
                                    valuechecked = true;
                                }

                                // if (!valuechecked) {
                                //     if (currval >= parseFloat(jsonify[i].plotbands[0].from) && currval <= parseFloat(jsonify[i].plotbands[0].to)) {
                                //         if (!isMultipleDevice) {
                                //             if (document.getElementById("header_" + jsonify[i].objectName))
                                //                 document.getElementById("header_" + jsonify[i].objectName).style.backgroundColor = jsonify[i].plotbands[0].color.trim();
                                            
                                //             if (document.getElementById("sphead_" + jsonify[i].objectName))
                                //                 document.getElementById("sphead_" + jsonify[i].objectName).style.color = rgblightblue;

                                //             if (document.getElementById("spheaduom_" + jsonify[i].objectName))
                                //                 document.getElementById("spheaduom_" + jsonify[i].objectName).style.color = rgblightblue;

                                //             if (document.getElementById("spheadstatus_" + jsonify[i].objectName))
                                //                 document.getElementById("spheadstatus_" + jsonify[i].objectName).style.color = rgblightblue;
                                //         }
                                //         document.getElementById("gaugech_img_" + jsonify[i].objectName).src = 'images/bell_red_ani.gif';
                                //         valuechecked = true;
                                //     }
                                // }

                                if (!valuechecked) {
                                    for (var a = 0; a < jsonify[i].plotbands.length - 1; a++) {
                                        if (currval >= parseFloat(jsonify[i].plotbands[a].from) && currval <= parseFloat(jsonify[i].plotbands[a].to)) {
                                            if (document.getElementById("header_" + jsonify[i].objectName))
                                                // document.getElementById("header_" + jsonify[i].objectName).style.backgroundColor = jsonify[i].headerbgcolor;
                                                document.getElementById("header_" + jsonify[i].objectName).style.backgroundColor = jsonify[i].plotbands[a].color.trim();
                                            
                                            if (document.getElementById("sphead_" + jsonify[i].objectName))
                                                document.getElementById("sphead_" + jsonify[i].objectName).removeAttribute("style");

                                            if (document.getElementById("spheaduom_" + jsonify[i].objectName))
                                                document.getElementById("spheaduom_" + jsonify[i].objectName).removeAttribute("style");

                                            if (document.getElementById("spheadstatus_" + jsonify[i].objectName))
                                                document.getElementById("spheadstatus_" + jsonify[i].objectName).removeAttribute("style");

                                            document.getElementById("spGaugeValue_" + jsonify[i].objectName).style.color = green;
                                            document.getElementById("spGaugeUOM_" + jsonify[i].objectName).style.color = green;
                                            valuechecked = true;
                                            break;
                                        }
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
                                        document.getElementById("spGaugeValue_" + jsonify[i].objectName).style.color = red;
                                        document.getElementById("spGaugeUOM_" + jsonify[i].objectName).style.color = red;
                                        document.getElementById("gaugech_img_" + jsonify[i].objectName).src = 'images/bell_red_ani.gif';
                                        valuechecked = true;
                                    }
                                }
                            }
                        }
                        if (!valuechecked) {
                            if (!isMultipleDevice) {
                                if (document.getElementById("header_" + jsonify[i].objectName))
                                    document.getElementById("header_" + jsonify[i].objectName).style.backgroundColor  = jsonify[i].headerbgcolor;
                                    // document.getElementById("header_" + jsonify[i].objectName).removeAttribute("style");

                                if (document.getElementById("sphead_" + jsonify[i].objectName))
                                    document.getElementById("sphead_" + jsonify[i].objectName).removeAttribute("style");

                                if (document.getElementById("spheaduom_" + jsonify[i].objectName))
                                    document.getElementById("spheaduom_" + jsonify[i].objectName).removeAttribute("style");

                                if (document.getElementById("spheadstatus_" + jsonify[i].objectName))
                                    document.getElementById("spheadstatus_" + jsonify[i].objectName).removeAttribute("style");
                            }
                            document.getElementById("gaugech_img_" + jsonify[i].objectName).src = 'images/bell_gr.png';
                        }
                        var chartName = "parentGauge_" + jsonify[i].objectName;
                        var minplot = jsonify[i].plotmin;
                        var maxplot = jsonify[i].plotmax;
                        
                        document.getElementById("spGaugeValue_" + jsonify[i].objectName).innerHTML = gaugeVal1;
                        document.getElementById("spGaugeUOM_" + jsonify[i].objectName).innerHTML = jsonify[i].uom;
                        document.getElementById("pGaugeDesc_" + jsonify[i].objectName).innerHTML = jsonify[i].devDescription;
                        // if (valuechecked) {
                        //     document.getElementById("spGaugeValue_" + jsonify[i].objectName).style.color = red;
                        //     document.getElementById("spGaugeUOM_" + jsonify[i].objectName).style.color = red;
                        // }
                        // else {
                        //     document.getElementById("spGaugeValue_" + jsonify[i].objectName).style.color = green;
                        //     document.getElementById("spGaugeUOM_" + jsonify[i].objectName).style.color = green;
                        // }

                        var yaxisarr = [{
                            min: parseFloat(minplot),
                            max: parseFloat(maxplot),
                            showFirstLabel: true,
                            showLastLabel: true,

                            /* minorTickInterval: 'auto',
                            minorTickWidth: 1,
                            minorTickLength: 30,
                            minorTickPosition: 'inside',
                            minorTickColor: '#666', */

                            tickPixelInterval: 30,
                            tickWidth: 2,
                            tickPosition: 'inside',
                            tickLength: 15,
                            tickColor: '#666',
                            labels: {
                                step: 3,//2,
                                rotation: 'auto',
                                distance: 5
                            },
                            title: {
                                // text: '\xB0C'
                            },
                            from: parseFloat(minplot),
                            to: parseFloat(maxplot),
                            plotBands: [],
                        }];

                        for (var j = 0; j < jsonify[i].plotbands.length; j++)
                            yaxisarr[0].plotBands.push({from: parseFloat(jsonify[i].plotbands[j].from), to: parseFloat(jsonify[i].plotbands[j].to), color: jsonify[i].plotbands[j].color.trim(), innerRadius: '45%', outerRadius: '100%'});
                        yaxisarr[0].plotBands.push({thickness: 80});

                        var gaugeOptions = {
                            chart: {
                                renderTo: chartName,
                                type: 'gauge',
                                plotBackgroundColor: '#E4E4E4',
                                plotBackgroundImage: null,
                                plotBorderWidth: 0,
                                plotShadow: false,
                                margin: [0, 0, 0, 0],
                                spacingTop: 0,
                                spacingBottom: 0,
                                spacingLeft: 0,
                                spacingRight: 0,
                                width: 195,
                                height: 190,
                            },
                            credits: { enabled: false },
                            title: {
                                text: '', //jsonify[i].deviceName,
                            },
                            pane: {
                                startAngle: -100,
                                endAngle: 100,
                                // size: '95%',
                                center: ['51%', '50%'],
                                background: {backgroundColor: 'transparent', borderWidth: 0},
                            },
                            yAxis: yaxisarr,
                            plotOptions: {
                                gauge: {
                                    dial: {
                                        radius: '70%',
                                        backgroundColor: 'black',
                                        baseWidth: 10,
                                        topWidth: 1,
                                        baseLength: '3%', // of radius
                                        rearLength: '0%'
                                    }, 
                                    pivot: {
                                        radius: 5,
                                        backgroundColor: 'black'
                                    }
                                }
                            },
                            exporting: { enabled: false },
                            series: [{
                                name: jsonify[i].deviceName,
                                data: [gaugeVal1],
                                tooltip: {
                                    valueSuffix: '\xB0C'
                                },
                                dataLabels: {
                                    enabled: false, // to display value below gauge
                                    borderWidth: 0,
                                    // style: {
                                    //     fontWeight:'bold',
                                    //     fontSize: '22px'
                                    // },
                                    formatter: function () {
                                        var s;
                                        s = '<span style="font-size: 20px;">' + this.point.y + '</span>';
                                        // s += '<span style="font-size: 20px;">' + ' \xB0C' + '</span>';
                                        return s;
                                    },
                                }
                            }]
                        };
                        var chart = new Highcharts.Chart(gaugeOptions);
                    }

                    val1 = [], gaugeVal1 = 0, plotbands = [], currval = 0, valuechecked = false, timeArray = [];
                    if (jsonify[i].charttype.toLowerCase().indexOf("trend") >= 0) {
                        document.getElementById("spValue_" + jsonify[i].objectName).innerHTML = parseFloat(jsonify[i].currvalue).toFixed(2);
                        document.getElementById("spValueUOM_" + jsonify[i].objectName).innerHTML = jsonify[i].uom;
                        document.getElementById("pValueDesc_" + jsonify[i].objectName).innerHTML = jsonify[i].devDescription;
                        
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

                                    // if (!valuechecked) {
                                    //     if (currval >= parseFloat(jsonify[i].plotbands[0].from) && currval <= parseFloat(jsonify[i].plotbands[0].to)) {
                                    //         if (document.getElementById("header_" + jsonify[i].objectName))
                                    //             document.getElementById("header_" + jsonify[i].objectName).style.backgroundColor = jsonify[i].plotbands[0].color.trim();

                                    //         if (document.getElementById("sphead_" + jsonify[i].objectName))
                                    //             document.getElementById("sphead_" + jsonify[i].objectName).style.color = rgblightblue;

                                    //         if (document.getElementById("spheaduom_" + jsonify[i].objectName))
                                    //             document.getElementById("spheaduom_" + jsonify[i].objectName).style.color = rgblightblue;

                                    //         if (document.getElementById("spheadstatus_" + jsonify[i].objectName))
                                    //             document.getElementById("spheadstatus_" + jsonify[i].objectName).style.color = rgblightblue;
                                    //         valuechecked = true;
                                    //     }
                                    // }

                                    if (!valuechecked) {
                                        for (var a = 0; a < jsonify[i].plotbands.length - 1; a++) {
                                            if (currval >= parseFloat(jsonify[i].plotbands[a].from) && currval <= parseFloat(jsonify[i].plotbands[a].to)) {
                                                if (document.getElementById("header_" + jsonify[i].objectName))
                                                    // document.getElementById("header_" + jsonify[i].objectName).style.backgroundColor = jsonify[i].headerbgcolor;
                                                    document.getElementById("header_" + jsonify[i].objectName).style.backgroundColor = jsonify[i].plotbands[a].color.trim();
                                                
                                                if (document.getElementById("sphead_" + jsonify[i].objectName))
                                                    document.getElementById("sphead_" + jsonify[i].objectName).removeAttribute("style");

                                                if (document.getElementById("spheaduom_" + jsonify[i].objectName))
                                                    document.getElementById("spheaduom_" + jsonify[i].objectName).removeAttribute("style");

                                                if (document.getElementById("spheadstatus_" + jsonify[i].objectName))
                                                    document.getElementById("spheadstatus_" + jsonify[i].objectName).removeAttribute("style");
                                                valuechecked = true;
                                                break;
                                            }
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
                                        document.getElementById("header_" + jsonify[i].objectName).style.backgroundColor  = jsonify[i].headerbgcolor;
                                        // document.getElementById("header_" + jsonify[i].objectName).style.backgroundColor = green;

                                    if (document.getElementById("sphead_" + jsonify[i].objectName))
                                        document.getElementById("sphead_" + jsonify[i].objectName).removeAttribute("style");

                                    if (document.getElementById("spheaduom_" + jsonify[i].objectName))
                                        document.getElementById("spheaduom_" + jsonify[i].objectName).removeAttribute("style");

                                    if (document.getElementById("spheadstatus_" + jsonify[i].objectName))
                                        document.getElementById("spheadstatus_" + jsonify[i].objectName).removeAttribute("style");
                                }
                            }
                        }

                        chartName = "dvGraph_" + jsonify[i].objectName;
                        for (var j = 0; j < jsonify[i].deviceTimeData.length; j++) {
                            timeArray.push(jsonify[i].deviceTimeData[j].categoryVal);
                            val1.push({name: jsonify[i].deviceTimeData[j].categoryVal, y: parseFloat(jsonify[i].deviceTimeData[j].devdata)});
                            // if (parseFloat(jsonify[i].deviceTimeData[j].devdata) > 3 && parseFloat(jsonify[i].deviceTimeData[j].devdata) < 4)
                            //     val1.push({name: jsonify[i].deviceTimeData[j].categoryVal, y: parseFloat(jsonify[i].deviceTimeData[j].devdata), color: red});
                            // else
                            //     val1.push({name: jsonify[i].deviceTimeData[j].categoryVal, y: parseFloat(jsonify[i].deviceTimeData[j].devdata), color: green});
                        }

                        var xaxisstep = 12;
                        if (timeArray.length <= 10) 
                            xaxisstep = 1;
                        else if (timeArray.length > 10 && timeArray.length <= 20) 
                            xaxisstep = 2;
                        else if (timeArray.length > 20 && timeArray.length <= 30) 
                            xaxisstep = 4;
                        else if (timeArray.length > 30 && timeArray.length <= 40) 
                            xaxisstep = 6;
                        else if (timeArray.length > 40 && timeArray.length <= 50) 
                            xaxisstep = 8;
                        else if (timeArray.length > 50 && timeArray.length <= 60) 
                            xaxisstep = 10;
                        else if (timeArray.length > 60 && timeArray.length <= 72)
                            xaxisstep = 12;

                        var columnoptions = {
                                chart: {
                                    renderTo: chartName,
                                    type: 'spline',
                                    backgroundColor: '#fef2e4', //'#FCFFC5',
                                    // width: 300,
                                    // height: 110,
                                    // plotBackgroundColor: '#E4E4E4',
                                    // margin: [0, 0, 0, 0],
                                    // spacingTop: 0,
                                    spacingBottom: 0,
                                    spacingLeft: 0,
                                    // spacingRight: 0,
                                },
                                credits: { enabled: false },
                                title: {
                                    // useHTML:true,
                                    // text: '<div class="">' + jsonify[i].deviceName + " - " + tday + '</div>'
                                    text: '', //jsonify[i].deviceName,
                                    align: 'left'
                                },
                                xAxis: {
                                    gridLineWidth: 1,
                                    // type: 'category',
                                    // uniqueNames: false,
                                    // tickInterval: 1000 * 3600,
                                    labels: {
                                        // step: 12,
                                        step : xaxisstep,
                                        // staggerLines: 2
                                        style: {
                                            // color: 'red',
                                            fontSize: '10px'
                                        },
                                        formatter: function () {
                                            if (timeArray.length <= 40)
                                                return this.value;
                                            else
                                                return  this.value.toString().substring(0, 2);
                                            // return Math.round(this.value / 10);
                                        }
                                    },
                                    title: {
                                        text: 'Time'
                                    },
                                    categories: timeArray
                                },
                                yAxis: {
                                    gridLineWidth: 1,
                                    title: {
                                        text: 'kW'
                                    },
                                    // labels: {
                                    //     rotation: 270
                                    // },
                                    // labels: {
                                    //     format: '{value:.2f}'
                                    // }
                                    allowDecimals: false,
                                },
                                legend: {
                                    enabled: false
                                },
                                series: [{
                                    name: jsonify[i].deviceName,
                                    data: val1,
                                    // shadow: {
                                    //     width: 10,
                                    //     offsetX: 0,
                                    //     offsetY: 0
                                    // }
                                }],
                                plotOptions: {
                                    spline: {
                                        shadow: true,
                                    },
                                    // series: {
                                    //     color: green
                                    // }
                                },
                                exporting: { enabled: false },
                            };

                        // var columnoptions = {
                        //         chart: {
                        //             renderTo: chartName,
                        //             type: 'columnpyramid',
                        //             backgroundColor: '#FCFFC5',
                        //             // width: 400,
                        //             // height: 210,
                        //             // plotBackgroundColor: '#E4E4E4',
                        //             // margin: [0, 0, 0, 0],
                        //             spacingTop: 0,
                        //             spacingBottom: 0,
                        //             spacingLeft: 0,
                        //             spacingRight: 0,
                        //             // events: {
                        //             //         load: function() {
                        //             //             var chart = this;
                        //             //             chart.showLoading('Loading ...');
                        //             //         }
                        //             // },
                        //             // // plotBorderWidth: 1,
                        //         },
                        //         credits: { enabled: false },
                        //         title: {
                        //             // useHTML:true,
                        //             // text: '<div class="">' + jsonify[i].deviceName + " - " + tday + '</div>'
                        //             text: '', //jsonify[i].deviceName,
                        //             align: 'left'
                        //         },
                        //         xAxis: {
                        //         crosshair: true,
                        //         // labels: {
                        //         //     step: 2,
                        //         //     rotation: 'auto',
                        //         //     distance: 5
                        //         // },
                        //         labels: {
                        //             step: 6,
                        //             style: {
                        //                     // color: 'red',
                        //                 fontSize: '10px'
                        //             }
                        //         },
                        //         // type: 'category',
                        //         categories: timeArray
                        //     },
                        //     yAxis: {
                        //         min: 0,
                        //         title: {
                        //             text: 'KW'
                        //         }
                        //     },
                        //     series: [{
                        //             name: jsonify[i].deviceName,
                        //             data: val1,
                        //             color: '#00a652'
                        //         }],
                        //         legend: {
                        //             enabled: false
                        //         },
                        //         exporting: { enabled: false },
                        //     };
                            chart = new Highcharts.Chart(columnoptions);
                            chart.reflow();
                    }

                    currval = 0, valuechecked = false;
                    if (jsonify[i].charttype.toLowerCase().indexOf("value") >= 0) {
                        document.getElementById("spValue_" + jsonify[i].objectName).innerHTML = parseFloat(jsonify[i].currvalue).toFixed(2);
                        document.getElementById("spValueUOM_" + jsonify[i].objectName).innerHTML = jsonify[i].uom;
                        document.getElementById("pValueDesc_" + jsonify[i].objectName).innerHTML = jsonify[i].devDescription;

                        if (Math.floor(jsonify[i].currvalue) >= 100) {
                            document.getElementById("spValue_" + jsonify[i].objectName).style.fontSize = "30px";
                            document.getElementById("spValueUOM_" + jsonify[i].objectName).style.fontSize = "20px";
                        }
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

                                // if (!valuechecked) {
                                //     if (currval >= parseFloat(jsonify[i].plotbands[0].from) && currval <= parseFloat(jsonify[i].plotbands[0].to)) {
                                //         if (document.getElementById("header_" + jsonify[i].objectName))
                                //             document.getElementById("header_" + jsonify[i].objectName).style.backgroundColor = jsonify[i].plotbands[0].color.trim();

                                //             if (document.getElementById("sphead_" + jsonify[i].objectName))
                                //                 document.getElementById("sphead_" + jsonify[i].objectName).style.color = rgblightblue;

                                //         if (document.getElementById("spheaduom_" + jsonify[i].objectName))
                                //             document.getElementById("spheaduom_" + jsonify[i].objectName).style.color = rgblightblue;

                                //         if (document.getElementById("spheadstatus_" + jsonify[i].objectName))
                                //             document.getElementById("spheadstatus_" + jsonify[i].objectName).style.color = rgblightblue;
                                //         valuechecked = true;
                                //     }
                                // }

                                if (!valuechecked) {
                                    for (var a = 0; a < jsonify[i].plotbands.length - 1; a++) {
                                        if (currval >= parseFloat(jsonify[i].plotbands[a].from) && currval <= parseFloat(jsonify[i].plotbands[a].to)) {
                                            if (document.getElementById("header_" + jsonify[i].objectName))
                                                // document.getElementById("header_" + jsonify[i].objectName).style.backgroundColor = jsonify[i].headerbgcolor;
                                                document.getElementById("header_" + jsonify[i].objectName).style.backgroundColor = jsonify[i].plotbands[a].color.trim();
                                            
                                            if (document.getElementById("sphead_" + jsonify[i].objectName))
                                                document.getElementById("sphead_" + jsonify[i].objectName).removeAttribute("style");

                                            if (document.getElementById("spheaduom_" + jsonify[i].objectName))
                                                document.getElementById("spheaduom_" + jsonify[i].objectName).removeAttribute("style");

                                            if (document.getElementById("spheadstatus_" + jsonify[i].objectName))
                                                document.getElementById("spheadstatus_" + jsonify[i].objectName).removeAttribute("style");
                                            valuechecked = true;
                                            break;
                                        }
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
                                    document.getElementById("header_" + jsonify[i].objectName).style.backgroundColor  = jsonify[i].headerbgcolor;
                                    // document.getElementById("header_" + jsonify[i].objectName).style.backgroundColor = green;
                                    // document.getElementById("header_" + jsonify[i].objectName).removeAttribute("style");
                                    
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
		        document.querySelector("#preloader").style.visibility = "hidden";
            }
        });
         
        function autoRefresh(){
            $.ajax({
                type: "GET",
                url : 'device/fetchdata',
                dataType: "json",
                error: function() {},
                success: function (json) {console.log(json);
                    // if (document.getElementById("hdnChartCount").value == 0 || json.recordStatus.toLowerCase() == 'offline')
                    if (document.getElementById("hdnChartCount").value == 0)
                        window.location.reload(true);
                    document.getElementById("spMyDash").innerHTML = json.recordStatus;
                    if (json.recordStatus.toLowerCase() == 'online') {
                        document.getElementById("header_mydash").style.backgroundColor  = green;
                        
                        // Set header
                        // headerObj = [];
                        for (var i = 0; i < json.headerObjectNameList.length; i++) {
                            if (json.headerObjectNameList[i].countValue == 'count')
                                document.getElementById("sphead_" + json.headerObjectNameList[i].name).innerHTML = json.headerObjectNameList[i].activevalue + "/" + json.headerObjectNameList[i].count;
                            else
                                document.getElementById("sphead_" + json.headerObjectNameList[i].name).innerHTML = json.headerObjectNameList[i].currvalue;
                            // document.getElementById("sphead_" + json.headerObjectNameList[i].name).innerHTML = json.headerObjectNameList[i].activevalue + "/" + json.headerObjectNameList[i].count;
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
                                document.getElementById("header_" + json.headerObjectNameList[i].name).style.backgroundColor  = json.headerObjectNameList[i].headerbgcolor;
                                if (json.headerObjectNameList[i].activevalue == json.headerObjectNameList[i].count) {
                                    document.getElementById("spheadstatus_" + json.headerObjectNameList[i].name).innerHTML = "NORMAL";
                                    document.getElementById("header_" + json.headerObjectNameList[i].name).style.backgroundColor  = json.headerObjectNameList[i].headerbgcolor;
                                    // document.getElementById("header_" + json.headerObjectNameList[i].name).removeAttribute("style");

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
                            // headerObj.push({name: json.headerObjectNameList[i].name, count: json.headerObjectNameList[i].count});
                        }
                        // Set header ends

                        // // Set AC Location
                        // for (var i = 0; i < json.binaryDeviceLocationList.length; i++) {
                        //     if (json.binaryDeviceLocationList[i].deviceName.toLowerCase().indexOf("ac") >= 0) {
                        //         var aclocvalarr = json.binaryDeviceLocationList[i].activeval.split(",");
                        //         var acobjname = json.binaryDeviceLocationList[i].name.split(",");
                        //         for (var j = 0; j < aclocvalarr.length; j++) {
                        //             var checked = false;
                        //             if (aclocvalarr[j] == '1')
                        //                 checked = true;
                        //             document.getElementById("acswitch_" + acobjname[j]).checked = checked;
                        //         }
                        //     }
                        // }                        
                        // // Set AC Location ends

                        var jsonify = JSON.parse(json.allChartArray);
                        var val1 = [], gaugeVal1 = 0, plotbands = [], currval = 0, valuechecked = false;
                        var x1 = "", y1 = "", yGauge = "", chartId = 0, chartName = '';
                        var charts = Highcharts.charts;
                        var shiftFlag = false, isXAxisSet = false;

                        for (var i = 0; i < jsonify.length; i++) {
                            if (jsonify[i].objectName.toLowerCase().indexOf("room") >= 0 || jsonify[i].charttype.toLowerCase().indexOf("trend") >= 0) {
                                if (jsonify[i].objectName.toLowerCase().indexOf("room") >= 0) {
                                    currval = parseFloat(jsonify[i].currvalue).toFixed(2);
                                    yGauge = gaugeVal1 = parseFloat(jsonify[i].roundedData);

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
                                                    document.getElementById("imgRoomTemp").src = 'images/bell_red_ani.gif';
                                                    valuechecked = true;
                                                }

                                                if (!valuechecked) {
                                                    if (currval >= parseFloat(jsonify[i].plotbands[0].from) && currval <= parseFloat(jsonify[i].plotbands[0].to)) {
                                                        if (document.getElementById("header_" + jsonify[i].objectName))
                                                            document.getElementById("header_" + jsonify[i].objectName).style.backgroundColor = jsonify[i].plotbands[0].color.trim();

                                                        document.getElementById("sphead_" + jsonify[i].objectName).style.color = rgblightblue;
                                                        if (document.getElementById("spheaduom_" + jsonify[i].objectName))
                                                            document.getElementById("spheaduom_" + jsonify[i].objectName).style.color = rgblightblue;

                                                        if (document.getElementById("spheadstatus_" + jsonify[i].objectName))
                                                            document.getElementById("spheadstatus_" + jsonify[i].objectName).style.color = rgblightblue;
                                                        document.getElementById("imgRoomTemp").src = 'images/bell_red_ani.gif';
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
                                                        document.getElementById("imgRoomTemp").src = 'images/bell_red_ani.gif';
                                                        valuechecked = true;
                                                    }
                                                }
                                            }
                                        }
                                        if (!valuechecked) {
                                            if (document.getElementById("header_" + jsonify[i].objectName))
                                                document.getElementById("header_" + jsonify[i].objectName).style.backgroundColor  = jsonify[i].headerbgcolor;
                                                // document.getElementById("header_" + jsonify[i].objectName).style.backgroundColor  = green;

                                            document.getElementById("sphead_" + jsonify[i].objectName).removeAttribute("style");
                                            if (document.getElementById("spheaduom_" + jsonify[i].objectName))
                                                document.getElementById("spheaduom_" + jsonify[i].objectName).removeAttribute("style");

                                            if (document.getElementById("spheadstatus_" + jsonify[i].objectName))
                                                document.getElementById("spheadstatus_" + jsonify[i].objectName).removeAttribute("style");
                                            document.getElementById("imgRoomTemp").src = 'images/bell_gr.png';
                                        }
                                    }

                                    chartName = "gauge_room";
                                    var actualGaugeVal = gaugeVal1;
                                    if (gaugeVal1 < parseFloat(jsonify[i].plotbands[0].from) || gaugeVal1 > parseFloat(jsonify[i].plotbands[jsonify[i].plotbands.length - 1].to))
                                        yGauge = gaugeVal1 = parseFloat(jsonify[i].plotbands[jsonify[i].plotbands.length - 1].to);
                                    
                                    charts.forEach(function(chart, index) {                        
                                        if (chart.renderTo.id === chartName) {
                                            var point = chart.series[0].points[0];
                                            point.update(yGauge);
                                            // chart.tooltip.options.pointFormat = jsonify[i].deviceName + ': ' + actualGaugeVal + ' \xB0C';
                                            // chart.update({
                                            //     tooltip: {
                                            //         pointFormat: jsonify[i].deviceName + ': ' + actualGaugeVal + ' \xB0C'
                                            //     }
                                            // });
                                        }
                                    });

                                    x1 = jsonify[i].category;
                                    y1 = parseFloat(jsonify[i].devdata);
                                    chartName = "trend_room";
                                    charts.forEach(function(chart, index) {                        
                                        if (chart.renderTo.id === chartName) {
                                            if (!isXAxisSet){
                                                chart.xAxis[0].categories.push(x1);
                                                chart.update({
                                                    xAxis: {
                                                        categories: chart.xAxis[0].categories
                                                    },
                                                    shadow: {
                                                        color: chart.series[0].color
                                                    }
                                                });
                                                isXAxisSet = true;
                                            }
                                            shiftFlag = chart.series[0].data.length > 15;
                                            chart.series[0].addPoint([x1, y1], true, shiftFlag, false);
                                        }
                                    });
                                }
                            }

                            val1 = [], gaugeVal1 = 0, plotbands = [], currval = 0, valuechecked = false;
                            x1 = "", y1 = "", yGauge = "", chartId = 0, chartName = '';
                            shiftFlag = false, isXAxisSet = false;
                            if (jsonify[i].charttype.toLowerCase().indexOf("gauge") >= 0) {
                                if (jsonify[i].objectName.toLowerCase().indexOf("room") >= 0) continue;

                                currval = parseFloat(jsonify[i].currvalue).toFixed(2);
                                yGauge = gaugeVal1 = parseFloat(jsonify[i].roundedData);

                                var isMultipleDevice = false;
                                for (var cnt = 0; cnt < headerObj.length; cnt ++)
                                {
                                    if (jsonify[i].objectName == headerObj[cnt].name && headerObj[cnt].count > 1)
                                        isMultipleDevice = true;
                                }

                                if (jsonify[i].showinheader == '1') {
                                    // currval = parseFloat(jsonify[i].currvalue);
                                    currval = gaugeVal1;
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
                                            document.getElementById("spGaugeValue_" + jsonify[i].objectName).style.color = red;
                                            document.getElementById("spGaugeUOM_" + jsonify[i].objectName).style.color = red;
                                            document.getElementById("gaugech_img_" + jsonify[i].objectName).src = 'images/bell_red_ani.gif';
                                            valuechecked = true;
                                        }

                                        // if (!valuechecked) {
                                        //     if (currval >= parseFloat(jsonify[i].plotbands[0].from) && currval <= parseFloat(jsonify[i].plotbands[0].to)) {
                                        //         if (!isMultipleDevice) {
                                        //             if (document.getElementById("header_" + jsonify[i].objectName))
                                        //                 document.getElementById("header_" + jsonify[i].objectName).style.backgroundColor = jsonify[i].plotbands[0].color.trim();

                                        //             if (document.getElementById("sphead_" + jsonify[i].objectName))
                                        //                 document.getElementById("sphead_" + jsonify[i].objectName).style.color = rgblightblue;

                                        //             if (document.getElementById("spheaduom_" + jsonify[i].objectName))
                                        //                 document.getElementById("spheaduom_" + jsonify[i].objectName).style.color = rgblightblue;

                                        //             if (document.getElementById("spheadstatus_" + jsonify[i].objectName))
                                        //                 document.getElementById("spheadstatus_" + jsonify[i].objectName).style.color = rgblightblue;
                                        //         }
                                        //         document.getElementById("gaugech_img_" + jsonify[i].objectName).src = 'images/bell_red_ani.gif';
                                        //         valuechecked = true;
                                        //     }
                                        // }

                                        if (!valuechecked) {
                                            for (var a = 0; a < jsonify[i].plotbands.length - 1; a++) {
                                                if (currval >= parseFloat(jsonify[i].plotbands[a].from) && currval <= parseFloat(jsonify[i].plotbands[a].to)) {
                                                    if (!isMultipleDevice) {
                                                        if (document.getElementById("header_" + jsonify[i].objectName))
                                                            // document.getElementById("header_" + jsonify[i].objectName).style.backgroundColor = jsonify[i].headerbgcolor;
                                                            document.getElementById("header_" + jsonify[i].objectName).style.backgroundColor = jsonify[i].plotbands[a].color.trim();
                                                        
                                                        if (document.getElementById("sphead_" + jsonify[i].objectName))
                                                            document.getElementById("sphead_" + jsonify[i].objectName).removeAttribute("style");

                                                        if (document.getElementById("spheaduom_" + jsonify[i].objectName))
                                                            document.getElementById("spheaduom_" + jsonify[i].objectName).removeAttribute("style");

                                                        if (document.getElementById("spheadstatus_" + jsonify[i].objectName))
                                                            document.getElementById("spheadstatus_" + jsonify[i].objectName).removeAttribute("style");
                                                        document.getElementById("spGaugeValue_" + jsonify[i].objectName).style.color = green;
                                                        document.getElementById("spGaugeUOM_" + jsonify[i].objectName).style.color = green;
                                                        valuechecked = true;
                                                        break;
                                                    }
                                                }
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
                                                document.getElementById("gaugech_img_" + jsonify[i].objectName).src = 'images/bell_red_ani.gif';
                                                document.getElementById("spGaugeValue_" + jsonify[i].objectName).style.color = red;
                                                document.getElementById("spGaugeUOM_" + jsonify[i].objectName).style.color = red;
                                                valuechecked = true;
                                            }
                                        }
                                    }
                                    if (!valuechecked) {
                                        if (!isMultipleDevice) {
                                            if (document.getElementById("header_" + jsonify[i].objectName))
                                                document.getElementById("header_" + jsonify[i].objectName).style.backgroundColor  = jsonify[i].headerbgcolor;
                                                // document.getElementById("header_" + jsonify[i].objectName).removeAttribute("style");

                                            if (document.getElementById("sphead_" + jsonify[i].objectName))
                                                document.getElementById("sphead_" + jsonify[i].objectName).removeAttribute("style");

                                            if (document.getElementById("spheaduom_" + jsonify[i].objectName))
                                                document.getElementById("spheaduom_" + jsonify[i].objectName).removeAttribute("style");

                                            if (document.getElementById("spheadstatus_" + jsonify[i].objectName))
                                                document.getElementById("spheadstatus_" + jsonify[i].objectName).removeAttribute("style");
                                        }
                                        document.getElementById("gaugech_img_" + jsonify[i].objectName).src = 'images/bell_gr.png';
                                        document.getElementById("spGaugeValue_" + jsonify[i].objectName).style.color = red;
                                        document.getElementById("spGaugeUOM_" + jsonify[i].objectName).style.color = red;
                                    }
                                }
                                

                                document.getElementById("spGaugeValue_" + jsonify[i].objectName).innerHTML = gaugeVal1;
                                document.getElementById("spGaugeUOM_" + jsonify[i].objectName).innerHTML = jsonify[i].uom;
                                // if (valuechecked)
                                //     document.getElementById("spGaugeValue_" + jsonify[i].objectName).style.color = red;
                                // else
                                //     document.getElementById("spGaugeValue_" + jsonify[i].objectName).style.color = green;

                                chartName = "parentGauge_" + jsonify[i].objectName;
                                charts.forEach(function(chart, index) {                        
                                    if (chart.renderTo.id === chartName) {
                                        var point = chart.series[0].points[0];
                                        point.update(yGauge);
                                    }
                                });
                            }

                            val1 = [], gaugeVal1 = 0, plotbands = [], currval = 0, valuechecked = false;
                            x1 = "", y1 = "", yGauge = "", chartId = 0, chartName = '';
                            shiftFlag = false, isXAxisSet = false;
                            if (jsonify[i].charttype.toLowerCase().indexOf("trend") >= 0) {
                                currval = parseFloat(jsonify[i].currvalue).toFixed(2);
                                document.getElementById("spValue_" + jsonify[i].objectName).innerHTML = parseFloat(jsonify[i].currvalue).toFixed(2);
                                document.getElementById("spValueUOM_" + jsonify[i].objectName).innerHTML = jsonify[i].uom;
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

                                        // if (!valuechecked) {
                                        //     if (currval >= parseFloat(jsonify[i].plotbands[0].from) && currval <= parseFloat(jsonify[i].plotbands[0].to)) {
                                        //         if (document.getElementById("header_" + jsonify[i].objectName))
                                        //             document.getElementById("header_" + jsonify[i].objectName).style.backgroundColor = jsonify[i].plotbands[0].color.trim();

                                        //         if (document.getElementById("sphead_" + jsonify[i].objectName))
                                        //             document.getElementById("sphead_" + jsonify[i].objectName).style.color = rgblightblue;

                                        //         if (document.getElementById("spheaduom_" + jsonify[i].objectName))
                                        //             document.getElementById("spheaduom_" + jsonify[i].objectName).style.color = rgblightblue;

                                        //         if (document.getElementById("spheadstatus_" + jsonify[i].objectName))
                                        //             document.getElementById("spheadstatus_" + jsonify[i].objectName).style.color = rgblightblue;
                                        //         valuechecked = true;
                                        //     }
                                        // }

                                        if (!valuechecked) {
                                            for (var a = 0; a < jsonify[i].plotbands.length - 1; a++) {
                                                if (currval >= parseFloat(jsonify[i].plotbands[a].from) && currval <= parseFloat(jsonify[i].plotbands[a].to)) {
                                                    if (document.getElementById("header_" + jsonify[i].objectName))
                                                        // document.getElementById("header_" + jsonify[i].objectName).style.backgroundColor = jsonify[i].headerbgcolor;
                                                        document.getElementById("header_" + jsonify[i].objectName).style.backgroundColor = jsonify[i].plotbands[a].color.trim();
                                                    
                                                    if (document.getElementById("sphead_" + jsonify[i].objectName))
                                                        document.getElementById("sphead_" + jsonify[i].objectName).removeAttribute("style");

                                                    if (document.getElementById("spheaduom_" + jsonify[i].objectName))
                                                        document.getElementById("spheaduom_" + jsonify[i].objectName).removeAttribute("style");

                                                    if (document.getElementById("spheadstatus_" + jsonify[i].objectName))
                                                        document.getElementById("spheadstatus_" + jsonify[i].objectName).removeAttribute("style");
                                                    valuechecked = true;
                                                    break;
                                                }
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
                                            document.getElementById("header_" + jsonify[i].objectName).style.backgroundColor  = jsonify[i].headerbgcolor;
                                            // document.getElementById("header_" + jsonify[i].objectName).style.backgroundColor = green;

                                        if (document.getElementById("sphead_" + jsonify[i].objectName))
                                            document.getElementById("sphead_" + jsonify[i].objectName).removeAttribute("style");

                                        if (document.getElementById("spheaduom_" + jsonify[i].objectName))
                                            document.getElementById("spheaduom_" + jsonify[i].objectName).removeAttribute("style");

                                        if (document.getElementById("spheadstatus_" + jsonify[i].objectName))
                                            document.getElementById("spheadstatus_" + jsonify[i].objectName).removeAttribute("style");
                                    }
                                }

                                x1 = jsonify[i].category;
                                y1 = parseFloat(jsonify[i].devdata);
                                chartName = "dvGraph_" + jsonify[i].objectName;
                                charts.forEach(function(chart, index) {                        
                                    if (chart.renderTo.id === chartName) {
                                        if (!isXAxisSet) {
                                            chart.xAxis[0].categories.push(x1);
                                            var xaxisstep = 12;
                                            if (chart.xAxis[0].categories.length <= 10) 
                                                xaxisstep = 1;
                                            else if (chart.xAxis[0].categories.length > 10 && chart.xAxis[0].categories.length <= 20) 
                                                xaxisstep = 2;
                                            else if (chart.xAxis[0].categories.length > 20 && chart.xAxis[0].categories.length <= 30) 
                                                xaxisstep = 4;
                                            else if (chart.xAxis[0].categories.length > 30 && chart.xAxis[0].categories.length <= 40) 
                                                xaxisstep = 6;
                                            else if (chart.xAxis[0].categories.length > 40 && chart.xAxis[0].categories.length <= 50) 
                                                xaxisstep = 8;
                                            else if (chart.xAxis[0].categories.length > 50 && chart.xAxis[0].categories.length <= 60) 
                                                xaxisstep = 10;
                                            else if (chart.xAxis[0].categories.length > 60 && chart.xAxis[0].categories.length <= 72) 
                                                xaxisstep = 12;

                                            chart.update({
                                                xAxis: {
                                                    categories: chart.xAxis[0].categories,
                                                },
                                                shadow: {
                                                    color: chart.series[0].color
                                                }
                                            });
                                            chart.xAxis[0].update({
                                                labels: {
                                                    step: xaxisstep,
                                                    formatter: function () {
                                                        if (chart.xAxis[0].categories.length <= 40)
                                                            return this.value;
                                                        else
                                                            return  this.value.toString().substring(0, 2);
                                                    }
                                                }
                                            });
                                        }
                                        var shiftFlag = chart.series[0].data.length > 71;
                                        chart.series[0].addPoint([x1, y1], true, shiftFlag, false);
                                    }
                                });
                            }

                            val1 = [], gaugeVal1 = 0, plotbands = [], currval = 0, valuechecked = false;
                            x1 = "", y1 = "", yGauge = "", chartId = 0, chartName = '';
                            shiftFlag = false, isXAxisSet = false;
                            if (jsonify[i].charttype.toLowerCase().indexOf("value") >= 0) {
                                document.getElementById("spValue_" + jsonify[i].objectName).innerHTML = parseFloat(jsonify[i].currvalue).toFixed(2);
                                if (jsonify[i].showinheader == '1') {
                                    currval = parseFloat(jsonify[i].currvalue).toFixed(2);
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

                                        // if (!valuechecked) {
                                        //     if (currval >= parseFloat(jsonify[i].plotbands[0].from) && currval <= parseFloat(jsonify[i].plotbands[0].to)) {
                                        //         if (document.getElementById("header_" + jsonify[i].objectName))
                                        //             document.getElementById("header_" + jsonify[i].objectName).style.backgroundColor = jsonify[i].plotbands[0].color.trim();

                                        //         if (document.getElementById("sphead_" + jsonify[i].objectName))
                                        //             document.getElementById("sphead_" + jsonify[i].objectName).style.color = rgblightblue;

                                        //         if (document.getElementById("spheaduom_" + jsonify[i].objectName))
                                        //             document.getElementById("spheaduom_" + jsonify[i].objectName).style.color = rgblightblue;

                                        //         if (document.getElementById("spheadstatus_" + jsonify[i].objectName))
                                        //             document.getElementById("spheadstatus_" + jsonify[i].objectName).style.color = rgblightblue;
                                        //         valuechecked = true;
                                        //     }
                                        // }

                                        if (!valuechecked) {
                                            for (var a = 0; a < jsonify[i].plotbands.length - 1; a++) {
                                                if (currval >= parseFloat(jsonify[i].plotbands[a].from) && currval <= parseFloat(jsonify[i].plotbands[a].to)) {
                                                    if (document.getElementById("header_" + jsonify[i].objectName))
                                                        // document.getElementById("header_" + jsonify[i].objectName).style.backgroundColor = jsonify[i].headerbgcolor;
                                                        document.getElementById("header_" + jsonify[i].objectName).style.backgroundColor = jsonify[i].plotbands[a].color.trim();
                                                    
                                                    if (document.getElementById("sphead_" + jsonify[i].objectName))
                                                        document.getElementById("sphead_" + jsonify[i].objectName).removeAttribute("style");

                                                    if (document.getElementById("spheaduom_" + jsonify[i].objectName))
                                                        document.getElementById("spheaduom_" + jsonify[i].objectName).removeAttribute("style");

                                                    if (document.getElementById("spheadstatus_" + jsonify[i].objectName))
                                                        document.getElementById("spheadstatus_" + jsonify[i].objectName).removeAttribute("style");
                                                    valuechecked = true;
                                                    break;
                                                }
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
                                            document.getElementById("header_" + jsonify[i].objectName).style.backgroundColor  = jsonify[i].headerbgcolor;
                                            // document.getElementById("header_" + jsonify[i].objectName).style.backgroundColor = green;
                                            // document.getElementById("header_" + jsonify[i].objectName).removeAttribute("style");                                            

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

                            // x1 = jsonify[i].category;
                            // y1 = parseFloat(jsonify[i].devdata);
                            // yGauge = parseFloat(jsonify[i].roundedData);
                            // chartId = "chart_" + jsonify[i].objectName;
                            // gauzeChartId = "gauge_" + jsonify[i].objectName;

                            // charts.forEach(function(chart, index) {
                            // // lineCharts.forEach(function(chart, index) {
                            //     //Line Chart
                            //     if (chart.renderTo.id === chartId) {
                            //         if (!isXAxisSet){
                            //             chart.xAxis[0].categories.push(x1);
                            //             chart.update({
                            //                 xAxis: {
                            //                     categories: chart.xAxis[0].categories
                            //                 },
                            //                 shadow: {
                            //                     color: chart.series[0].color
                            //                 }
                            //             });
                            //             isXAxisSet = true;
                            //         }
                            //         // var shiftFlag = chart.series[0].data.length > 9;
                            //         chart.series[0].addPoint([x1, y1], true, shiftFlag, false);
                            //     }

                            //     // //Gauge Chart
                            //     // if (chart.renderTo.id === gauzeChartId) {
                            //     //     var point = chart.series[0].points[0];
                            //     //     point.update(yGauge);
                            //     // }
                            // });

                            // charts.forEach(function(chart, index) {
                            // // gaugeCharts.forEach(function(chart, index) {
                            //     //Gauge Chart
                            //     if (chart.renderTo.id === gauzeChartId) {
                            //         var point = chart.series[0].points[0];
                            //         point.update(yGauge);
                            //     }
                            // });
                        }
                    }
                    else {
                        clearInterval(autoupdate);
                        document.getElementById("header_mydash").style.backgroundColor  = red;
                        document.getElementById("spMyDash").style.color = rgblightblue;
                        alert("Server Not connected. Please check with System Operator and Reload page");
                    }
            },
            cache:false,
            });
        }

        function toggleac(devid, checkedval) {
            $.ajax({
                type: "GET",
                url : 'device/toggleac/' + devid + '/' + checkedval,
                dataType: "json",
                error: function() {},
                success: function (json) {console.log(json);
                }
            });
        }

        function reportDash(devicename) {
            window.open(window.location.href = 'reportdash/' + devicename);
        }
    </script>
@endsection