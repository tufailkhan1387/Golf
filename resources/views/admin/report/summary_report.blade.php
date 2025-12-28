<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Summary Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 100%;
            margin: auto;
            padding: 20px;
        }
        h1, h4 {
            text-align: center;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f4f4f4;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            font-size: 12px;
            color: #555;
        }
    </style>
</head>
<body>
    <div class="container">
        <div>
            <img src="assets/img/timbor-logo.png" style="width:120px" />
        </div>
        <h1>Summary Report</h1>
        <h4>Generated on: {{ $reportDate }}</h4>

        <h2>Total Stock Used</h2>
        <table>
            <tr>
                <th>Total Lineal Metres</th>
                <td>{{ $totalLength }} mm</td>
            </tr>
            <tr>
                <th>Total Cost</th>
                <td>${{ $totalCost }}</td>
            </tr>
        </table>

        <h2>Total Waste</h2>
        <table>
            <tr>
                <th>Total Waste Length</th>
                <td>{{ $wasteLength }} mm</td>
            </tr>
            <tr>
                <th>Waste Percentage</th>
                <td>{{ $wastePercentage }}%</td>
            </tr>
        </table>

        <div class="footer">
            <p>&copy; {{ date('Y') }} | Generated Report</p>
        </div>
    </div>
</body>
</html>
