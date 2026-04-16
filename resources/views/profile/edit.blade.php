@extends('layouts.app')

@section('content')
<div class="container">

    <h2 class="mb-4 font-semibold text-xl text-gray-800 leading-tight">Profile</h2>

    <!-- UPDATE PROFILE -->
    <div class="card mb-4">
        <div class="card-header">Update Profile Information</div>
        <div class="card-body">
            @include('profile.partials.update-profile-information-form')
        </div>
    </div>

    <!-- UPDATE PASSWORD -->
    <div class="card mb-4">
        <div class="card-header">Update Password</div>
        <div class="card-body">
            @include('profile.partials.update-password-form')
        </div>
    </div>

    <!-- DELETE ACCOUNT -->
    <div class="card mb-4">
        <div class="card-header text-danger">Delete Account</div>
        <div class="card-body">
            @include('profile.partials.delete-user-form')
        </div>
    </div>

</div>
@endsection