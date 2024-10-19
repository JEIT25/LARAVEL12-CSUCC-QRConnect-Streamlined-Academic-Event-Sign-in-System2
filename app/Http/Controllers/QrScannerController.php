<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Attendee;
use App\Models\AttendeeRecord;
use App\Models\MasterListMember;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
class QrScannerController extends Controller
{

    private function checkInOrOut($event, $member, $type = "check_in", $action = "check in")
    {
        try {
            $today = now()->toDateString(); // Get today's date (YYYY-MM-DD format)

            // Direct query on the AttendeeRecord model, not on the collection
            $existingAttendeeRecord = $event->attendee_records
                ->where('master_list_member_id', $member->master_list_member_id)
                ->first(); // Get the first result

            if ($existingAttendeeRecord) { // Check if attendee has a record for today
                if ($existingAttendeeRecord->$type) {
                    return response()->json([
                        'message' => "Member has already $action for this event",
                        'status' => false,
                        'attendee_record' => $member,
                        $type => $existingAttendeeRecord->$type,
                    ]);
                } else { // Update the record if no check-in/check-out yet for today
                    $existingAttendeeRecord->update([$type => now()]);

                    return response()->json([
                        'message' => "$action successful",
                        'attendee_record' => $member,
                        'status' => true,
                        $type => $existingAttendeeRecord->$type,
                    ]);
                }
            }

            // Create a new attendee entry for the event with the current date/time
            $newAttendeeRecord = $event->attendee_records()->create([
                "master_list_member_id" => $member->master_list_member_id,
                $type => now(),
            ]);

            return response()->json([
                'attendee_record' => MasterListMember::find($newAttendeeRecord->master_list_member_id),
                'message' => "$action successful",
                'status' => true,
                $type => $newAttendeeRecord->$type,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred: ' . $e->getMessage(),
                'status' => false,
            ]);
        }
    }


    public function singleSigninPost(Event $event, Request $request)
    {
        try {
            $today = now()->toDateString(); // Get today's date (YYYY-MM-DD format)

            // Validate received QR data
            $validated = $request->validate([
                "qrData" => 'string|required',
            ]);

            // Query to find member using unique ID
            $member = $event->master_list->master_list_members->where('unique_id', '=', $validated['qrData'])->first();

            $existingAttendeeRecord = $event->attendee_records()->where('event_id', $event->event_id)
                ->where('master_list_member_id', $member->master_list_member_id)
                ->whereRaw('DATE(created_at) = ?', [$today]) // Filter by the date of created_at
                ->orderBy('created_at', 'desc') // Order by highest to lowest datetime
                ->first(); // Get the first result

            if ($existingAttendeeRecord) { //check attendee if did not Sign In for today
                // If already checked in, return a message saying so
                if ($existingAttendeeRecord->single_signin) {
                    return response()->json([
                        'message' => "Member has already Signed-in for this event",
                        'status' => false,
                        'attendee_record' => $member,
                        "single_signin" => $existingAttendeeRecord->single_signin,
                    ]);
                } else { // If existing attendee and did not check in yet for today
                    $existingAttendeeRecord->update(["single_signin" => now()]);

                    return response()->json([
                        'message' => "Sign-in successful",
                        'attendee_record' => $member,
                        'status' => true,
                        "single_signin" => $existingAttendeeRecord->single_signin,
                    ]);
                }
            }

            // Create a new attendee entry for the event with current date/time as sign-in
            $newAttendeeRecord = $event->attendee_records()->create([
                "master_list_member_id" => $member->master_list_member_id,
                "single_signin" => now(),
            ]);

            return response()->json([
                'attendee_record' => MasterListMember::find($newAttendeeRecord->master_list_member_id),
                'message' => 'Sign-in successful',
                'status' => true,
                "single_signin" => $newAttendeeRecord->single_signin,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Member not found',
                'status' => false,
            ]);
        }
    }

