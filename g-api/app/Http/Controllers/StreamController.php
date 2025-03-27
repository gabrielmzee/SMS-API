<?php

namespace App\Http\Controllers;

use App\Models\Stream;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class StreamController extends Controller
{
    public function index()
    {
        try {
            $stream = Stream::orderBy('name', 'asc')->get();

            return response()->json([
                'statusCode' => '0',
                'data' => $stream
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
             ]);

             $checkExist = Stream::where('name', $validated['name'])->exists();
             if ($checkExist) {
                 return response()->json([
                     'statusCode' => '1',
                     'message' => 'Already exists',
                     'data' => $checkExist
                 ]);
             }

             // Create new record
             $stream = Stream::create([
                 'name' => $validated['name'],
                 'created_by' => auth()->user()->id,
             ]);

             return response()->json([
                 'statusCode' => '0',
                 'message' => 'Successfully created',
                 'data' => $stream
             ]);
         } catch (\Exception $e) {
             return response()->json([
                 'statusCode' => '2',
                 'message' => 'An error occurred while processing',
                 'error' => $e->getMessage()
             ]);
         }
     }

    public function show(Stream $stream)
    {
        try {
            return response()->json([
                'statusCode' => '0',
                'data' => $stream
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'statusCode' => '2',
                'message' => 'An error occurred while fetching the data',
                'error' => $e->getMessage()
            ]);
        }
    }

    public function update(Request $request, Stream $stream)
    {
        try {
            // Validate the request data
            $validator = Validator::make($request->all(), [
                'name' => 'required',
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
            $stream->update([
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

    public function destroy(Stream $stream)
    {
        try {
            $stream->delete();

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
