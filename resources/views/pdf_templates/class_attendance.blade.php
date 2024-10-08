<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Attendance List</title>
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
            margin: 10px 0 30px 0;
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

        .info2-right p {
            display: inline-block;
        }

        #year {
            margin-left: 50px;
        }

        .subject {
            margin-right: 15px;
        }

        .code{
            margin-right: 25px;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .table th,
        .table td {
            border: 1px solid #000;
            padding: 8px;
            font-size: 10px;
            text-align: center;
        }

        .page-break {
            page-break-before: always;
        }

        #submitAndDate {
            font-size: 10px;
            margin-top: 40px;
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
    </style>
</head>

<body>
    <!-- Loop through each month -->
    @foreach ($monthsData as $month => $attendanceData)
        @if (!$loop->first)
            <div class="page-break"></div> <!-- Page break between months -->
        @endif

        <!-- Header -->
        <header class="header">
            <img src="{{ public_path('assets/images/headers/header.png') }}" alt="School Logo">
        </header>

        @if ($loop->first)
            <div class="info2">
                <div class="info2-left">
                    <p class="subject">Course: {{ $event->subject ?? '______________' }}</p>
                    <p class="code">Code: {{ $event->subject_code ?? '______________' }}</p>
                    <p>Instructor: {{ $facilitator->fname . ' ' . $facilitator->lname ?? '______________' }}</p>
                </div>
                <div class="info2-right">
                    <p class="sem">Sem: {{ $event->semester ?? '_________' }} semester</p>
                    <p id="year">S.Y.: {{ $event->school_year ?? '_________' }}</p>
                </div>
            </div>
        @endif
        <div class="info1">
            <p>{{ $month }}</p>
        </div>
        <table class="table">
            <thead>
                <tr>
                    <th style="width: 40%;">NAME OF STUDENTS</th>
                    <th style="width: 30%;">Attendance</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($attendanceData as $record)
                    <tr>
                        <td>{{ $record['Name'] }}</td>
                        <td>{{ $record['Single_signin'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Footer -->
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
    @endforeach

    <div id="submitAndDate">
        <p id="submittedBy">Submitted By: _________________________</p>
        <p>Date of Submission: _________________________</p>
    </div>

    <script type="text/php">
        if (isset($pdf)) {
            $pdf->page_script(function ($pageNumber, $pageCount, $fontMetrics) use ($pdf) {
                $font = $fontMetrics->get_font("Arial", "normal");
                $size = 10;
                $x = 520; // X-coordinate for the text
                $y = 15;  // Y-coordinate for the text
                $text = "Page " . $pageNumber . " of " . $pageCount;
                $pdf->text($x, $y, $text, $font, $size);
            });
        }
    </script>

</body>

</html>
