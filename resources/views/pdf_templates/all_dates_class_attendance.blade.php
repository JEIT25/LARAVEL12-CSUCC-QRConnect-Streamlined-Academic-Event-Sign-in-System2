<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Class Attendance</title>
    <style>
        body {
            font-family: 'DejaVu Sans', 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            text-align: center;
            position: relative;
        }

        header {
            height: 100px;
            background: #fff;
        }

        .header img {
            width: 70%;
            max-height: 70px;
            object-fit: contain;
        }

        .info1 {
            margin: 10px 0 20px 0;
            font-weight: bold;
            font-size: 12px;
            line-height: 3px;
        }

        .info2 {
            font-size: 10px;
            margin-top: 10px;
            text-align: center;
        }

        .info2-left,
        .info2-right {
            display: inline-block;
            vertical-align: top;
        }

        .info2-left {
            margin-right: 50px;
        }

        .info2-right {
            margin-left: 80px;
            width: 40%;
            text-align: right;
        }

        #subject, {
            margin-right: 25px;
        }

        #year {
            margin-right: 8px;
        }

        #code {
            margin-right: 22px;
        }


        .table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8px;
            /* Reduce font size for compact layout */
        }

        .table th,
        .table td {
            border: 1px solid #000;
            padding: 2px;
            /* Minimized padding */
            text-align: left;
        }

        .table th {
            font-size: 8px;
            /* Smaller font for headers */
        }

        .table td {
            font-size: 8px;
            /* Smaller font for attendance data */
        }

        .page-break {
            page-break-before: always;
        }

        #submitAndDate {
            font-size: 10px;
            margin-top: 60px;
            text-align: left;
        }

        #submitAndDate p {
            display: inline-block;
        }

        #submittedBy {
            margin-right: 200px;
        }

        .footer {
            font-size: 10px;
            text-align: left;
            width: 30%;
            position: fixed;
            bottom: 0;
        }

        .footer img {
            height: 50px;
            vertical-align: middle;
        }

        .footer table {
            width: 100%;
        }

        .footer td {
            vertical-align: middle;
            line-height: 4px;
        }

        /* Styles for month grouping */
        .month-group {
            border-top: 2px solid #000;
            margin-top: 20px;
        }

        .month-header {
            font-weight: bold;
            font-size: 10px;
            background-color: #f2f2f2;
        }

        .month-header th {
            border: 1px solid #000;
        }
    </style>
</head>

<body>
    <header class="header">
        <img src="{{ public_path('assets/images/headers/header3.png') }}" alt="School Logo">
    </header>

    @if (isset($monthsData) && count($monthsData) > 0)
        <div class="info2">
            <div class="info2-left">
                <p id="subject">Course: {{ $event->subject ?? '______________' }}</p>
                <p id="code">Code: {{ $event->subject_code ?? '______________' }}</p>
                <p>Instructor: {{ $facilitator->fname . ' ' . $facilitator->lname ?? '______________' }}</p>
            </div>
            <div class="info2-right">
                <p class="sem">Sem: {{ $event->semester ?? '_____' }} semester</p>
                <p id="year">S.Y.: {{ $event->school_year ?? '______________' }}</p>
            </div>
        </div>

        <div class="info1">
            <p>Class Attendance</p>

        </div>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>NAME OF STUDENTS</th>
                    @foreach ($monthsData as $month => $monthData)
                        <th colspan="{{ count($monthData['dates']) }}" class="text-center">{{ $month }}</th>
                    @endforeach
                </tr>
                <tr>
                    <th></th>
                    @foreach ($monthsData as $monthData)
                        @foreach ($monthData['dates'] as $date)
                            <th class="text-center">{{ \Carbon\Carbon::parse($date)->format('m-d') }}</th>
                        @endforeach
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @php
                    // Collect all student names from the monthsData array
                    $allStudents = [];
                    foreach ($monthsData as $monthData) {
                        foreach ($monthData['data'] as $studentName => $attendance) {
                            $allStudents[$studentName] = $studentName;
                        }
                    }
                @endphp

                @foreach ($allStudents as $studentName)
                    <tr>
                        <td>{{ $studentName }}</td>
                        @foreach ($monthsData as $monthData)
                            @foreach ($monthData['dates'] as $date)
                                <td class="text-center">
                                    {{ isset($monthData['data'][$studentName][$date]) ? 1 : 0 }}
                                </td>
                            @endforeach
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <div id="submitAndDate">
        <p id="submittedBy">Submitted By: _________________________</p>
        <p>Date of Submission: _________________________</p>
    </div>

    <div class="footer">
        <table>
            <tr>
                <td style="width: 15%;">
                    <img src="{{ public_path('assets/images/logos/ceitlogo.jpg') }}" alt="Logo">
                </td>
                <td style="width: 85%; text-align: left;">
                    <p>Email address: ceit@csucc.edu.ph</p>
                    <p>Contact no.: 818-0205</p>
                </td>
            </tr>
        </table>
    </div>

    <script type="text/php">
        if (isset($pdf)) {
            $pdf->page_script(function ($pageNumber, $pageCount, $fontMetrics) use ($pdf) {
                $font = $fontMetrics->get_font("Arial", "normal");
                $size = 8; /* Smaller footer font */
                $x = 520;
                $y = 780;
                $pdf->text($x, $y, "Page $pageNumber of $pageCount", $font, $size);
            });
        }
    </script>
</body>

</html>
