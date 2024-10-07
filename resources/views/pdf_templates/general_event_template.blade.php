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
            width: 111%;
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

        .info2 p {
            margin: 0;
        }

        #certify {
            margin-top: 20px;
            width: 70%;
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
            text-align: center;
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

        .page-break {
            page-break-before: always;
        }

        .footer {
            position: absolute;
            bottom: -35px;
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
    @foreach ($attendee_records->chunk($itemsPerPage) as $chunk)
        @if (!$loop->first)
            <div class="page-break"></div> <!-- Add a page break between chunks -->
        @endif

        <header class="header">
            <img src="{{ public_path('assets/images/headers/header.png') }}" alt="School Logo">
        </header>

        <div class="info1">
            <p>ATTENDANCE SHEET</p>
            <p>{{ $event->name }} Event</p>
        </div>

        <div class="info2">
            <div class="info2-left">
                <p>Start Date: {{ $event->start_date ?? '_____' }}</p>
                <p>Instructor: {{ $facilitator->fname . ' ' . $facilitator->lname ?? '______________' }}</p>
            </div>
            <p id="certify">We certify that the following attendance was recorded for this event.</p>
        </div>

        <table class="table">
            <thead>
                <tr>
                    <th style="width: 30%;">NAME OF STUDENTS</th>
                    <th style="width: 15%;">Check-In</th>
                    <th style="width: 15%;">Check-Out</th>
                    <th style="width: 15%;">Date</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($chunk as $attendee_record)
                    <tr>
                        <td>{{ $attendee_record->master_list_member->full_name ?? 'N/A' }}</td>
                        <td>
                            {{ $attendee_record->check_in ? \Carbon\Carbon::parse($attendee_record->check_in)->format('h:i A') : '-' }}
                        </td>
                        <td>
                            {{ $attendee_record->check_out ? \Carbon\Carbon::parse($attendee_record->check_out)->format('h:i A') : '-' }}
                        </td>
                        <td>
                            {{ $attendee_record->created_at->format('Y-m-d') }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

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
                $x = 520;
                $y = 15;
                $text = "Page " . $pageNumber . " of " . $pageCount;
                $pdf->text($x, $y, $text, $font, $size);
            });
        }
    </script>

</body>

</html>
