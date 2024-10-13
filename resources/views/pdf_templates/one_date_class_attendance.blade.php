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

        #subject {
            margin-right: 25px;
        }

        #year {
            margin-right: 8px;
        }

        #code {
            margin-right: 22px;
        }

        .table {
            width:80%;
            border-collapse: collapse;
            font-size: 10px;
            margin: 0 auto;
        }

        .table th,
        .table td {
            border: 1px solid #000;
            padding: 5px;
            text-align: left;
        }

        .table th {
            background-color: #f2f2f2;
        }

        .footer {
            font-size: 10px;
            text-align: left;
            width: 100%;
            position: fixed;
            bottom: 0;
        }

        .footer img {
            height: 50px;
            vertical-align: middle;
            min-width: 15%;
        }

        .footer table {
            width: 35%;
        }

        .footer td {
            vertical-align: middle;
        }

        #email {
            line-height: 3px;
        }
    </style>
</head>

<body>
    <header class="header">
        <img src="{{ public_path('assets/images/headers/header.png') }}" alt="School Logo">
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
        <p>Class Attendance for {{ \Carbon\Carbon::parse($selectedDate)->format('F d, Y') }}</p>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>NAME OF STUDENTS</th>
                <th>Attendance</th>
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
