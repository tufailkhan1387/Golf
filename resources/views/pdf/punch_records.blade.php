<!DOCTYPE html>
<html>
<head>
    <title>Punch Records Report</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .header { text-align: center; margin-bottom: 20px; }
        .employee-info { text-align: center; margin-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background: #007BFF; color: #ffffff; }
    </style>
</head>
<body>

    <h2 class="header">Punch Records Report</h2>

    <!-- Employee Information -->
    <div class="employee-info">
        <p><strong>Employee Name:</strong> {{ $employee->name ?? 'N/A' }}</p>
        <p><strong>Email:</strong> {{ $employee->email ?? 'N/A' }}</p>
        <p><strong>Phone:</strong> {{ $employee->phone ?? 'N/A' }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>S.No</th>
                <th>Day</th>
                <th>Punch In</th>
                <th>Punch Out</th>
                <th>Duration (minutes)</th>
                <th>Created At</th>
            </tr>
        </thead>
        <tbody>
            @foreach($punchRecords as $key => $record)
                <tr>
                    <td>{{ $key + 1 }}</td>
                    <td>{{$record->day}}</td>
                    <td>{{ \Carbon\Carbon::parse($record->punch_in)->format('H:i d M, Y') }}</td>
                    <td>{{ $record->punch_out ? \Carbon\Carbon::parse($record->punch_out)->format('H:i d M, Y') : 'Still Active' }}</td>
                    <td>{{ round($record->duration) }}</td>
                    <td>{{ \Carbon\Carbon::parse($record->created_at)->format('H:i d M, Y') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

</body>
</html>
