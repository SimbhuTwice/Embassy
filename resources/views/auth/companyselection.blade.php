@extends('layouts.masterlogin')

@section('content')
    <section class="branch">
        <div class="container">
            <div class="row">
                <div class="col-sm-12 col-md-6 image-left">
                    <img src="images/embassyzone.jpeg" alt="">
                    <div class="in-logo">
                      <img src="images/si_care_logo.png" alt="">
                    </div>
                    <input type="hidden" id="hdnUserId" value="{{ session('user_id') }}" />
                </div>
                <div class="col-sm-12 col-md-6 form-right">
                    <form class="user" method="POST" action="{{ route('company-select') }}">
                        @csrf
                        <img src="images/embassylogo.png" alt="" style="width: 290px;">
                        <h1>Hi Select Location</h1>
                        <h5>Filter to explore</h5>
                        <div class="input">
                        <input type="text" name="country_id" placeholder="Enter Country Name*"
                            value="India" readonly />
                        </div>
                        <div class="select">
                            <select name="state_id" id="state_id" autofocus="autofocus" tabindex="1">
                                <option value="">Select State*</option>
                                @foreach($states as $state)
                                    <option value="{{ $state->id }}" @if($state->id == $defaultStateId) selected @endif>{{ $state->state_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <span id="stateid" class="invalid-feedback d-block"></span>
                        @if ($errors->has('state_id'))
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $errors->first('state_id') }}</strong>
                            </span>
                        @endif

                        <div class="select">
                            <select name="city" id="city" autofocus="autofocus" tabindex="1">
                                <option value="">Select City*</option>
                                @foreach($branchList as $branch)
                                    <option value="{{ $branch->branchid }}">{{ $branch->branchname }}</option>
                                @endforeach
                            </select>
                        </div>
                        <span id="ct_name" class="invalid-feedback d-block"></span>
                        @if ($errors->has('city'))
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $errors->first('city') }}</strong>
                            </span>
                        @endif

                        <button type="submit" class="btn" name="btnVisit">Visit the store</button>
                    </form>
                </div>
            </div>
        </div>

        <script>
            $('select[name="state_id"]').bind('change',function(){
                var state_id = jQuery(this).val();
                if (state_id) {
                    jQuery.ajax({
                        url : '/getBranches/' + state_id,
                        type : "GET",
                        dataType : "json",
                        success:function(data)
                        {console.log(data);
                            jQuery('select[name="city"]').empty();
                            if (data != null) {
                                $('select[name="city"]').append('<option value="">Select City*</option>');
                                jQuery.each(data, function(key,value){
                                    $('select[name="city"]').append('<option value="'+ value.id +'">'+ value.branchname +'</option>');
                                });
                            }
                        }
                    });
                }
                else
                {
                    $('select[name="city"]').empty();
                }
            });

            $('button[name="btnVisit"]').bind('click',function() {
                var formvalid = true;
                document.getElementById('stateid').innerHTML = "";
                if (document.getElementById('state_id').value == '') {
                    formvalid = false;
                    document.getElementById('stateid').innerHTML = "<strong>State is required</strong>";
                }
                document.getElementById('ct_name').innerHTML = "";
                if (document.getElementById('city').value == '') {
                    formvalid = false;
                    document.getElementById('ct_name').innerHTML = "<strong>City is required</strong>";
                }
                return formvalid;
            });
        </script>
    </section>
@endsection
