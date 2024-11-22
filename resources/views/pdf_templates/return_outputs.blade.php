<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Returned Students' Outputs</title>
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
            font-size: 14px;
            line-height: 1.5;
        }

        .info2 {
            font-size: 12px;
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

        .info2 p {
            margin: 5px 0;
        }

        #certify {
            margin-top: 20px;
            width: 100%;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            font-size: 10px;
        }

        .table th,
        .table td {
            border: 1px solid #000;
            padding: 5px;
            text-align: center;
        }

        .table th {
            font-weight: bold;
            background-color: #f2f2f2;
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

        #submitAndDate {
            font-size: 12px;
            margin-top: 50px;
            text-align: left;
        }

        #submitAndDate p {
            margin: 0 20px;
            display: inline-block;
        }

        .page-break {
            page-break-before: always;
        }
    </style>
</head>

<body>
    <header class="header">
        <img src="{{ public_path('assets/images/headers/header2.png') }}" alt="School Logo">
    </header>

    <div class="info1">
        <p>RECORD SHEET</p>
        <p>RETURNED STUDENTS' OUTPUTS</p>
    </div>

    <div class="info2">
        <div class="info2-left">
            <p>Course: {{ $event->subject ?? '______________' }}</p>
            <p>Instructor: {{ $facilitator->fname . ' ' . $facilitator->lname }}</p>
        </div>
        <div class="info2-right">
            <p>Sem: {{ $event->semester ?? '1st' }}</p>
            <p>S.Y.: {{ $event->school_year ?? '2023-2024' }}</p>
        </div>
        <p id="certify">We certify that we received the following documents below.</p>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th rowspan="2" style="width: 40%;">NAME OF STUDENTS</th>
                <th colspan="4">Quizzes</th>
                <th rowspan="2" style="width: 15%;">Midterm Results</th>
                <th rowspan="2" style="width: 15%;">Final Exam Results</th>
            </tr>
            <tr>
                <th>Q1</th>
                <th>Q2</th>
                <th>Q3</th>
                <th>Q4</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($attendee_records as $attendee_record)
                <tr>
                    <td style="text-align: left;">{{ $loop->iteration }}. {{ $attendee_record->master_list_member->full_name ?? 'N/A' }}</td>
                    <td>&#10003;</td>
                    <td>&#10003;</td>
                    <td>&#10003;</td>
                    <td>&#10003;</td>
                    <td>&#10003;</td>
                    <td>&#10003;</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div id="submitAndDate">
        <p id="submittedBy">Submitted By: _________________________</p>
        <p>Date of Submission: _________________________</p>
    </div>
</body>

</html>
