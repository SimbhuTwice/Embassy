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
    <div class="pcb-sect pl-3">
        <div class="row">
            <img class="logo" src="images/karnatakapcb.jpg" alt="" />
        </div>
    </div>
@endsection

@section('script')
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
                    if (json.headerObjectNameList[i].countValue == 'count')
                        document.getElementById("sphead_" + json.headerObjectNameList[i].name).innerHTML = json.headerObjectNameList[i].activevalue + "/" + json.headerObjectNameList[i].count;
                    else
                        document.getElementById("sphead_" + json.headerObjectNameList[i].name).innerHTML = json.headerObjectNameList[i].currvalue;
                    // document.getElementById("sphead_" + json.headerObjectNameList[i].name).innerHTML = json.headerObjectNameList[i].activevalue + "/" + json.headerObjectNameList[i].count;
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
                        document.getElementById("header_" + json.headerObjectNameList[i].name).style.backgroundColor  = json.headerObjectNameList[i].headerbgcolor;
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
                                                        document.getElementById("header_" + jsonify[i].objectName).style.backgroundColor = jsonify[i].headerbgcolor;
                                                        // document.getElementById("header_" + jsonify[i].objectName).style.backgroundColor = jsonify[i].plotbands[a].color.trim();
                                                    
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
                                //         valuechecked = true;
                                //     }
                                // }

                                if (!valuechecked) {
                                    for (var a = 0; a < jsonify[i].plotbands.length - 1; a++) {
                                        if (currval >= parseFloat(jsonify[i].plotbands[a].from) && currval <= parseFloat(jsonify[i].plotbands[a].to)) {
                                            if (document.getElementById("header_" + jsonify[i].objectName))
                                                document.getElementById("header_" + jsonify[i].objectName).style.backgroundColor = jsonify[i].headerbgcolor;
                                                // document.getElementById("header_" + jsonify[i].objectName).style.backgroundColor = jsonify[i].plotbands[a].color.trim();
                                            
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
                                    document.getElementById("header_" + jsonify[i].objectName).style.backgroundColor  = jsonify[i].headerbgcolor;
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
                                                    document.getElementById("header_" + jsonify[i].objectName).style.backgroundColor = jsonify[i].headerbgcolor;
                                                    // document.getElementById("header_" + jsonify[i].objectName).style.backgroundColor = jsonify[i].plotbands[a].color.trim();
                                                
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
                                                document.getElementById("header_" + jsonify[i].objectName).style.backgroundColor = jsonify[i].headerbgcolor;
                                                // document.getElementById("header_" + jsonify[i].objectName).style.backgroundColor = jsonify[i].plotbands[a].color.trim();
                                            
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
                                    document.getElementById("header_" + json.headerObjectNameList[i].name).style.backgroundColor  = json.headerObjectNameList[i].headerbgcolor;
                                    // document.getElementById("header_" + json.headerObjectNameList[i].name).style.backgroundColor  = green;
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

                                                // if (!valuechecked) {
                                                //     if (currval >= parseFloat(jsonify[i].plotbands[0].from) && currval <= parseFloat(jsonify[i].plotbands[0].to)) {
                                                //         if (document.getElementById("header_" + jsonify[i].objectName))
                                                //             document.getElementById("header_" + jsonify[i].objectName).style.backgroundColor = jsonify[i].plotbands[0].color.trim();

                                                //         document.getElementById("sphead_" + jsonify[i].objectName).style.color = rgblightblue;
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
                                                                document.getElementById("header_" + jsonify[i].objectName).style.backgroundColor = jsonify[i].headerbgcolor;
                                                                // document.getElementById("header_" + jsonify[i].objectName).style.backgroundColor = jsonify[i].plotbands[a].color.trim();
                                                            
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
                                                document.getElementById("header_" + jsonify[i].objectName).style.backgroundColor  = jsonify[i].headerbgcolor;
                                                // document.getElementById("header_" + jsonify[i].objectName).style.backgroundColor  = green;

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
                                        //         valuechecked = true;
                                        //     }
                                        // }

                                        if (!valuechecked) {
                                            for (var a = 0; a < jsonify[i].plotbands.length - 1; a++) {
                                                if (currval >= parseFloat(jsonify[i].plotbands[a].from) && currval <= parseFloat(jsonify[i].plotbands[a].to)) {
                                                    if (document.getElementById("header_" + jsonify[i].objectName))
                                                        document.getElementById("header_" + jsonify[i].objectName).style.backgroundColor = jsonify[i].headerbgcolor;
                                                        // document.getElementById("header_" + jsonify[i].objectName).style.backgroundColor = jsonify[i].plotbands[a].color.trim();
                                                    
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
                                                document.getElementById("header_" + jsonify[i].objectName).style.backgroundColor  = jsonify[i].headerbgcolor;
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
                                                        document.getElementById("header_" + jsonify[i].objectName).style.backgroundColor = jsonify[i].headerbgcolor;
                                                        // document.getElementById("header_" + jsonify[i].objectName).style.backgroundColor = jsonify[i].plotbands[a].color.trim();
                                                    
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
                                                        document.getElementById("header_" + jsonify[i].objectName).style.backgroundColor = jsonify[i].headerbgcolor;
                                                        // document.getElementById("header_" + jsonify[i].objectName).style.backgroundColor = jsonify[i].plotbands[a].color.trim();
                                                    
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
                    else 
                        document.getElementById("header_mydash").style.backgroundColor  = red;
            },
            cache:false,
            });
        }
    </script>
@endsection
