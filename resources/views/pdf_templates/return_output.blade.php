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
            min-height: 100vh;
        }

        header {
            height: 100px;
            background: #fff;
            z-index: 1000;
        }

        .header img {
            width: 100%;
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
            margin-left: 230px;
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
    <!-- Static Information -->
    <header class="header">
        <img src="{{ public_path('assets/images/headers/header2.png') }}" alt="School Logo">
    </header>

    <div class="info1">
        <p>RECORD SHEET</p>
        <p>RETURN STUDENTS' OUTPUT</p>
    </div>

    <div class="info2">
        <div class="info2-left">
            <p>Course: {{ $event->subject ?? '______________' }}</p>
            <p>Code: {{ $event->subject_code ?? '_____' }}</p>
            <p>Instructor: {{ $facilitator->fname . ' ' . $facilitator->lname }}</p>
        </div>
        <div class="info2-right">
            <p id="sem">Sem: {{ $event->semester ?? '_____' }} semester</p>
            <p id="year">S.Y.: {{ $event->school_year ?? '_____' }}</p>
        </div>
        <p id="certify">We certify that we received the following documents below.</p>
    </div>

    @php
        $rowNumber = 1; // Initialize row number counter
    @endphp

    @foreach ($attendee_records->chunk($itemsPerPage) as $chunk)
        @if (!$loop->first)
            <div class="page-break"></div>
        @endif

        <table class="table" style="margin-top: 20px; font-size: 8px;">
            <thead>
                <tr>
                    <th style="width: 50%; padding: 5px;">NAME OF STUDENTS</th>
                    <th style="width: 15%; padding: 5px; fomt-weight: bold;">{{$event->name}}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($chunk as $attendee_record)
                    <tr>
                        <td style="padding: 2px;">{{$rowNumber++}}.{{ $attendee_record->master_list_member->full_name ?? 'N/A' }}</td>
                        <td style="padding: 2px; text-align: center;">&#10003;</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Repeated footer -->
        <div class="footer">
            <table style="width: 100%; margin-top: 40px;">
                <tr>
                    <td style="width: 60%; text-align: left;">
                        <img src="{{ public_path('assets/images/logos/ceitlogo.jpg') }}" alt="Logo">
                        <div style="display: inline-block; text-align: left;">
                            <p>Email address: ceit@csucc.edu.ph</p>
                            <p>Contact no.: 818-0205</p>
                        </div>
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
