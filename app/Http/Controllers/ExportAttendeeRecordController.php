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
        $selectedDate = $request->query('date');

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

        // Redirect back if the template is invalid
        return redirect()->back()->with('failed', "Invalid template");
    }

    public function exportAttendanceGenTemplate(Event $event, $selectedDate)
    {
        // Fetch attendee records with check-in and check-out not null
        $attendeeRecords = $event->attendee_records();

        // Filter by selected date if provided and not 'all'
        if ($selectedDate && $selectedDate !== 'all') {
            $attendeeRecords = $attendeeRecords->whereDate('created_at', $selectedDate);
        }

        // Load records with related master list member
        $attendeeRecords = $attendeeRecords
            ->with('master_list_member')
            ->orderBy('created_at', 'asc') // Sort by created_at in ascending order
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
            $attendeeRecords = $attendeeRecords->whereDate('created_at', $selectedDate);
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
        ->orderBy('full_name') //alphabetical
        ->get();

        // Prepare the data for Excel export
        $data = [];

        // If 'all' is selected, prepare attendance for all dates
        if ($selectedDate === 'all') {
            // Initialize headers for the data
            $data[] = ['Name', 'Student ID Number'];

            // Create date range from event's start date to the current date
            $startDate = \Carbon\Carbon::parse($event->start_date);
            $currentDate = \Carbon\Carbon::now();
            $dateRange = [];

            // Loop through each date and add headers for check-in/check-out
            for ($date = $startDate; $date->lte($currentDate); $date->addDay()) {
                $dateRange[] = $date->format('Y-m-d');
                $data[0][] = "{$date->format('Y-m-d')} (Check-In)";
                $data[0][] = "{$date->format('Y-m-d')} (Check-Out)";
            }

            // Populate attendance data for each member
            foreach ($members as $member) {
                $row = [
                    'Name' => $member->full_name,
                    'Student ID Number' => $member->unique_id,
                ];

                // Check attendance for each date
                foreach ($dateRange as $date) {
                    // Attempt to find the attendance record for this date and member
                    $attendanceRecord = $member->attendee_records()
                        ->where('event_id', $event->event_id) // Use $event->event_id for the correct relationship
                        ->whereDate('created_at', $date)
                        ->first();

                    // Ensure the check-in and check-out cells are always filled, defaulting to 0
                    $row[] = $attendanceRecord && $attendanceRecord->check_in ? '1' : '0'; // Check-In
                    $row[] = $attendanceRecord && $attendanceRecord->check_out ? '1' : '0'; // Check-Out
                }

                // Append the row to the data array
                $data[] = $row;
            }
        } else {
            // Create headers for a specific selected date
            $data[] = [
                'Name',
                'Student ID Number',
                'Check-In (' . $selectedDate . ')',
                'Check-Out (' . $selectedDate . ')'
            ];

            // Loop through each member to add their attendance records
            foreach ($members as $member) {
                $attendeeRecord = $member->attendee_records()
                    ->where('event_id', $event->event_id) // Use $event->event_id for the correct relationship
                    ->whereDate('created_at', $selectedDate)
                    ->first();

                // Determine attendance values (1 or 0)
                $checkInValue = $attendeeRecord && $attendeeRecord->check_in ? '1' : '0';
                $checkOutValue = $attendeeRecord && $attendeeRecord->check_out ? '1' : '0';

                // Add a new row with the member's name and attendance values
                $data[] = [
                    'Name' => $member->full_name,
                    'Student ID Number' => $member->unique_id,
                    'Check-In (' . $selectedDate . ')' => $checkInValue,
                    'Check-Out (' . $selectedDate . ')' => $checkOutValue,
                ];
            }
        }

        // Check if there are any records to export
        if (count($data) <= 1) {
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
}
