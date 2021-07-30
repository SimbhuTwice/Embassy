@extends('layouts.masterlogin')

@section('content')
    <section class="map-area">
		<div class="container">
			<div class="image-map-sect">
				<div class="logo-sect">
					<a href=""><img src="images/embassy-logo.png" alt=""></a>
					<h1>Welcome to Embassy Manyata Business Park</h1>
				</div>
				<div class="map-sect">
                    <img id="imgembassy" src="images/floorplan.jpg" usemap="#maps" alt="" class="map">
                    <map name="maps">
                        <!-- <area target="" alt="N1" title="N1" href="#" coords="386,657,440,732" shape="rect"> -->
                        <area target="" alt="Block C4 Elm" title="" href="{{ route('companymapselect', 2) }}" coords="588,292,638,308,621,362,576,348" shape="poly">
                        <!-- <area target="" alt="Block C4 Elm" title="" href="{{ route('companymapselect', 2) }}" coords="1032,514,1006,594,1088,611,1106,529" shape="poly"> -->
                    </map>
                    <div id="axome-tooltip" class="box sb"></div>
				</div>
				<div class="compass-sect">
					<img src="images/combass.png" alt="">
				</div>
			</div>
		</div>
	</section>

    <script>
        //remove this script ref <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js" />
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/maphilight/1.4.0/jquery.maphilight.min.js"></script>
        <script type="text/javascript" src="http://www.outsharked.com/scripts/jquery.imagemapster.js"></script>
        //reference http://jsfiddle.net/mt5pynn8/1/ for mapster
        
		$(document).ready(function (e) {
			// $('img[usemap]').rwdImageMaps();

			// $('area').on('click', function () {
			// 	alert($(this).attr('alt') + ' clicked');
			// });
            $('.map').maphilight();
            // $.fn.maphilight.defaults = {
            //     fill: true,
            //     fillColor: '000000',
            //     fillOpacity: 0.2,
            //     stroke: true,
            //     strokeColor: 'ff0000',
            //     strokeOpacity: 1,
            //     strokeWidth: 1,
            //     fade: true,
            //     alwaysOn: false,
            //     neverOn: false,
            //     groupBy: false,
            //     wrapClass: true,
            //     shadow: false,
            //     shadowX: 0,
            //     shadowY: 0,
            //     shadowRadius: 6,
            //     shadowColor: '000000',
            //     shadowOpacity: 0.8,
            //     shadowPosition: 'outside',
            //     shadowFrom: false
            // }

            $('area').hover(function() {
                $('#axome-tooltip').html('<span>' + $(this).attr('alt') + '</span>');
                $('#axome-tooltip').show();
            }, function() {
                $('#axome-tooltip').hide();
            });

            $('area').mousemove(function(e) {
                var offsetX = -140,//-210,
                    offsetY = -15;
                $('#axome-tooltip').offset({left: e.pageX + offsetX, top: e.pageY + offsetY});
            });
		});
	</script>
@endsection
