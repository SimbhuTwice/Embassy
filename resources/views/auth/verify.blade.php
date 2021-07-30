@extends('layouts.masterlog')

@section('content')
    <form class="user" method="POST" action="{{ route('verifing-auth') }}">
        @csrf
        
        <div class="row">
        <div class="form-group col-md-4">
            <label>User ID</label></div>
        <div class="form-group col-md-4">
            <input type="text" ng-model="form.user_id" name="user_id" autofocus class="form-control" placeholder="Enter User ID*" 
                value="{{ \Session::get('user_name') }}" readonly />
            @if ($errors->has('user_id'))
                <span class="invalid-feedback d-block" role="alert">
                    <strong>{{ $errors->first('user_id') }}</strong>
                </span>
            @endif
        </div>
        </div>

        <div class="row">
        <div class="form-group col-md-4">
            <label>Enter New Password</label></div>
        <div class="form-group col-md-4">
            <input type="password" ng-model="form.password" name="password" autofocus class="form-control" placeholder="Enter Password*" />
            @if ($errors->has('password'))
                <span class="invalid-feedback d-block" role="alert">
                    <strong>{{ $errors->first('password') }}</strong>
                </span>
            @endif
        </div>
        </div>

        <div class="row">
        <div class="form-group col-md-4">
            <label>Confirm Password</label></div>
        <div class="form-group col-md-4">
            <input type="password" ng-model="form.password_confirmation" name="password_confirmation" autofocus class="form-control" placeholder="Enter Confirm Password*" />
            @if ($errors->has('password_confirmation'))
                <span class="invalid-feedback d-block" role="alert">
                    <strong>{{ $errors->first('password_confirmation') }}</strong>
                </span>
            @endif
        </div>
        </div>

        <div class="row">
            <div class="col-md-4"></div>
            <div class="form-group col-md-4">
                <button type="submit" class="btn btn-success">Change</button>
            </div>
        </div>
    </form>
@endsection
