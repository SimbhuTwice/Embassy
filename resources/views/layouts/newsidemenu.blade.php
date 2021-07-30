<div class="col-sm-3 col-xl-3" id="menubar1">                
    <div class="card card-body">
        <a href="{{ route('dashboard') }}"
            @if (Request::segment(1) == 'dashboard') class="tab active" @else class="tab disable-style" @endif>
            <img class="icon" src="images/dash_ico.png" alt=""> Dash Board</a>
        <a href="#" 
            @if (Request::segment(1) == 'trends') class="tab active" @else class="tab disable-style" @endif>
            <img class="icon" src="images/tren_ico.png" alt=""> View Trends</a>
        <a href="{{ route('traffic') }}" 
            @if (Request::segment(1) == 'traffic') class="tab active" @else class="tab disable-style" @endif>
            <img class="icon" src="images/cctv_ico.png" alt=""> Traffic Updates</a>
        <!-- <a href="{{ route('mkspcb') }}" class="tab disable-style">
            <img class="icon" src="images/sto_ico.png" alt=""> KSPCB</a>

        <a href="{{ route('mkspcbcreate') }}" class="tab disable-style">
            <img class="icon" src="images/sto_ico.png" alt=""> KSPCB Create</a> -->
        <a href="{{ route('contactus') }}" 
            @if (Request::segment(1) == 'contactus') class="tab active" @else class="tab disable-style" @endif>
            <img class="icon" src="images/ph_ico.png" alt=""> Contact us 24x7</a>
    </div>
</div>