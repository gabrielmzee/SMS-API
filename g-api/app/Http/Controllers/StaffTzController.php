<?php

namespace App\Http\Controllers;

use App\Models\StaffTZ;
use Illuminate\Http\Request;

class StaffTzController extends Controller
{
    public function index()
    {
        try {

            $staff = StaffTZ::all();

            return response()->json([
                'statusCode' => '0',
                'data' => $staff
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'statusCode' => '2',
                'message' => 'An error occurred while fetching data',
                'error' => $e->getMessage()
            ]);
        }
    }

     public function store(Request $request)
     {
         try {
             // Validate request
             $validated = $request->validate([
                 'nin' => 'required',
                 'first_name' => 'required',
                 'middle_name' => 'required',
                 'last_name' => 'required',
                 'sex' => 'nullable',
                 'date_of_birth' => 'nullable',
                 'birth_country' => 'nullable',
                 'birth_region' => 'nullable',
                 'birth_district' => 'nullable',
                 'birth_ward' => 'nullable',
                 'nationality' => 'nullable',
                 'photo' => 'required',
                 'signature' => 'required'
             ]);

             $validated = (object) $validated;
             // Create new record
             $staff = StaffTZ::create([
                 'nin' => $validated->nin,
                 'first_name' => $validated->first_name,
                 'middle_name' => $validated->middle_name,
                 'last_name' => $validated->last_name,
                 'sex' => $validated->sex,
                 'date_of_birth' => $validated->date_of_birth,
                 'birth_country' => $validated->birth_country,
                 'birth_region' => $validated->birth_region,
                 'birth_district' => $validated->birth_district,
                 'birth_ward' => $validated->birth_ward,
                 'nationality' => $validated->nationality,
                 'photo' => $validated->photo,
                 'signature' => $validated->signature,
                 'created_by' => auth()->user()->id,
             ]);

             return response()->json([
                 'statusCode' => '0',
                 'message' => 'Successfully created',
                 'data' => $staff
             ]);
         } catch (\Exception $e) {
             return response()->json([
                 'statusCode' => '2',
                 'message' => 'An error occurred while processing',
                 'error' => $e->getMessage()
             ]);
         }
     }

    public function show(StaffTZ $staff)
    {
        try {
            return response()->json([
                'statusCode' => '0',
                'data' => $staff
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'statusCode' => '2',
                'message' => 'An error occurred while fetching the data',
                'error' => $e->getMessage()
            ]);
        }
    }

    public function activate_deactivate(Request $request, StaffTZ $staff)
    {
        try {
            // Validate the request data
            $validator = Validator::make($request->all(), [
                'is_active' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    "statusCode" => '1',
                    'message' => $validator->errors()
                ]);
            }

            $validated = (object) $validated;

            // Update the record
            $staff->update([
                'is_active' => $validated->is_active,
                'updated_by' => auth()->user()->id,
            ]);

            return response()->json([
                'statusCode' => '0',
                'message' => 'Successfully updated'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'statusCode' => '2',
                'message' => 'An error occurred while processing',
                'error' => $e->getMessage()
            ]);
        }
    }

    public function destroy(StaffTZ $staff)
    {
        try {
            $staff->delete();

            return response()->json([
                'statusCode' => '0',
                'message' => 'Successfully deleted'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'statusCode' => '2',
                'message' => 'An error occurred while deleting the record',
                'error' => $e->getMessage()
            ]);
        }
    }
}
