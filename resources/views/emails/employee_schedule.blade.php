<!DOCTYPE html>
<html>
<head>
    <title>Your Weekly Work Schedule</title>
</head>
<body>
    <h2>Hello,</h2>
    <p>Here is your work schedule for this week:</p>

    <table border="1" cellpadding="10" cellspacing="0">
        <thead>
            <tr>
                <th>Day</th>
                <th>Start Time</th>
                <th>End Time</th>
                <th>Section</th>
            </tr>
        </thead>
        <tbody>
            @foreach($scheduleData as $schedule)
                <tr>
                    <td>{{ $schedule['day'] }}</td>
                    <td>{{ $schedule['start_time'] }}</td>
                    <td>{{ $schedule['end_time'] }}</td>
                    <td>{{ $schedule['section'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <p>Best Regards,</p>
    <p>Punch Clock System</p>
</body>
</html>
