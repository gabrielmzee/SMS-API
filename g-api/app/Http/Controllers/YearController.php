<?php

namespace App\Http\Controllers;

use App\Models\Year;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class YearController extends Controller
{
    public function index()
    {
        try {
            $years = Year::all();

            return response()->json([
                'statusCode' => '0',
                'data' => $years
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
                 'year' => 'required|string',
                 'from' => 'required|date',
                 'to' => 'required|date',
             ]);

             // Create new record
             $year = Year::create([
                 'year' => $validated['year'],
                 'from_date' => $validated['from'],
                 'to_date' => $validated['to'],
                 'created_by' => auth()->user()->id,
             ]);

             return response()->json([
                 'statusCode' => '0',
                 'message' => 'Successfully created',
                 'data' => $year
             ]);
         } catch (\Exception $e) {
             return response()->json([
                 'statusCode' => '2',
                 'message' => 'An error occurred while processing',
                 'error' => $e->getMessage()
             ]);
         }
     }

    public function show(Year $year)
    {
        try {
            return response()->json([
                'statusCode' => '0',
                'data' => $year
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'statusCode' => '2',
                'message' => 'An error occurred while fetching the data',
                'error' => $e->getMessage()
            ]);
        }
    }

    public function update(Request $request, Year $year)
    {
        try {
            // Validate the request data
            $validator = Validator::make($request->all(), [
                'year' => 'required|string',
                'from' => 'required|date',
                'to' => 'required|date',
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
            $year->update([
                'year' => $validated['year'],
                'from_date' => $validated['from'],
                'to_date' => $validated['to'],
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

    public function destroy(Year $year)
    {
        try {
            $year->delete();

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
