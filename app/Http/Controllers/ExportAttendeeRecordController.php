<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;

class ExportAttendeeRecordController extends Controller
{
    // Main function to export attendee records based on the template type
    public function exportAttendeeRecords(Event $event, $template, Request $request)
    {
        // Retrieve the selected date from the query parameters
        $selectedDate = $request->query('date'); //test

        // Export as PDF for class orientation
        if ($template === "general-template") {
            return $this->exportAttendanceGenTemplate($event, $selectedDate);
        }

        // Export as PDF for class orientation
        if ($template === "class-orientation") {
            return $this->exportClassOrientationToPDF($event, $selectedDate);
        }
        // Export as Excel for class attendance
        if ($template === "class-attendance-excel") {
            return $this->exportClassAttendanceToExcel($event, $selectedDate);
        }
        // Export as Excel for class attendance
        if ($template === "class-attendance-pdf") {
            return $this->exportClassAttendanceToPdf($event, $selectedDate);
        }
        if ($template === "midterm-exam") {
            return $this->exportMidtermExamTemplate($event, $selectedDate);
        }
        if ($template === "final-exam") {
            return $this->exportFinalExamTemplate($event, $selectedDate);
        }

        // Redirect back if the template is invalid
        return redirect()->back()->with('failed', "Invalid template");
    }

    public function exportAttendanceGenTemplate(Event $event, $selectedDate)
    {
        // Fetch attendee records with check-in and check-out not null
        $attendeeRecords = $event->attendee_records();

        // Filter by selected date if provided and not 'all'
        if ($selectedDate && $selectedDate !== 'all') {
            $attendeeRecords = $attendeeRecords->whereDate('check_in', $selectedDate);
        }

        // Load records with related master list member
        $attendeeRecords = $attendeeRecords
            ->with('master_list_member')
            ->orderBy('check_in', 'asc') // Sort by check_in in ascending order
            ->get();

        // Check if any records were found
        if ($attendeeRecords->isEmpty()) {
            return redirect()->back()->with('failed', "No Attendees found yet for the selected date");
        }

        // Load the PDF view with necessary data
        $pdf = Pdf::loadView('pdf_templates/general_event_template', [
            'event' => $event,
            'attendee_records' => $attendeeRecords,
            'facilitator' => $event->owner,
            'itemsPerPage' => 20, // Number of records per page
        ]);

        // Stream the generated PDF for download
        return $pdf->stream(filename: $event->name . "_attendance_checkin_checkout.pdf");
    }


    // Function to handle PDF export for class orientation
    public function exportClassOrientationToPDF(Event $event, $selectedDate)
    {
        // Fetch attendee records with check-in and check-out not null
        $attendeeRecords = $event->attendee_records()
            ->whereNotNull('single_signin');

        // Filter by selected date if provided and not 'all'
        if ($selectedDate && $selectedDate !== 'all') {
            $attendeeRecords = $attendeeRecords->whereDate('single_signin', $selectedDate);
        }

        // Load records with related master list member
        $attendeeRecords = $attendeeRecords->with('master_list_member')->get();



        // Check if any records were found
        if ($attendeeRecords->isEmpty()) {
            return redirect()->back()->with('failed', "No Attendees found yet for the selected date");
        }

        // Get unique attendee records to avoid redundancy in full names
        $uniqueAttendeeRecords = $attendeeRecords->unique(function ($record) {
            return $record->master_list_member->full_name;
        });

        // Load the PDF view with necessary data
        $pdf = Pdf::loadView('pdf_templates/class_orientation', [
            'event' => $event,
            'attendee_records' => $uniqueAttendeeRecords,
            'facilitator' => $event->owner,
            'itemsPerPage' => 25, // Number of records per page
        ]);

        // Stream the generated PDF for download
        return $pdf->stream(filename: $event->name . "_class_orientation_attendance_list.pdf");
    }

