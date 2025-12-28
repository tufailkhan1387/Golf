@extends('layouts.app')

@section('title', 'Employee Punching History')

@section('content')

    <body>

        <!-- Main Wrapper -->
        <div class="main-wrapper">
            <div class="content">
                <h2 class="text-center my-4">Employee Punching History</h2>

                <div class="container mt-5">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h4 class="card-title text-center">Punch Records</h4>
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered">
                                    <thead class=" text-white text-center">
                                        <tr>
                                            <th>ID</th>
                                            <th>Day</th>
                                            <th>Punch In</th>
                                            <th>Punch Out</th>
                                            <th>Duration (hours)</th>
                                            <th>Created At</th>
                                        </tr>
                                    </thead>
                                    <tbody class="text-center">
                                        @foreach($history as $record)
                                        <tr>
                                            <td>{{ $record['id'] }}</td>
                                            <th>{{$record['day']}}</th>
                                            <td>{{ \Carbon\Carbon::parse($record['punch_in'])->format('H:i d M, Y') }}</td>
                                            <td>{{ $record['punch_out'] ? \Carbon\Carbon::parse($record['punch_out'])->format('H:i d M, Y') : 'Still Active' }}</td>
                                            <td>
                                                {{ $record['duration'] > 0 ? number_format($record['duration'] / 60, 2) . ' hrs (' . $record['duration'] . ' mins)' : '0 hrs (0 mins)' }}
                                            </td>
                                            <td>{{ \Carbon\Carbon::parse($record['created_at'])->format('H:i d M, Y') }}</td>
                                        </tr>
                                    @endforeach
                                    
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </body>

@endsection
