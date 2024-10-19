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
            margin-right: 80px;
        }

        .info2-right {
            margin-left: 80px;
            width: 40%;
            text-align: right;
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
            text-align: left;
        }

        #bottom-info1,
        #bottom-info2 {
            font-size: 10px;
            margin-top: 40px;
        }

        #bottom-info1 {
            margin-bottom: -50px;
        }

        #bottom-info2 {
            margin-bottom: 10px;
            width: 25%;
            text-align: left;
        }

        #bottom-info1 div {
            display: inline-block;
        }


        #bottom-info1 .left-side {
            margin-right: 400px;
            margin-left: -130px;
            text-align: left;
        }

        #bottom-info1 .right-side {
            text-align: left;

        }

        #name {
            font-weight: bold;
            text-transform: uppercase;
        }

        #prepared-by,
        #checked-by {
            line-height: 4px;
        }

        #invigilator,
        #faculty,
        #chairman {
            line-height: 2px;
            text-align: center;
        }

        #chairman {
            line-height: 2px;
            text-align: left;
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
        <p>MIDTERM EXAMINATION ATTENDANCE</p>
        <p>{{ $event->name }}</p>
        <p>{{ $event->semester }} Sem. S.Y {{ $event->school_year }}</p>
    </div>

    <div class="info2">
        <div class="info2-left">
            <p>Program Year & Section: {{ $event->program }} {{ $event->year }}</p>
            <p>Time: {{ $start_time }} - {{ $end_time }}</p>
        </div>
        <div class="info2-right">
            <p class="sem">Date:
                {{ $event->start_date == $event->end_date ? $event->start_date : $event->start_date . '-' . $event->end_date }}
            </p>
            <p id="year">Room: {{ $event->location }} </p>
        </div>
    </div>

    @foreach ($attendee_records->chunk($itemsPerPage) as $chunk)
        @if (!$loop->first)
            <div class="page-break"></div>
            <header class="header">
                <img src="{{ public_path('assets/images/headers/header.png') }}" alt="School Logo">
            </header>
        @endif

        <table class="table" style="margin-top: 20px; font-size: 8px;">
            <thead>
                <tr>
                    <th style="width: 10%; padding: 2px;">No.</th> <!-- Added No. column -->
                    <th style="width: 60%; padding: 2px;">NAME OF STUDENTS</th>
                    <th style="width: 30%; padding: 2px;">ATTENDANCE</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($chunk as $index => $attendee_record)
                    <tr>
                        <td style="padding: 2px;">{{ $loop->iteration + $loop->parent->index * $itemsPerPage }}</td>
                        <!-- Added number -->
                        <td style="padding: 2px;">{{ $attendee_record->master_list_member->full_name ?? 'N/A' }}</td>
                        <td style="padding: 2px;">
                            @if ($attendee_record->single_signin)
                                {{ \Carbon\Carbon::parse($attendee_record->single_signin)->format('Y-m-d g:i A') }}
                            @else
                                <!-- Leave blank if not checked in -->
                            @endif
                        </td>
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

    <div id="bottom-info1">
        <div class="left-side">
            <p id="prepared-by">Prepared by:</p>
            <p id="name">{{ $facilitator->fname . ' ' . $facilitator->lname }}</p>
            <p id="faculty">Faculty</p>
        </div>
        <div class="right-side">
            <p id="checked-by">Checked by:</p>
            <p id="name">{{ $invigilator }}</p>
            <p id="invigilator">Invigilator</p>
        </div>
    </div>


    <div id="bottom-info2">
        <p id="noted">Noted</p>
        <p id="name">RONAL A. MONZON, MIT</p>
        <p id="chairman">Chairman, IT Department</p>
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
