<?php

namespace App\Http\Controllers;

use App\Models\MasterList;
use App\Models\MasterListMember;
use Illuminate\Http\Request;

class MasterListMemberController extends Controller
{
    public function store(Request $request, MasterList $master_list)
    {
        // Base validation rules
        $rules = [
            'type' => 'required|string|in:individual,bulk', // Ensure type is either individual or bulk
        ];

        // Get existing unique IDs only for this master list
        $existingUniqueIds = $master_list->master_list_members()->pluck('unique_id')->toArray();

        // Additional rules based on type
        if ($request->input('type') === 'individual') {
            $rules['full_name'] = 'required|string|max:255';
            $rules['unique_id'] = [
                'required',
                'string',
                'max:255',
                function ($attribute, $value, $fail) use ($existingUniqueIds) {
                    if (in_array($value, $existingUniqueIds)) {
                        $fail('The unique_id has already been taken for this master list.');
                        return back()->with('failed', 'This member already exist in this master list!');
                    }
                }
            ];
        } elseif ($request->input('type') === 'bulk') {
            $rules['members'] = 'required|array';
            $rules['members.*.full_name'] = 'required|string|max:255';
            $rules['members.*.unique_id'] = 'required|string|max:255';
        }

        // Validate the request data
        $validatedData = $request->validate($rules);

        // Check if the request is for individual or bulk addition
        if ($validatedData['type'] === 'individual') {
            // Add individual member to the master list
            $master_list->master_list_members()->create([
                "full_name" => $validatedData['full_name'], // Include full_name
                "unique_id" => $validatedData['unique_id'], // Include unique_id
            ]);

            return back()->with('success', 'New member added successfully to the master list.');
        } elseif ($validatedData['type'] === 'bulk') {
            $members = $validatedData['members'];

            // Loop through each member in the bulk array
            foreach ($members as $member) {
                // Check if the unique ID is already added to the master list
                if (in_array($member['unique_id'], $existingUniqueIds)) {
                    continue; // Skip adding this member if they already exist
                }

                // Add the bulk member to the master list
                $master_list->master_list_members()->create([
                    'full_name' => $member['full_name'], // Include full_name
                    'unique_id' => $member['unique_id'], // Include unique_id
                ]);
            }

            return back()->with('success', 'Members added successfully. Some were skipped if they already exist');
        }

        return back()->with('failed', 'Invalid request.');
    }


    public function destroy(MasterListMember $master_list_member)
    {
        $master_list_member->delete();

        // Redirect back with a success message
        return redirect()->route('master-lists.show', [
            'event' => $master_list_member->master_list->event->event_id,
            'master_list' => $master_list_member->master_list->master_list_id,
        ])->with('success', 'Member successfully deleted from the master list.');
    }

}