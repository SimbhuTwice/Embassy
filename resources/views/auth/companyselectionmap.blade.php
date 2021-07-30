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
                        <area target="" alt="Block C4 Elm" title="" href="{{ route('companymapselect', 2) }}" coords="1032,514,1006,594,1088,611,1106,529" shape="poly">
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
        $(document).ready(function (e) {
            $('img[usemap]').rwdImageMaps();

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
