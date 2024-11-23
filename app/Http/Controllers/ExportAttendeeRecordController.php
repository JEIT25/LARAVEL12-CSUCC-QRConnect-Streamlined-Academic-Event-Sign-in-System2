<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;


class ExportAttendeeRecordController extends Controller
{
    // Main function to export attendee records based on the template type
    public function exportAttendeeRecords(Event $event, $template, Request $request)
    {

        // Retrieve the selected date from the query parameters
        $selectedDate = $request['date'];

        // Retrieve the selected date from the query parameters
        $invigilator = $request['invigilator'];

        $start_time = date("g:i A", strtotime($request->start_time)); // Converts to 12-hour format with AM/PM
        $end_time = date("g:i A", strtotime($request->end_time));     // Converts to 12-hour format with AM/PM


        // Load records with related master list member
        $attendeeRecords = $event->attendee_records()->get();

        // Check if any records were found
        if ($attendeeRecords->isEmpty()) {
            return redirect()->back()->with('failed', "No Attendees found yet for the selected date");
        }

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
            return $this->exportMidtermExamTemplate($event, $selectedDate, $invigilator, $start_time, $end_time);
        }
        if ($template === "final-exam") {
            return $this->exportFinalExamTemplate($event, $selectedDate, $invigilator, $start_time, $end_time);
        }

        if ($template === "return-output") {
            return $this->exportReturnOuputToPDF($event, $selectedDate);
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
            ->join('master_list_members', 'attendee_records.master_list_member_id', '=', 'master_list_members.master_list_member_id')
            ->orderBy('master_list_members.full_name', 'asc') // Sort by full_name in ascending order
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
    public function exportReturnOuputToPDF(Event $event, $selectedDate)
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
        $pdf = Pdf::loadView('pdf_templates/return_output', [
            'event' => $event,
            'attendee_records' => $uniqueAttendeeRecords,
            'facilitator' => $event->owner,
            'itemsPerPage' => 25, // Number of records per page
        ]);

        // Stream the generated PDF for download
        return $pdf->stream(filename: $event->name . "return_output.pdf");
    }
    public function exportReturnOuputsToPDF(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'events' => 'nullable|array',
                'quiz' => 'nullable|array|min:1|max:5',
                'lab' => 'nullable|array|min:1|max:5',
                'exam' => 'nullable|array|min:1|max:2',
                'attendanceDate' => 'required|date',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->with('failed', $e->validator->errors()->first());
        }

        $attendanceDate = $request->get('attendanceDate');
        $quizzes = $this->fetchRecords($request->get('quiz'), $attendanceDate);
        $laboratories = $this->fetchRecords($request->get('lab'), $attendanceDate);
        $exams = $this->fetchRecords($request->get('exam'), $attendanceDate);

        // Combine all unique attendees
        $allAttendees = collect($quizzes)
            ->concat($laboratories)
            ->concat($exams)
            ->flatMap(fn($record) => $record['attendee_records'])
            ->pluck('full_name')
            ->unique()
            ->sort()
            ->values();

        // Prepare data for rendering
        $combinedRecords = $allAttendees->map(function ($attendeeName) use ($quizzes, $laboratories, $exams) {
            return [
                'name' => $attendeeName,
                'quizzes' => $this->getEventStatus($attendeeName, $quizzes),
                'laboratories' => $this->getEventStatus($attendeeName, $laboratories),
                'exams' => $this->getEventStatus($attendeeName, $exams),
            ];
        });

        if ($combinedRecords->isEmpty()) {
            return redirect()->back()->with('failed', "No data available for the given attendance date.");
        }

        $pdf = Pdf::loadView('pdf_templates.return_outputs', [
            'records' => $combinedRecords,
            'facilitator' => $request->user(),
            'attendanceDate' => $attendanceDate,
            'numQuizzes' => count($quizzes),
            'numLaboratories' => count($laboratories),
            'numExams' => count($exams),
            'itemsPerPage' => 25, // Number of records per page
        ]);


        $filename = uniqid('return_outputs_') . '.pdf';
        $folderPath = storage_path('app/public/pdfs');

        if (!is_dir($folderPath)) {
            mkdir($folderPath, 0755, true);
        }

        Storage::put("public/pdfs/{$filename}", $pdf->output());

        return redirect()->route('events.index', ['filename' => $filename]);
    }

    /**
     * Get event status for a specific attendee across all events.
     */
    protected function getEventStatus(string $attendeeName, array $events): array
    {
        return collect($events)->map(function ($event) use ($attendeeName) {
            $attendee = collect($event['attendee_records'])->firstWhere('full_name', $attendeeName);

            return $attendee && $attendee->single_signin ? '✔' : '✘';
        })->toArray();
    }


    /**
     * Fetch records based on event data and attendance date.
     */
    protected function fetchRecords(?array $eventArray, string $attendanceDate): array
    {
        if (!$eventArray) {
            return [];
        }

        $records = [];
        foreach ($eventArray as $event) {
            $membersWithAttendance = DB::table('master_list_members')
                ->join('attendee_records', 'master_list_members.master_list_member_id', '=', 'attendee_records.master_list_member_id')
                ->where('attendee_records.event_id', $event['event_id'])
                ->whereDate('attendee_records.single_signin', $attendanceDate)
                ->orWhereNull('attendee_records.single_signin') // Include records without sign-in
                ->select('master_list_members.full_name', 'attendee_records.single_signin')
                ->get();

            $records[] = [
                'event' => $event,
                'attendee_records' => $membersWithAttendance,
            ];
        }

        return $records;
    }




    public function downloadPDF(Request $request)
    {
        $filename = $request->query('name');

        // Check if the file exists in the storage directory
        $path = 'public/pdfs/' . $filename;

        if (!Storage::exists($path)) {
            return redirect()->route('events.index')->with('failed', "The requested PDF file does not exist.");
        }

        // Serve the file for download
        return response()->download(storage_path('app/' . $path))->deleteFileAfterSend(true);
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

        return $pdf->stream($event->name . '_class_attendance.pdf');
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



    public function exportMidtermExamTemplate(Event $event, $selectedDate, $invigilator, $start_time, $end_time)
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
            'invigilator' => $invigilator,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'itemsPerPage' => 26,
        ]);

        return $pdf->stream($event->name . "_exam_attendees.pdf");
    }


    public function exportFinalExamTemplate(Event $event, $selectedDate, $invigilator, $start_time, $end_time)
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
            'invigilator' => $invigilator,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'itemsPerPage' => 26,
        ]);

        return $pdf->stream($event->name . "_exam_attendees.pdf");
    }

}