@extends('layouts.masterlogin')

@section('content')
<form class="user" method="POST" action="{{ route('login-auth') }}">
    @csrf
    <div class="wrapper">
    <div class="one">
        <img src="{{ asset('assets/img/pexels-photo-1995010.jpeg') }}" class="image">
    </div>
    <div class="two">
        <div class="row mb-3">
            <div class="col-lg-12 col-md-12 p-0"><h2>Welcome</h2></div>
            <div class="col-lg-12 col-md-12 p-0"><h4>BMS Dashboard</h4></div>
        </div>
        <div class="row pr-4">
            <div class="col-lg-12 col-md-12 p-0">
                <small>Sign in below to start explore!</small>
            </div>
            <div class="col-lg-12 col-md-12 p-0">
                <input class="form-control mb-3" type="text" name="user_id" autofocus placeholder="Email ID" value="{{ old('user_id') }}" />
                @if ($errors->has('user_id'))
                    <span class="invalid-feedback d-block" role="alert">
                        <strong>{{ $errors->first('user_id') }}</strong>
                    </span>
                @endif
            </div>

            <div class="col-lg-12 col-md-12 p-0">
                <input class="form-control" type="password" name="password" placeholder="Password" />
                @if ($errors->has('password'))
                    <span class="invalid-feedback d-block" role="alert">
                        <strong>{{ $errors->first('password') }}</strong>
                    </span>
                @endif
            </div>
            <a href="#" class="ml-auto mb-0 text-sm">Forgot Password?</a>
            @if(count($errors) > 0)
                @foreach( $errors->all() as $message )
                    @if ($message == 'Password is incorrect' || $message == 'User ID/Password is incorrect')
                        <span class="invalid-feedback d-block" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @endif
                @endforeach
            @endif
        </div>
        <div class="row mb-3 pr-4">
            <small>By logging in, you are agreeing to our Terms and Conditions - (Ver. 1.0/2021)</small>
            <button type="submit" class="btn btn-warning text-center w-100">Explore</button> 
        </div>
        <div class="row bottom-right">
            <small>Do you need to create an account?</small>
            <div class="clearfix"></div>
            <small>You can sign up by entering your email below. We will notify the admin and get you set up!</small>
            <div class="clearfix"></div>
            <div class="input-group mb-3">
                <input type="text" class="form-control" />
                <div class="input-group-append">                    
                    <button class="btn btn-warning" type="button">Go
                        <!-- <span class="material-icons-outlined">arrow_right_alt</span> -->
                    </button>
                </div>
            </div>
        </div>
    </div>
    </div>
</form>
@endsection