    public function singleSignin(Event $event, Request $request)
    {
        if (
            $request->user()->cannot('create', [AttendeeRecord::class, $event]) ||
            //This will match the words "exam," "class orientation," and "class attendance" regardless of whether they appear alone or as part of a longer string.
            !preg_match('/\b(exam|class orientation|class attendance)\b/i', $event->type)
        ) {
            abort(403);
        }

        // Get the current date (formatted to 'Y-m-d' to ignore time)
        $currentDate = Carbon::now('Asia/Manila')->toDateString();
        $startDate = Carbon::parse($event->start_date)->toDateString();
        $endDate = Carbon::parse($event->end_date)->toDateString();

        // Check if the current date is outside the event's date range
        if ($currentDate < $startDate || $currentDate > $endDate) {
            return redirect()->back()->with('failed', 'This event is not available for sign-in at this time.');
        }

        return inertia('QrScanner/SingleSignin', [
            "event" => $event
        ]);
    }


    public function checkin(Event $event, Request $request)
    {
        if (
            $request->user()->cannot('create', [AttendeeRecord::class, $event]) ||
            //This will match the words "exam," "class orientation," and "class attendance" regardless of whether they appear alone or as part of a longer string.
            preg_match('/\b(exam|class orientation|class attendance)\b/i', $event->type)
        ) {
            abort(403);
        }


        // Get the current date (formatted to 'Y-m-d' to ignore time)
        $currentDate = Carbon::now('Asia/Manila')->toDateString();
        $startDate = Carbon::parse($event->start_date)->toDateString();
        $endDate = Carbon::parse($event->end_date)->toDateString();

        // Check if the current date is outside the event's date range
        if ($currentDate < $startDate || $currentDate > $endDate) {
            return redirect()->back()->with('failed', 'This event is not available for sign-in at this time.');
        }

        return inertia('QrScanner/Checkin', [
            "event" => $event,
        ]);
    }

    public function checkinPost(Event $event, Request $request)
    {
        if ($request->user()->cannot('create', [AttendeeRecord::class, $event])) {
            abort(403);
        }

        // Validate received QR data
        $validated = $request->validate([
            "qrData" => 'string|required',
        ]);

        // Query to find member using unique ID
        $member = $event->master_list->master_list_members()->where("unique_id", $validated['qrData'])->first();

        // If member is not found, return an error
        if (!$member || $member->master_list_id !== $event->master_list->master_list_id) {
            return response()->json(['message' => 'Person not found in Master List!', "status" => false]);
        }

        return $this->checkInOrOut($event, $member, "check_in", "check in");
    }

    public function checkout(Event $event, Request $request)
    {
        if (
            $request->user()->cannot('create', [AttendeeRecord::class, $event]) ||
            //This will match the words "exam," "class orientation," and "class attendance" regardless of whether they appear alone or as part of a longer string.
            preg_match('/\b(exam|class orientation|class attendance)\b/i', $event->type)
        ) {
            abort(403);
        }
        if ($request->user()->cannot('create', [AttendeeRecord::class, $event])) {
            abort(403);
        }


        // Get the current date (formatted to 'Y-m-d' to ignore time)
        $currentDate = Carbon::now('Asia/Manila')->toDateString();
        $startDate = Carbon::parse($event->start_date)->toDateString();
        $endDate = Carbon::parse($event->end_date)->toDateString();

        // Check if the current date is outside the event's date range
        if ($currentDate < $startDate || $currentDate > $endDate) {
            return redirect()->back()->with('failed', 'This event is not available for sign-in at this time.');
        }

        return inertia('QrScanner/Checkout', [
            "event" => $event,
        ]);
    }

    public function checkoutPost(Event $event, Request $request)
    {
        if ($request->user()->cannot('create', [AttendeeRecord::class, $event])) {
            abort(403);
        }

        // Validate received QR data
        $validated = $request->validate([
            "qrData" => 'string|required',
        ]);

        // Query to find member using unique ID
        $member = $event->master_list->master_list_members()->where("unique_id", $validated['qrData'])->first();

        // If member is not found, return an error
        if (!$member || $member->master_list_id !== $event->master_list->master_list_id) {
            return response()->json(['message' => "Person not found in Master List!", "status" => false]);
        }


        return $this->checkInOrOut($event, $member, "check_out", "check out");
    }

}

