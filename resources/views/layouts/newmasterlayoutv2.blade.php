<!DOCTYPE html>
<html lang="en">
    <head>
        <!-- Required meta tags -->
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />

        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>

        <!-- Bootstrap CSS -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css"
            integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous" />
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-ho+j7jyWK8fNQe+A12Hb8AhRq26LrZ/JpcUGGOn+Y7RsweNrtN/tE3MoK7ZeZDyx" crossorigin="anonymous"></script>
            
        <link rel="stylesheet" type="text/css" href="{{ asset('css/style.css') }}" />
        <link rel="stylesheet" type="text/css" href="{{ asset('css/common.css') }}" />
        <!-- <link href="{{ asset('assets/css/sb-admin-2.min.css') }}" rel="stylesheet"> -->
        <link href="{{ asset('css/multiselect.css') }}" rel="stylesheet" />

        <!-- WickedPicker -->
        <link rel="stylesheet" type="text/css" href="{{ asset('css/wickedpicker.min.css') }}" />
        <script src="https://code.jquery.com/jquery-3.4.0.min.js"></script>
        <script src="{{ asset('js/wickedpicker.min.js') }}" ></script>
        
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" />

        <!-- Material-Icon JS -->
	    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

        
        <title>MyDash By SiCare</title>
    </head>
    <body>
    <div id="preloader">
        <div id="loader"></div>
    </div>
    <!-- <div id="loader1" class="center"></div> -->
    <nav class="sticky-top">
        <section class="first-layer">
            <div class="container-fluid top-nav">
                <div class="container">
                    <div class="row">
                        <div class="col-sm-9">
                            <div class="column-img-text">
                                <img src="../images/si_care_logo.png" alt="" class="image-sicare">
                            </div>
                            <div class="top-text column-img-text">
                                <p><span id="spAnalogClock"></span></p>
                            </div>
                        </div>
                        <!-- <div class="col-sm-3">
                            <div class="top-log">
                                <a id="user-name" href="">Hi, {{ session('user_name') }}!</a>
                                <a id="login" href="#"><i class="fa fa-user" aria-hidden="true">
                                    <i class="fa fa-caret-down" aria-hidden="true"></i></i>
                                </a>
                                <a id="alert" href="#">
                                    <i class="fa fa-exclamation-triangle" aria-hidden=""></i>
                                </a>
                            </div>
                        </div> -->

                        <div class="col-sm-3">
                            <div class="row top-log">
                                <span id="user-name">
                                    Hi {{ session('user_name') }}!
                                    <input type="hidden" id="hdnUserId" value="{{ session('user_id') }}" />
                                </span>
                                <div class="dropdown show">
                                    <a id="login" class="btn dropdown-toggle1" href="#" role="button" id="dropdownMenuLink" 
                                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fa fa-user" aria-hidden="true"></i>
                                    </a>
                                    <!-- <div class="dropdown-menu" aria-labelledby="dropdownMenuLink" style="z-index: 2000;">
                                        <a class="dropdown-item" href="{{ route('logout') }}">Log-out</a>
                                    </div> -->
                                </div>                                
                                <!-- <a id="alert" href="#"><i class="fa fa-exclamation-triangle" aria-hidden=""></i></a> -->
                                <a id="logout" href="{{ route('logout') }}" title="logout"><i class="fa fa-sign-out" aria-hidden=""></i></a>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </section>

        <section class="second-layer">
            <div class="container">
                <div class="row">
                    <div class="col-sm-3 div-left">
                        <img class="logo" src="images/embassylogo.png" alt="" />
                        <div class="location">
                            <p>{{ session('branch_name') }}</p>
                        </div>
                    </div>
                    <div class="col-sm-9 div-right">
                        <div class="row box-row">
                            @yield('headcontent')
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </nav>

    <section class="main-layer">
        <div class="container">
            <div class="row">
                @include('layouts.newsidemenu')
                <div class="col-sm-9 pl-0" id="maincontent1">
                    @yield('content')
                </div>
            </div>
        </div>
    </section>

    <section class="last-layer">
        <div class="container-fluid bottom-foot">
            <div class="container">
                <div class="row">
                    <div class="col-sm-9 copyrights">
                        <p>Copyright © 2021. All rights reserved. Zaprify</p>
                    </div>
                    <div class="col-sm-3 update">
                        <p>Last updated 25 MAY 2021 - Ver.2.30</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @yield('script')
    </body>
    <script>
        $("html").on("contextmenu",function(e){
            return false;
        });

        // // ...when the elements exist:
        // $("a").click(dontWarn);
        // $("form").submit(dontWarn);
        // function dontWarn() {
        //     // Don't warn
        //     warnBeforeClose = false;

        //     // ...but if we're still on the page a second later, set the flag again
        //     setTimeout(function() {
        //         warnBeforeClose = true;
        //     }, 1000);
        // }

        document.onreadystatechange = function() {
            if (document.readyState !== "complete") {
                document.querySelector("body").style.visibility = "hidden";
                document.querySelector("#preloader").style.visibility = "visible";
                // document.querySelector("#loader1").style.visibility = "visible";
            }
            else {
                // document.querySelector("#preloader").style.visibility = "hidden";
                // document.querySelector("#loader1").style.visibility = "hidden";
                document.querySelector("body").style.visibility = "visible";
            }
        };

        var acc = document.getElementsByClassName("accordion");
        var i;
        for (i = 0; i < acc.length; i++) {
            acc[i].addEventListener("click", function () {
                this.classList.toggle("active");
                var panel = this.nextElementSibling;
                if (panel.style.display === "block") {
                    panel.style.display = "none";
                } 
                else {
                    panel.style.display = "block";
                }
            });
        }

        var acc = document.getElementsByClassName("accordion1");
        var i;

        for (i = 0; i < acc.length; i++) {
            acc[i].addEventListener("click", function () {
                this.classList.toggle("active");
                var panel1 = this.nextElementSibling;
                if (panel1.style.display === "block") {
                    panel1.style.display = "none";
                } 
                else {
                    panel1.style.display = "block";
                }
            });
        }

        setInterval(function() { analogClock(); }, 1000);
        function analogClock(){
            var monthName = ['JAN', 'FEB', 'MAR', 'APR', 'MAY', 'JUN', 'JUL', 'AUG', 'SEP', 'OCT', 'NOV', 'DEC'];
            var x = new Date();
            var ampm = x.getHours( ) >= 12 ? ' PM' : ' AM';
            hours = x.getHours( ) % 12;
            hours = hours ? hours : 12;
            hours = hours.toString().length == 1 ? (0 + hours.toString()) : hours;

            var minutes=x.getMinutes().toString()
            minutes = minutes.length == 1 ? (0 + minutes) : minutes;

            var seconds = x.getSeconds().toString()
            seconds = seconds.length == 1 ? (0 + seconds) : seconds;

            var dt = x.getDate().toString();
            dt = dt.length == 1 ? (0 + dt) : dt;

            var x1 = dt + " " + monthName[x.getMonth()] + " " + x.getFullYear(); 
            x1 = x1 + " / " +  hours + ":" +  minutes + ":" +  seconds + " " + ampm;
            document.getElementById('spAnalogClock').innerHTML = x1;
        }
    </script>
</html>