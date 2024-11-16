<?php

namespace App\Http\Controllers;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Validator;

class EventController extends BaseController
{
    use AuthorizesRequests;
    /**
     * Display a listing of the resource.-
     */

    public function __construct()
    { //third way in Authorization (easiest)
        $this->authorizeResource(Event::class, 'event');
    }
    public function index(Request $request)
    {
        return inertia(
            'Event/Index',
            [
                'events' => Event::where('facilitator_id', $request->user()->user_id)->get()
            ]
        );
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return inertia(
            'Event/Create',
        );
    }

    /**
     * Store a newly created resource in storage.
     */

    public function store(Request $request)
    {
        $user = $request->user(); // Get current user from the request
        $currentDate = now()->toDateString(); // Get the current date

        // Define validation rules and custom messages
        $rules = [
            'name' => 'required',
            'description' => 'required',
            'location' => 'required',
            'start_date' => 'required|date|after_or_equal:' . $currentDate,
            'end_date' => 'required|date|after_or_equal:start_date',
            'profile_image' => 'nullable|mimes:jpg,png,jpeg,webp|max:5000',
            'type' => 'required',
            'subject' => 'nullable|string|required_if:type,lecture,class orientation,quiz,laboratory,return output,exam,class orientation',
            'subject_code' => 'nullable|string|required_if:type,lecture,class orientation,quiz,laboratory,return output,exam,class orientation',
            'year_level' => 'nullable|required_if:type,lecture,class orientation,quiz,laboratory,return output,exam,class orientation|in:1,2,3,4', // Validate year as enum with values 1 to 5
            'program' => 'nullable|required_if:type,lecture,class orientation,quiz,laboratory,return output,exam,class orientation|string|max:6', // Validate program as a text input with a max length of 255 characters
            'semester' => 'required|in:1st,2nd',
            'school_year' => 'required|string',
        ];

        $messages = [
            'start_date.after_or_equal' => 'The start date cannot be earlier than today.',
            'end_date.after_or_equal' => 'The end date cannot be earlier than the start date.',
            'profile_image.mimes' => 'The file should be in one of the formats: jpg, png, jpeg, webp',
            'subject.required_if' => 'The subject field is required for events like lecture, class orientation, quiz, laboratory, return output, or exam.',
            'subject_code.required_if' => 'The subject code is required for events like lecture, class orientation, quiz, laboratory, return output, or exam.',
        ];

        // Create the validator instance
        $validator = Validator::make($request->all(), $rules, $messages);

        // Check if validation fails
        if ($validator->fails()) {
            // Return back with errors, old input, and additional data
            return redirect()->back()
                ->withErrors($validator) // Add validation errors
                ->withInput() // Retain old input
                ->with('failed', 'Some input fields were invalid, try again!'); // Add any custom data
        }

        // Get the validated data
        $validatedData = $validator->validated();

        // Validate and split the school year, if provided
        if ($request->school_year) {
            // Check if the school year is in the correct format
            if (strpos($validatedData['school_year'], '-') !== false) {
                // Split the school year into startYear and endYear
                [$startYear, $endYear] = explode('-', $validatedData['school_year']);

                // Validate the start year
                if ((int) $startYear < date('Y')) {
                    return redirect()->back()->withErrors([
                        'school_year' => 'The start year of the school year must be the current year or later.'
                    ])->withInput();
                }

                // Validate the end year
                if ((int) $endYear <= (int) $startYear) {
                    return redirect()->back()->withErrors([
                        'school_year' => 'The end year of the school year must be greater than the start year.'
                    ])->withInput();
                }
            } else {
                // Handle invalid format of school year
                return redirect()->back()->withErrors([
                    'school_year' => 'Invalid school year format. Expected format: YYYY-YYYY.'
                ])->withInput();
            }
        }

        // Handle the profile image if it's present
        if ($request->hasFile('profile_image')) {
            $profileImage = $request->file('profile_image');
            $imagePath = $profileImage->store('images/events', 'public');  // Store the image in the 'images' directory in the 'public' disk
            $validatedData['profile_image'] = $imagePath;
        }

        // Create a new Event with the validated data
        $newEvent = $user->events()->create($validatedData);

        // Redirect to the event show page with success message
        return redirect()->route('events.show', ['event' => $newEvent->event_id])
            ->with('success', 'Event created successfully!');
    }



