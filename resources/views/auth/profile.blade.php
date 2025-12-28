@extends('layouts.app')

@section('title', 'Profile') <!-- Title specific to this page -->

@section('content')


    <div class="content">
        <div class="page-header">
            <div class="page-title">
                <h4>Profile</h4>
                <h6>User Profile</h6>
            </div>
        </div>
        <!-- /product list -->
        <div class="card">
            <div class="card-body">
                <div class="profile-set">
                    <div class="profile-head">

                    </div>
                    <div class="profile-top">

                        <!-- <div class="ms-auto">
                                        <a href="javascript:void(0);" class="btn btn-submit me-2">Save</a>
                                        <a href="javascript:void(0);" class="btn btn-cancel">Cancel</a>
                                    </div> -->
                    </div>
                </div>
                <form action="{{route('update_profile')}}" method="POST">
                    @csrf
                <div class="row">
                    <div class="col-lg-6 col-sm-12">
                        <div class="input-blocks">
                            <label class="form-label">Name</label>
                            <input name="name" type="text" class="form-control" value="{{ $user->name }}">
                        </div>
                    </div>

                    <div class="col-lg-6 col-sm-12">
                        <div class="input-blocks">
                            <label>Email</label>
                            <input name="email" type="email" class="form-control" value="{{ $user->email }}">
                        </div>
                    </div>
                    <div class="col-lg-6 col-sm-12">
                        <div class="input-blocks">
                            <label class="form-label">Phone</label>
                            <input name="phone" type="text" value="{{ $user->phone }}">
                        </div>
                    </div>

                    <div class="col-lg-6 col-sm-12">
                        <div class="input-blocks">
                            <label class="form-label">Password</label>
                            <div class="pass-group">
                                <input name="password" type="password" class="pass-input form-control">
                                <span class="fas toggle-password fa-eye-slash"></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <button type="submit" href="javascript:void(0);" class="btn btn-submit me-2">Submit</button>
                        <a href="javascript:void(0);" class="btn btn-cancel">Cancel</a>
                    </div>
                </div>
                </form>
            </div>
        </div>
        <!-- /product list -->
    </div>

@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/home.css') }}">
@endpush

@push('scripts')
    <script src="{{ asset('js/home.js') }}"></script>
@endpush
