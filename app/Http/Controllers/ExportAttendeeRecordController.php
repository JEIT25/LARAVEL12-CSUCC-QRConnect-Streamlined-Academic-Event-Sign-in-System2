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
        if ($template === "class-attendance") {
            return $this->exportClassAttendanceToExcel($event, $selectedDate);
        }
        if($template === "midterm-exam"){
            return  $this->exportMidtermExamTemplate($event, $selectedDate);
        }
        if ($template === "final-exam"){
            return  $this->exportFinalExamTemplate($event, $selectedDate);
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
            $attendeeRecords = $attendeeRecords->whereDate('single_signin', $selectedDate);
        }

        // Load records with related master list member
        $attendeeRecords = $attendeeRecords
            ->with('master_list_member')
            ->orderBy('single_signin', 'asc') // Sort by single_signin in ascending order
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
            ->whereNotNull('check_in')
            ->whereNotNull('check_out');

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

    // Function to handle Excel export for class attendance
    public function exportClassAttendanceToExcel(Event $event, $selectedDate)
    {
        // Fetch all members from the master list
        $members = $event->master_list->master_list_members()
            ->orderBy('full_name') // Alphabetical order
            ->get();

        // Prepare the data for Excel export
        $data = [];

        // Flag to check if any attendance records are found
        $hasAttendanceRecords = false;

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
                $attendanceExists = false;

                foreach ($members as $member) {
                    $attendanceRecord = $member->attendee_record()
                        ->where('event_id', $event->event_id) // Use $event->event_id for the correct relationship
                        ->whereDate('single_signin', $date)
                        ->first();

                    if ($attendanceRecord) {
                        $attendanceExists = true; // At least one attendance record exists for this date
                        break; // No need to check other members for this date
                    }
                }

                // If attendance exists, add the date to headers
                if ($attendanceExists) {
                    $dateRange[] = $date->format('Y-m-d');
                    $data[0][] = $date->format('Y-m-d'); // Add date to the header
                }
            }

            // Populate attendance data for each member if there are valid dates
            foreach ($members as $member) {
                $row = [
                    'Name' => $member->full_name,
                    'Student ID Number' => $member->unique_id,
                ];

                foreach ($dateRange as $date) {
                    // Attempt to find the attendance record for this date and member
                    $attendanceRecord = $member->attendee_record()
                        ->where('event_id', $event->event_id) // Use $event->event_id for the correct relationship
                        ->whereDate('single_signin', $date)
                        ->first();

                    // Determine single_signin value (1 if present, 0 if absent)
                    $row[] = $attendanceRecord && $attendanceRecord->single_signin ? '1' : '0';
                }

                // Append the row to the data array only if there's at least one attendance record for this member
                if (count($row) > 2) { // The row must have more than just name and ID
                    $data[] = $row;
                    $hasAttendanceRecords = true; // Mark that we found at least one attendance record
                }
            }
        } else {
            // Create headers for a specific selected date
            $data[] = [
                'Name',
                'Student ID Number',
                $selectedDate,
            ];

            // Loop through each member to add their attendance records
            foreach ($members as $member) {
                $attendeeRecord = $member->attendee_record()
                    ->where('event_id', $event->event_id) // Use $event->event_id for the correct relationship
                    ->whereDate('single_signin', $selectedDate)
                    ->first();

                // Determine attendance values (1 or 0)
                $singleSigninValue = $attendeeRecord && $attendeeRecord->single_signin ? '1' : '0';

                // Add a new row with the member's name and attendance value
                if ($singleSigninValue === '1') {
                    $hasAttendanceRecords = true; // At least one attendance record exists
                }

                $data[] = [
                    'Name' => $member->full_name,
                    'Student ID Number' => $member->unique_id,
                    $selectedDate=> $singleSigninValue,
                ];
            }

            // Check if there are no attendance records for the specific date
            if (!$hasAttendanceRecords) {
                return redirect()->back()->with('failed', "No Attendees found for the selected date");
            }
        }

        // Check if there are any records to export
        if (!$hasAttendanceRecords || count($data) < 2) { // Ensure at least the header and one record
            return redirect()->back()->with('failed', "No Attendees found for the selected date");
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
            'itemsPerPage' => 20,
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
            'itemsPerPage' => 20,
        ]);

        return $pdf->stream($event->name . "_exam_attendees.pdf");
    }

}