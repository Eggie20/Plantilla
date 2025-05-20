<html>
<head>
    <meta charset="UTF-8">
    <title>Years of Service Report</title>
    <style>
        @media screen {
            .print-only { display: none; }
        }
        @media print {
            .no-print { display: none; }
            body * {
                visibility: hidden;
            }
            .print-content, .print-content * {
                visibility: visible;
            }
            .print-content {
                position: absolute;
                left: 0;
                top: 0;
            }
        }
        
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .header h1 {
            font-size: 24px;
            margin: 0;
        }
        
        .header h2 {
            font-size: 18px;
            margin: 5px 0;
        }
        
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        
        .table th, .table td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }
        
        .table th {
            background-color: #f5f5f5;
            font-weight: bold;
        }
        
        .footer {
            margin-top: 30px;
            text-align: right;
            font-style: italic;
        }
        
        .print-btn {
            margin: 20px 0;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="print-content">
        <div class="header">
            <h1>Municipality of Magallanes</h1>
            <h2>Years of Service Report</h2>
            <p>Generated on: {{ $today }}</p>
        </div>

        <table class="table">
            <thead>
                <tr>
                    <th>Employee Name</th>
                    <th>Position</th>
                    <th>Office</th>
                    <th>Original Appointment</th>
                    <th>Years of Service</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($personnels as $personnel)
                <tr>
                    <td>{{ $personnel->lastName }}, {{ $personnel->firstName }} {{ $personnel->middleName }}</td>
                    <td>{{ $personnel->position }}</td>
                    <td>{{ $personnel->office->name ?? 'N/A' }}</td>
                    <td>{{ $personnel->originalAppointment }}</td>
                    <td>{{ $personnel->yearsOfService ?? 'N/A' }}</td>
                    <td>{{ $personnel->status }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="footer">
            Printed on: {{ now()->format('F j, Y g:i A') }}
        </div>
    </div>

    <div class="print-btn no-print">
        <button onclick="window.print()" class="btn btn-primary">
            <i class="fas fa-print"></i> Print Report
        </button>
    </div>
</body>
</html>