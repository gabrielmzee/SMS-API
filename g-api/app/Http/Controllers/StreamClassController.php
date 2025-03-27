<?php

namespace App\Http\Controllers;

use App\Models\StreamClass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StreamClassController extends Controller
{
    public function index($class)
    {
        try {

            $data = DB::table('stream_classes')
                ->join('streams', 'streams.id', '=', 'stream_classes.stream_id')
                ->join('class_years', 'class_years.id', '=', 'stream_classes.class_year_id')
                ->join('classses', 'classses.id', '=', 'class_years.class_id')
                ->select(
                    'stream_classes.id as id',
                    'classses.id as class_id',
                    'classses.name as class_name',
                    'streams.name as stream_name',
                    'stream_classes.stream_id as stream_id'
                )
                ->where('stream_classes.class_year_id', $class)
                ->get();

                $list = $data->pluck('stream_name');

            return response()->json([
                'statusCode' => '0',
                'list' => $list,
                'data' => $data
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
                 'stream_id' => 'required|array',
                 'stream_id.*' => 'required',
                 'class_year_id' => 'required',
             ]);
     
             \Log::error('storing StreamClass payload: ' . json_encode($validated));
     
             $createdRecords = [];
             $userId = auth()->user()->id;
             $checkToDelete = StreamClass::where('class_year_id', $validated['class_year_id'])
                ->whereNotIn('stream_id', $validated['stream_id'])
                ->delete();
     
             foreach ($validated['stream_id'] as $singleId) {
                 $singular_add = StreamClass::updateOrCreate(
                    [
                        'class_year_id' => $validated['class_year_id'],
                        'stream_id' => (int) $singleId,  
                    ],
                    [
                     'class_year_id' => $validated['class_year_id'],
                     'stream_id' => (int) $singleId,  
                     'created_by' => $userId,
                     'updated_by' => $userId,
                 ]);
     
                 $createdRecords[] = $singular_add;
             }
     
             return response()->json([
                 'statusCode' => '0',
                 'message' => 'Successfully created',
                 'data' => $createdRecords,
             ]);
         } catch (\Exception $e) {
             \Log::error('storing StreamClass error: ' . $e->getMessage());
             return response()->json([
                 'statusCode' => '2',
                 'message' => 'An error occurred while processing',
                 'error' => $e->getMessage(),
             ]);
         }
     }

    public function show(StreamClass $stream_class)
    {
        try {
            return response()->json([
                'statusCode' => '0',
                'data' => $stream_class
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'statusCode' => '2',
                'message' => 'An error occurred while fetching the data',
                'error' => $e->getMessage()
            ]);
        }
    }

    public function update(Request $request, StreamClass $stream_class)
    {
        try {
            
            // Validate the request data
            $validator = Validator::make($request->all(), [
                'stream_id' => 'required',
                'class_id' => 'required',
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
            $stream_class->update([
                'stream_id' => $validated['stream_id'],
                'class_id' => $validated['class_id'],
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

    public function destroy(StreamClass $stream_class)
    {
        try {
            $stream_class->delete();

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