    public function exportClassAttendanceToPdf(Event $event, $selectedDate)
    {
        $hasAttendanceRecords = false;

        // Initialize Carbon instances for date handling
        $startDate = \Carbon\Carbon::parse($event->start_date);
        $currentDate = \Carbon\Carbon::now('Asia/Manila');

        // Determine the date range
        $monthsData = [];

        if ($selectedDate !== 'all') {
            $selectedMonthStart = \Carbon\Carbon::parse($selectedDate)->startOfMonth();
            $selectedMonthEnd = \Carbon\Carbon::parse($selectedDate)->endOfMonth();
            $dateRange = [$selectedMonthStart, $selectedMonthEnd];
        } else {
            $dateRange = [$startDate, $currentDate];
        }

        for ($date = $dateRange[0]; $date->lte($dateRange[1]); $date->addMonth()) {
            $currentMonth = $date->format('F Y');
            $monthlyData = [];
            $datesWithAttendance = [];
            $attendanceExistsForMonth = false;

            // Fetch attendance records for the event within the current month
            $attendanceRecords = $event->attendee_records()
                ->whereMonth('single_signin', $date->month)
                ->whereYear('single_signin', $date->year)
                ->with('master_list_member')
                ->get();

            foreach ($attendanceRecords as $record) {
                $member = $record->master_list_member;
                if ($member) {
                    $signinDate = \Carbon\Carbon::parse($record->single_signin)->format('Y-m-d');
                    $monthlyData[$member->full_name][$signinDate] = 1; // Mark present
                    $datesWithAttendance[$signinDate] = $signinDate; // Track the date
                    $attendanceExistsForMonth = true;
                }
            }

            // If attendance exists for the current month, store the data
            if ($attendanceExistsForMonth) {
                $monthsData[$currentMonth] = [
                    'dates' => $datesWithAttendance,
                    'data' => $monthlyData
                ];
                $hasAttendanceRecords = true;
            }
        }

        if (!$hasAttendanceRecords) {
            return redirect()->back()->with('failed', 'No attendance records found for the selected date range.');
        }


        if ($selectedDate !== 'all') {
            // Generate the PDF with the specific date layout in long bond paper size
            $pdf = PDF::loadView('pdf_templates.one_date_class_attendance', [
                'event' => $event,
                'monthsData' => $monthsData,
                'selectedDate' => $selectedDate,
                'facilitator' => $event->owner()->first(),
            ])->setPaper([0, 0, 612, 1008], 'portrait');  // Long bond paper in portrait mode
        } else {
            // Generate the PDF with the all dates layout
            $pdf = PDF::loadView('pdf_templates.all_dates_class_attendance', [
                'event' => $event,
                'monthsData' => $monthsData,
                'facilitator' => $event->owner()->first(),
            ])->setPaper([0, 0, 612, 1008], 'landscape');

        }


        return $pdf->download($event->name . '_class_attendance.pdf');
    }



