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
            min-height: 100vh;
        }

        @page {
            margin: 120px 30px 60px;
            /* Top space for the header */
        }

        header {
            position: fixed;
            top: -100px; /* Position the header above the content */
            left: 0;
            right: 0;
            height: 100px;
            text-align: center;
        }

        .header img {
            width: 60%;
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
            text-align: left;
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
            margin-left: 250px;
            margin-top: -43px;
            width: 40%;
            text-align: right;
        }

        .info2-right p {
            display: inline-block;
        }

        #year,
        #id {
            margin-left: 50px;
        }

        .info2 p {
            margin: 0;
        }

        #certify {
            margin-top: 20px;
            width: 100%;
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
            font-size: 9px;
            text-align: left;
        }

        #submitAndDate {
            font-size: 10px;
            margin-top: 50px;
            text-align: left;
        }

        #submitAndDate p {
            display: inline-block;
        }

        #submittedBy {
            margin-right: 200px;
        }

        .page-break {
            page-break-before: always;
        }

        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            font-size: 10px;
            text-align: left;
        }

        .footer img {
            height: 50px;
            margin-right: 10px;
            display: inline-block;
            vertical-align: middle;
        }

        .footer p {
            display: block;
            margin: 0;
            line-height: 1.5;
            vertical-align: middle;
        }
    </style>
</head>

<body>
    <header class="header">
        <img src="{{ public_path('assets/images/headers/header3.png') }}" alt="School Logo">
    </header>

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
        <p><span style="text-transform:capitalize;">{{$event->type}}</span> Attendance for {{ \Carbon\Carbon::parse($selectedDate)->format('F d, Y') }}</p>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>NAME OF STUDENTS</th>
                <th>Single Sign-in</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($monthsData as $month => $data)
                @foreach ($data['data'] as $studentName => $attendanceDates)
                    <tr>
                        <td>{{ $studentName }}</td>
                        <td class="text-center">
                            {{-- Check if the student is present on the selected date, otherwise show 0 --}}
                            {{ isset($attendanceDates[$selectedDate]) ? '1' : '0' }}
                        </td>
                    </tr>
                @endforeach
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <table>
            <tr>
                <td>
                    <img src="{{ public_path('assets/images/logos/ceitlogo.jpg') }}" alt="Logo">
                </td>
                <td style="text-align: left;">
                    <p id="email">Email address: ceit@csucc.edu.ph</p>
                    <p>Contact no.: 818-0205</p>
                </td>
            </tr>
        </table>
    </div>


    <div id="submitAndDate">
        <p id="submittedBy">Submitted By: _________________________</p>
        <p>Date of Submission: _________________________</p>
    </div>

    <script type="text/php">
        if (isset($pdf)) {
            $pdf->page_script(function ($pageNumber, $pageCount, $fontMetrics) use ($pdf) {
                $font = $fontMetrics->get_font("Arial", "normal");
                $size = 8;
                $x = 520;
                $y = 780;
                $pdf->text($x, $y, "Page $pageNumber of $pageCount", $font, $size);
            });
        }
    </script>
</body>

</html>
