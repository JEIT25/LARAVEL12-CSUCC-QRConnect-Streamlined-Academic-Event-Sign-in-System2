<?php

namespace App\Http\Controllers;

use App\Models\AttendeeRecord;
use App\Models\AttendeeRecordRecord;
use App\Models\Event;
use Illuminate\Http\Request;
class AttendeeRecordController extends Controller
{

    /**
     * Display a listing of the resource.
     */
    public function index(Event $event,Request $request)
    {
        // Fetch the attendees of the event
        $attendee_records = $event->attendee_records()->get();

        if ($request->user()->cannot('viewAny',[AttendeeRecord::class,$event])) { //use policy to check if user can viewa attendance records,
        //also passed the event so that only owner of event can see all attendance records of this event
            abort(403);
        }

        return inertia('AttendeeRecord/Index', [
            'event' => $event,
            'attendee_records' => $attendee_records->load('master_list_member')
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Event $event , AttendeeRecord $attendee)
    {
        $attendee->delete();

        return redirect()->route('attendees.index',["event" => $event->event_id])
        ->with("success","AttendeeRecord deleted successfully.");
    }
}
