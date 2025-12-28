@extends('layouts.app')

@section('title', 'Kitchen Section Schedule')

@section('content')

    <body>

        <!-- Main Wrapper -->
        <div class="main-wrapper">
            <div class="content">
                <h2 class="text-center my-4">Employee Dashboard - Punch Clock System</h2>

                <div class="container mt-5">
                    <div class="row justify-content-center">
                        <div class="col-md-6">
                            <div class="card shadow-sm border-primary">
                                <div class="card-body text-center">
                                    <h4 class="card-title">Welcome, {{ auth()->user()->name }}</h4>
                                    <p class="card-text">Job Position: <strong>{{ auth()->user()->job_position }}</strong></p>
                                    <p class="card-text">Status: 
                                        <span class="badge {{ $isPunchedIn ? 'bg-success' : 'bg-danger' }}">
                                            {{ $isPunchedIn ? '🟢 Punched In' : '🔴 Not Punched In' }}
                                        </span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row justify-content-center mt-4">
                        <div class="col-md-6">
                            <div class="card shadow-sm border-secondary text-center">
                                <div class="card-body">
                                    @if(!$isPunchedIn)
                                        <form action="{{ route('punch.store') }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-success btn-lg">🟢 Punch In</button>
                                        </form>
                                    @else
                                        <form action="{{ route('punch.update') }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-danger btn-lg">🔴 Punch Out</button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row justify-content-center mt-4">
                        <div class="col-md-6">
                            <div class="card shadow-sm border-info">
                                <div class="card-body text-center">
                                    <h5 class="card-title">Last Recorded Punch</h5>

                                    <p class="card-text">Punch In: <strong>{{ $lastPunchIn ? $lastPunchIn->punch_in : 'N/A' }}</strong></p>
                                    <p class="card-text">Punch Out: <strong>{{ $lastPunchIn && $lastPunchIn->punch_out ? $lastPunchIn->punch_out : 'N/A' }}</strong></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </body>

@endsection