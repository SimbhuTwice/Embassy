@extends('layouts.masterlogin')

@section('content')
    <section class="home">
        <div class="container">
            <div class="row">
            <div class="col-sm-8 left-sec">
                <img src="images/home_ima.jpg" alt="">
                <div class="in-logo">
                    <img src="images/si_care_logo.png" alt="">
                </div>                
            </div>
            <div class="col-sm-4 right-sec">
                <form class="log pl-2" method="POST" action="{{ route('login-auth') }}">
                    @csrf
                    <img src="images/my_dash_logo.png" alt="" class="image-bg">
                    <p>Sign in below to start explore!</p>
                
                    <input type="text" ng-model="form.user_id" name="user_id" autofocus class="form-control" placeholder="Email ID*"
                        value="{{ old('user_id') }}" />
                    @if ($errors->has('user_id'))
                        <span class="invalid-feedback d-block" role="alert">
                            <strong>{{ $errors->first('user_id') }}</strong>
                        </span>
                    @endif
                    <br>
                    <input type="password" ng-model="form.password" name="password" autofocus class="form-control" placeholder="Password*" />
                    @if ($errors->has('password'))
                        <span class="invalid-feedback d-block" role="alert">
                            <strong>{{ $errors->first('password') }}</strong>
                        </span>
                    @endif

                    @if(count($errors) > 0)
                        @foreach( $errors->all() as $message )
                            @if ($message == 'Password is incorrect' || $message == 'User ID/Password is incorrect' || 'You have already logged in')
                                <span class="invalid-feedback d-block" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @endif
                        @endforeach
                    @endif
                
                    <a href="#" style="float: right;">Forgot Password?</a><br><br>
                    <div class="terms">
                        <p>By logging in, you are agreeing to our <a href="#"><u>Terms and condition</u></a></p><p>-(Ver-1.0/2021)</p>
                    </div>
                    <div class="exp"><button type="submit" class="btn exp">Explore</button></div>
                </form>

                <div class="in-box">
                    <form class="pl-2">
                        @csrf
                        <h3>Do you need to create an account?</h3>
                        <p>You can sign up by entering your email below.<br>We will notify the admin and get you set up!</p>
                    
                        <div class="input-group mb-3">
                            <input type="email" class="form-control" placeholder="Email" aria-label="Recipient's username" aria-describedby="basic-addon2">
                            <div class="input-group-append">
                                <button class="btn" type="button"><i class="fa fa-arrow-right"></i></button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            </div>
        </div>
    </section>
@endsection
