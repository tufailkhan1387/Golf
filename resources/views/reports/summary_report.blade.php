<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Summary Report</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 8px 12px;
            text-align: left;
            border: 1px solid #ddd;
        }
        th {
            background-color: #f2f2f2;
        }
        .header {
            font-size: 18px;
            font-weight: bold;
        }
        .sub-header {
            font-size: 16px;
            font-weight: bold;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <h2>Summary Report</h2>
    <p>Dear Admin,</p>
    <p>Below is the summary report for the week:</p>

    @foreach($reportData as $employee)
        <div>
            <p class="header">Employee: {{ $employee['name'] }}</p>
            
            
            <!-- Estimated Work Hours Table -->
            {{-- @if($employee['employee_type'] == "regular") --}}
            <p class="sub-header">Estimated Work Hours</p>
            <table>
                <thead>
                    <tr>
                        <th>Day</th>
                        <th>Start Time</th>
                        <th>End Time</th>
                        <th>Duration</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($employee['estimated_records'] as $day => $record)
                        <tr>
                            <td>{{ $day }}</td>
                            <td>{{ $record['start'] }}</td>
                            <td>{{ $record['end'] }}</td>
                            <td>{{ $record['duration'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <p>Total Hours: {{ $employee['total_estimated_hours'] }}</p>
            <p>Hourly Wage: ${{ $employee['salary'] }}</p>
            <p>Estimated Total Salary: ${{ $employee['estimated_salary'] }}</p>
            {{-- @endif --}}
            <!-- Actual Work Hours Table -->
            <p class="sub-header">Actual Work Hours</p>
            <table>
                <thead>
                    <tr>
                        <th>Day</th>
                        <th>Punch-in</th>
                        <th>Punch-out</th>
                        <th>Duration</th>
                        <th>Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($employee['actual_records'] as $record)
                        <tr>
                            <td>{{ $record['day'] }}</td>
                            <td>{{ $record['start'] }}</td>
                            <td>{{ $record['end'] }}</td>
                            <td>{{ $record['duration'] }}</td>
                            <td>${{ number_format($record['amount'], 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <p>Total Rounded Hours: {{$employee['total_rounded_hours'] }}</p>
            
            <p>Actual Salary: ${{ $employee['total_amount'] }}</p>

            <p>------------------------------------------------------------</p>
        </div>
    @endforeach

    <p>Best Regards,</p>
    <p>Punch Clock System</p>
</body>
</html>
