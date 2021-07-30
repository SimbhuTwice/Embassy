@extends('layouts.newmasterlayoutv2')

@section('headcontent')
@endsection

@section('content')
    <form class="user" method="POST" action="{{ route('mkspcbstore') }}">
        @csrf

        <div class="row">
            <div class="col-md-3">Consent No</div>
            <div class="col-md-3">
                <input type="text" id="txtconsentno" name="txtconsentno" />
            </div>
        </div>

        <div class="row">
            <div class="col-md-3">Issue Date</div>
            <div class="col-md-3">
                <input type="date" id="txtissuedate" name="txtissuedate">
            </div>
            <div class="col-md-3">Valid Till Date</div>
            <div class="col-md-3">
                <input type="date" id="txtvaliditydate" name="txtvaliditydate">
            </div>
        </div>

        @foreach($resourceList as $resrcObj)
            <div class="row mt-3 mb-2">
                <div class="col-sm-12">
                    {{ $resrcObj['resourceName'] }} {{ $resrcObj['resourceAct'] }}
                </div>
                @foreach($resrcObj['resourceGroupList'] as $resrcgrpObj)
                    <div class="col-sm-6">
                        {{ $resrcgrpObj['resourceGroupName'] }}
                        @if ($resrcgrpObj['itemCount'] == 0)
                            <input type="text" class="wd-120" name="txtresrcgrp_{{ $resrcgrpObj['resourceGroupNo'] }}" id="txtresrcgrp_{{ $resrcgrpObj['resourceGroupNo'] }}" />
                            {{ $resrcgrpObj['resourceGroupUOM'] }}
                        @else
                            @foreach($resrcgrpObj['resourceGrpItemList'] as $resrcitemObj)
                                <div class="col-sm-12">

                                    {{ $resrcitemObj['resourceItemName'] }}
                                    <input type="text" class="wd-120" name="txtresrcitem_{{ $resrcitemObj['resourceItemId'] }}" id="txtresrcitem_{{ $resrcitemObj['resourceItemId'] }}" />
                                    {{ $resrcitemObj['resourceItemUOM'] }}
                                </div>
                            @endforeach
                        @endif
                    </div>
                @endforeach
            </div>
        @endforeach
        
        <div class="row">
            <div class="col-md-4"></div>
            <div class="form-group col-md-4">
                <button type="submit" class="btn btn-success">Save</button>
                <a href="{{ route('mkspcbcreate') }}" class="btn btn-warning">Cancel</a>
            </div>
        </div>
    </form>
@endsection

@section('script')
    <script>
        window.onload= function() {
            document.getElementById('txtissuedate').value = (new Date()).toISOString().substr(0, 10);
            document.getElementById('txtvaliditydate').value = (new Date()).toISOString().substr(0, 10);
            document.querySelector("#preloader").style.visibility = "hidden";
            document.querySelector(".second-layer").style.display = "none";
        }
    </script>
@endsection
