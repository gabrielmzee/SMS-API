<?php

namespace App\Http\Controllers;

use App\Models\Term;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TermController extends Controller
{
    public function index()
    {
        try {
            $term = Term::all();

            return response()->json([
                'statusCode' => '0',
                'data' => $term
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
                 'name' => 'required|string'
             ]);

             $checkExist = Term::where('name', $validated['name'])->exists();
             if ($checkExist) {
                 return response()->json([
                     'statusCode' => '1',
                     'message' => 'Already exists',
                     'data' => $checkExist
                 ]);
             }

             // Create new record
             $term = Term::create([
                 'name' => $validated['name'],
                 'created_by' => auth()->user()->id,
             ]);

             return response()->json([
                 'statusCode' => '0',
                 'message' => 'Successfully created',
                 'data' => $term
             ]);
         } catch (\Exception $e) {
             return response()->json([
                 'statusCode' => '2',
                 'message' => 'An error occurred while processing',
                 'error' => $e->getMessage()
             ]);
         }
     }

    public function show(Term $term)
    {
        try {
            return response()->json([
                'statusCode' => '0',
                'data' => $term
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'statusCode' => '2',
                'message' => 'An error occurred while fetching the data',
                'error' => $e->getMessage()
            ]);
        }
    }

    public function update(Request $request, Term $term)
    {
        try {
            // Validate the request data
            $validator = Validator::make($request->all(), [
                'name' => 'required|string',
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
            $term->update([
                'name' => $validated['name'],
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

    public function destroy(Term $term)
    {
        try {
            $term->delete();

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