    // Function to handle Excel export for class attendance
    public function exportClassAttendanceToExcel(Event $event, $selectedDate)
    {
        // Prepare the data for Excel export
        $data = [];
        $hasAttendanceRecords = false;

        // Fetch all members from the master list
        $members = $event->master_list->master_list_members()
            ->orderBy('full_name')
            ->get();

        // If 'all' is selected, prepare attendance for all dates
        if ($selectedDate === 'all') {
            // Initialize headers for the data
            $data[] = ['Name', 'Student ID Number'];

            // Create date range from event's start date to the current date
            $startDate = \Carbon\Carbon::parse($event->start_date);
            $currentDate = \Carbon\Carbon::now('Asia/Manila');
            $dateRange = [];

            // Loop through each date and check for attendance records
            for ($date = $startDate; $date->lte($currentDate); $date->addDay()) {
                // Attempt to find at least one attendance record for any member on this date
                $attendanceRecords = $event->attendee_records()
                    ->whereDate('single_signin', $date)
                    ->get();

                // If any attendance exists, add the date to headers
                if ($attendanceRecords->count() > 0) {
                    $dateRange[] = $date->format('Y-m-d');
                    $data[0][] = $date->format('Y-m-d'); // Add date to the header
                }
            }

            // If no valid dates were found, return with a failure message
            if (empty($dateRange)) {
                return redirect()->back()->with('failed', 'No attendance records found for any dates.');
            }

            // Populate attendance data for each member
            foreach ($members as $member) {
                $row = [
                    'Name' => $member->full_name,
                    'Student ID Number' => $member->unique_id,
                ];

                foreach ($dateRange as $date) {
                    // Check if there's an attendance record for this date and member
                    $attendanceRecord = $event->attendee_records()
                        ->where('master_list_member_id', $member->master_list_member_id)
                        ->whereDate('single_signin', $date)
                        ->first();

                    // Determine single_signin value (1 if present, 0 if absent)
                    $row[] = $attendanceRecord ? '1' : '0';
                }

                // Append the row to the data array
                $data[] = $row; // Always include the member, even if no records are found
            }
        } else {
            // Create headers for a specific selected date
            $data[] = [
                'Name',
                'Student ID Number',
                $selectedDate,
            ];

            // Flag to check if there are any attendance records for the specific date
            $attendanceFound = false;

            // Loop through each member to add their attendance records
            foreach ($members as $member) {
                $attendanceRecord = $event->attendee_records()
                    ->where('master_list_member_id', $member->master_list_member_id)
                    ->whereDate('single_signin', $selectedDate)
                    ->first();

                // Determine attendance values (1 or 0)
                $singleSigninValue = $attendanceRecord ? '1' : '0';

                // Add a new row with the member's name and attendance value
                $data[] = [
                    'Name' => $member->full_name,
                    'Student ID Number' => $member->unique_id,
                    $selectedDate => $singleSigninValue,
                ];

                // Set the flag if attendance record is found for any member
                if ($attendanceRecord) {
                    $attendanceFound = true;
                }
            }

            // Check if there are no attendance records for the specific date
            if (!$attendanceFound) {
                return redirect()->back()->with('failed', "No attendees found for the selected date.");
            }
        }

        // Check if there are any records to export
        if (empty($data) || count($data) < 2) { // Ensure at least the header and one record
            return redirect()->back()->with('failed', "No attendees found for the selected date.");
        }

        // Create and download the Excel file with the data
        return Excel::download(new class ($data) implements \Maatwebsite\Excel\Concerns\FromArray {
            protected $data;

            public function __construct($data)
            {
                $this->data = $data; // Store the data for Excel export
            }

            public function array(): array
            {
                return $this->data; // Return the data array
            }
        }, $event->name . "_class_attendance.xlsx");
    }



    public function exportMidtermExamTemplate(Event $event, $selectedDate)
    {
        $attendeeRecords = $event->attendee_records();

        // Filter by selected date if not 'all'
        if ($selectedDate && $selectedDate !== 'all') {
            $attendeeRecords = $attendeeRecords->whereDate('single_signin', $selectedDate);
        }

        // Load the related master_list_member and filter by single_signin
        $attendeeRecords = $attendeeRecords
            ->with('master_list_member')
            ->whereNotNull('single_signin')  // Ensure records with single_signin are selected
            ->orderBy('single_signin', 'asc')
            ->get();

        // Return failure message if no attendees found
        if ($attendeeRecords->isEmpty()) {
            return redirect()->back()->with('failed', "No exam attendees found for the selected date");
        }

        // Generate the PDF
        $pdf = Pdf::loadView('pdf_templates/midterm_exam_templates', [
            'event' => $event,
            'attendee_records' => $attendeeRecords,
            'facilitator' => $event->owner,
            'itemsPerPage' => 25,
        ]);

        return $pdf->stream($event->name . "_exam_attendees.pdf");
    }


    public function exportFinalExamTemplate(Event $event, $selectedDate)
    {
        $attendeeRecords = $event->attendee_records();

        // Filter by selected date if not 'all'
        if ($selectedDate && $selectedDate !== 'all') {
            $attendeeRecords = $attendeeRecords->whereDate('single_signin', $selectedDate);
        }

        // Load the related master_list_member and filter by single_signin
        $attendeeRecords = $attendeeRecords
            ->with('master_list_member')
            ->whereNotNull('single_signin')  // Ensure records with single_signin are selected
            ->orderBy('single_signin', 'asc')
            ->get();

        // Return failure message if no attendees found
        if ($attendeeRecords->isEmpty()) {
            return redirect()->back()->with('failed', "No exam attendees found for the selected date");
        }

        // Generate the PDF
        $pdf = Pdf::loadView('pdf_templates/final_exam_templates', [
            'event' => $event,
            'attendee_records' => $attendeeRecords,
            'facilitator' => $event->owner,
            'itemsPerPage' => 25,
        ]);

        return $pdf->stream($event->name . "_exam_attendees.pdf");
    }

}