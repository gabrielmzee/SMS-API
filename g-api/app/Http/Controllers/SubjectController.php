<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SubjectController extends Controller
{
    public function index()
    {
        try {
            $subject = Subject::orderBy('name', 'asc')->get();

            return response()->json([
                'statusCode' => '0',
                'data' => $subject
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
                 'name' => 'required',
                 'code' => 'nullable|string',
             ]);



             $checkExist = Subject::where('name', $validated['name'])->where('code', $validated['code'])->exists();
                if ($checkExist) {
                    return response()->json([
                        'statusCode' => '1',
                        'message' => 'Already exists',
                        'data' => $checkExist
                    ]);
                }

             // Create new record
             $subject = Subject::create([
                 'name' => $validated['name'],
                 'code' => $validated['code'],
                 'created_by' => auth()->user()->id,
             ]);

             return response()->json([
                 'statusCode' => '0',
                 'message' => 'Successfully created',
                 'data' => $subject
             ]);
         } catch (\Exception $e) {
             return response()->json([
                 'statusCode' => '2',
                 'message' => 'An error occurred while processing',
                 'error' => $e->getMessage()
             ]);
         }
     }

    public function show(Subject $subject)
    {
        try {
            return response()->json([
                'statusCode' => '0',
                'data' => $subject
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'statusCode' => '2',
                'message' => 'An error occurred while fetching the data',
                'error' => $e->getMessage()
            ]);
        }
    }

    public function update(Request $request, Subject $subject)
    {
        try {
            // Validate the request data
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'code' => 'nullable|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    "statusCode" => '1',
                    'message' => $validator->errors()
                ]);
            }

            // Get the validated data
            $validated = $validator->validated();

            // Update the record
            $subject->update([
                'name' => $validated['name'],
                'code' => $validated['code'],
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

    public function destroy(Subject $subject)
    {
        try {
            $subject->delete();

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
