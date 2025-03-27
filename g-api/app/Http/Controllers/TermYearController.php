<?php

namespace App\Http\Controllers;

use App\Models\Term;
use App\Models\Year;
use App\Models\TermYear;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TermYearController extends Controller
{
    public function index($year)
    {
        try {
            $data = DB::table('term_years')
                ->join('terms', 'terms.id', '=', 'term_years.term_id')
                ->join('years', 'years.id', '=', 'term_years.year_id')
                ->select(
                    'term_years.id as id',
                    'term_years.year_id as year_id',
                    'years.year as year_name',
                    'terms.name as term_name',
                    'term_years.term_id as term_id',
                    'term_years.from_date as from',
                    'term_years.to_date as to',
                )
                ->where('term_years.year_id', $year)
                ->get();
                $list = $data->pluck('term_name')->join(', ');
                $retrieval = $data->pluck('year_name')->unique()->first();

            return response()->json([
                'statusCode' => '0',
                'retrieval' => $retrieval,
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
                'term_id' => 'required|array',
                'term_id.*' => 'required',
                'year_id' => 'required',
                'from' => 'required|array',
                'from.*' => 'required|date',
                'to' => 'required|array',
                'to.*' => 'required|date',
            ]);

             // check if year exists
             $checkExist = Year::findOrFail($validated['year_id']);
             if(!$checkExist){
                return response()->json([
                    'statusCode' => '1',
                    'message' => 'Year does not exist',
                ]);
             }
    
            \Log::info('storing TermYear payload: ' . json_encode($validated));
    
            $createdRecords = [];
            $userId = auth()->user()->id;
    
            $checkToDelete = TermYear::where('year_id', $validated['year_id'])
            ->whereNotIn('term_id', $validated['term_id'])
            ->delete();

            foreach ($validated['term_id'] as $index => $singleId) {
                
                $checkExistminor = Term::where('id', (int) $singleId)->exists();
                if (!$checkExistminor) {
                    continue;  
                }

                $singular_add = TermYear::updateOrCreate(
                    [
                        'year_id' => $validated['year_id'],
                        'term_id' => (int) $singleId
                    ],
                    [
                    'year_id' => $validated['year_id'],
                    'term_id' => (int) $singleId,
                    'from_date' => $validated['from'][$index],
                    'to_date' => $validated['to'][$index],
                    'created_by' => $userId,
                    ]
                );
    
                \Log::warning('Data TermYear created: ' .json_encode($singular_add));
                $createdRecords[] = $singular_add;
            }
            
            \Log::warning('List TermYear created: ' .json_encode($createdRecords));
    
            return response()->json([
                'statusCode' => '0',
                'message' => 'Successfully created',
                'data' => $createdRecords,
            ]);
        } catch (\Exception $e) {
            \Log::error('storing TermYear error: ' . $e->getMessage());
            return response()->json([
                'statusCode' => '2',
                'message' => 'An error occurred while processing',
                'error' => $e->getMessage(),
            ]);
        }
    }
    
    public function show(TermYear $term_year)
    {
        try {
            return response()->json([
                'statusCode' => '0',
                'data' => $term_year
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'statusCode' => '2',
                'message' => 'An error occurred while fetching the data',
                'error' => $e->getMessage()
            ]);
        }
    }

    

    public function destroy(TermYear $term_year)
    {
        try {
            $term_year->delete();

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