    /**
     * Display the specified resource.
     */
    public function show(Event $event)
    {
        if ($event->profile_image) {
            $event->profile_image = asset("storage/$event->profile_image"); //using the asset() set profile image path to public path
        }

        $master_list = $event->master_list()->first();


        return inertia(
            'Event/Show',
            [
                'default_image' => asset('assets/images/backgrounds/billboard.png'),
                'event' => $event,
                'master_list' => $master_list
            ]
        );
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Event $event)
    {
        return inertia(
            'Event/Edit',
            [
                "event" => $event
            ]
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Event $event)
    {
        // Get the current date
        $currentDate = now()->toDateString();

        // Get the current year
        $currentYear = date('Y');

        // Validate incoming request data
        $validatedData = $request->validate([
            'name' => 'required',
            'description' => 'required',
            'location' => 'required',
            'start_date' => 'required|date',// Start date must be today or later
            'end_date' => 'required|date|after_or_equal:start_date', // End date must be after or equal to start date
            'subject' => 'nullable|string|required_if:type,lecture,class orientation,quiz,laboratory,return output,exam,class orientation',
            'subject_code' => 'nullable|string|required_if:type,lecture,class orientation,quiz,laboratory,return output,exam,class orientation',
            'type' => 'required', // Validate type
            'semester' => 'required|in:1st,2nd',
            'school_year' => 'nullable|string|regex:/^\d{4}-\d{4}$/', // Validate format YYYY-YYYY
            'year_level' => 'nullable|required_if:type,lecture,class orientation,quiz,laboratory,return output,exam,class orientation|in:1,2,3,4', // Validate year as enum with values 1 to 5
            'program' => 'nullable|required_if:type,lecture,class orientation,quiz,laboratory,return output,exam,class orientation|string|max:6', // Validate program as a text input with a max length of 255 charactersdit
        ], [
            'end_date.after_or_equal' => 'The end date cannot be earlier than the start date.',
            'school_year.regex' => 'The school year must be in the format YYYY-YYYY.',
            'year_level.in' => 'The year must be between 1 and 4.',
        ]);

        if ($request->school_year) {
            // Split the school year into start and end years
            [$startYear, $endYear] = explode('-', $validatedData['school_year']);

            // Validate that the start year is greater than or equal to the current year
            if ((int) $startYear < $currentYear) {
                return redirect()->back()->withErrors(['school_year' => 'The start year of the school year must be the current year or later.']);
            }

            // Validate that the end year is greater than or equal to the start year
            if ((int) $endYear <= (int) $startYear) {
                return redirect()->back()->withErrors(['school_year' => 'The end year of the school year must be greater than the start year.']);
            }
        }

        // Automatically set 'type' to 'other_type' if 'other_type' is provided
        if (!empty($validatedData['other_type'])) {
            $validatedData['type'] = $validatedData['other_type'];
        }

        // Handle the profile image if it's present
        if ($request->hasFile('profile_image')) {
            // Validate the profile image
            $validatedData = array_merge($validatedData, $request->validate([
                'profile_image' => 'nullable|mimes:jpg,png,jpeg,webp|max:5000', // Validate image type and size
            ], [
                'profile_image.mimes' => 'The file should be in one of the formats: jpg, png, jpeg, webp'
            ]));

            // Delete the old profile image if it exists
            if ($event->profile_image) {
                Storage::disk('public')->delete($event->profile_image);
            }

            // Store the new profile image
            $profileImage = $request->file('profile_image');
            $imagePath = $profileImage->store('images/events', 'public'); // Store the image in the 'images' directory in the 'public' disk

            // Add the new image path to the validated data
            $validatedData['profile_image'] = $imagePath;
        }

        // Update the existing Event with the validated data
        $event->update($validatedData);

        return redirect()->route('events.show', ['event' => $event->event_id])
            ->with('success', 'Event updated successfully!');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Event $event)
    {
        if ($event->profile_image) {
            Storage::disk('public')->delete($event->profile_image);
        }
        $event->delete();
        return redirect()->route('events.index')
            ->with('success', 'SUCCESSFULLY DELETED!');
    }
}
