@extends('layouts.app')

@section('title', 'Employee Schedule')

@section('content')

    <body>

        <!-- Main Wrapper -->
        <div class="main-wrapper">
            <div class="content">
                <h2 class="text-center my-4">Employee Weekly Schedule</h2>

                <div class="container mt-5">
                    <div class="card shadow-sm ">
                        <div class="card-body">
                            <h4 class="card-title text-center">Assigned Work Schedule</h4>
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered">
                                    <thead class=" text-white text-center">
                                        <tr>
                                            <th>ID</th>
                                            <th>Day</th>
                                            <th>Start Time</th>
                                            <th>End Time</th>
                                            <th>Section</th>
                                            <th>Week Reference</th>
                                            <th>Created At</th>
                                        </tr>
                                    </thead>
                                    <tbody class="text-center">
                                        @foreach($schedules as $schedule)
                                            <tr>
                                                <td>{{ $schedule['id'] }}</td>
                                                <td>{{ $schedule['day'] }}</td>
                                                <td>{{ \Carbon\Carbon::parse($schedule['start_time'])->format('H:i') }}</td>
                                                <td>{{ \Carbon\Carbon::parse($schedule['end_time'])->format('H:i') }}</td>
                                                <td>{{ ucfirst($schedule['section']) }}</td>
                                                <td>{{ \Carbon\Carbon::parse($schedule['week_reference'])->format('d M, Y') }}</td>
                                                <td>{{ \Carbon\Carbon::parse($schedule['created_at'])->format('H:i d M, Y') }}</td>
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