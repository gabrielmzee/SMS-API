<?php

namespace App\Http\Controllers;

use App\Models\Year;
use App\Models\Classs;
use App\Models\ClassYear;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClassYearController extends Controller
{
    public function index($year)
    {
        try {

            $data = DB::table('class_years')
                ->join('classses', 'classses.id', '=', 'class_years.class_id')
                ->join('years', 'years.id', '=', 'class_years.year_id')
                ->select(
                    'class_years.id as id',
                    'years.id as year_id',
                    'years.year as year_name',
                    'classses.name as class_name',
                    'class_years.class_id as class_id',
                )
                ->where('class_years.year_id', $year)
                ->get();
                // \Log::info('class_years: ' . $data);

            return response()->json([
                'statusCode' => '0',
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
                 'class_id' => 'required|array',
                 'class_id.*' => 'required',
                 'year_id' => 'required',
             ]);
     
             // check if year exists
             $checkExist = Year::findOrFail($validated['year_id']);
             if(!$checkExist){
                return response()->json([
                    'statusCode' => '1',
                    'message' => 'Year does not exist',
                ]);
             }
             \Log::error('storing ClassYear payload: ' . json_encode($validated));
     
             $createdRecords = [];
             $userId = auth()->user()->id;
     
             $checkToDelete = ClassYear::where('year_id', $validated['year_id'])
                ->whereNotIn('class_id', $validated['class_id'])
                ->delete();
             foreach ($validated['class_id'] as $singleId) {
                $checkExistminor = Classs::where('id', (int) $singleId)->exists();
                if (!$checkExistminor) {
                    continue;  
                }
                
                 $singular_add = ClassYear::updateOrCreate(
                    [
                        'year_id' => $validated['year_id'],
                        'class_id' => (int) $singleId, 
                    ],
                    [
                     'year_id' => $validated['year_id'],
                     'class_id' => (int) $singleId,  
                     'created_by' => $userId,
                    ]
                );
     
                 $createdRecords[] = $singular_add;
             }
     
             return response()->json([
                 'statusCode' => '0',
                 'message' => 'Successfully created',
                 'data' => $createdRecords,
             ]);
         } catch (\Exception $e) {
             \Log::error('storing ClassYear error: ' . $e->getMessage());
             return response()->json([
                 'statusCode' => '2',
                 'message' => 'An error occurred while processing',
                 'error' => $e->getMessage(),
             ]);
         }
     }

    public function show(ClassYear $class_year)
    {
        try {
            return response()->json([
                'statusCode' => '0',
                'data' => $class_year
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'statusCode' => '2',
                'message' => 'An error occurred while fetching the data',
                'error' => $e->getMessage()
            ]);
        }
    }


    public function destroy(ClassYear $class_year)
    {
        try {
            $class_year->delete();

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
