<?php

namespace App\Http\Controllers;

use App\Models\ExamTerm;
use Illuminate\Http\Request;

class ExamTermController extends Controller
{
    public function index($term)
    {
        try {
            $data = DB::table('exam_terms')
                ->join('term_years', 'term_years.id', '=', 'exam_terms.term_year_id')
                ->join('terms', 'terms.id', '=', 'term_years.term_id')
                ->join('years', 'years.id', '=', 'term_years.year_id')
                ->join('exams', 'exams.id', '=', 'exam_terms.exam_id')
                ->select(
                    'term_years.id as id',
                    'years.year as year_name',
                    'years.id as year_id',
                    'terms.name as term_name',
                    'terms.id as term_id',
                    'exams.name as exam_name',
                    'exams.id as exam_id',
                    'exam_terms.from_date as from',
                    'exam_terms.to_date as to',
                )
                ->where('exam_terms.term_year_id', $term)
                ->get();

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
                 'term_year_id' => 'required|array',
                 'term_year_id.*' => 'required',
                 'exam_id' => 'required',
                 'from' => 'required|array',
                 'from.*' => 'required|date',
                 'to' => 'required|array',
                 'to.*' => 'required|date',
             ]);
 
              // check if exam exists
              $checkExist = Exam::findOrFail($validated['exam_id']);
              if(!$checkExist){
                 return response()->json([
                     'statusCode' => '1',
                     'message' => 'Exam does not exist',
                 ]);
              }
     
             \Log::info('storing ExamTerm payload: ' . json_encode($validated));
     
             $createdRecords = [];
             $userId = auth()->user()->id;
     
             $checkToDelete = ExamTerm::where('exam_id', $validated['exam_id'])
             ->whereNotIn('term_year_id', $validated['term_year_id'])
             ->delete();
 
             foreach ($validated['term_year_id'] as $index => $singleId) {
                 
                 $checkExistminor = TermYear::where('id', (int) $singleId)->exists();
                 if (!$checkExistminor) {
                     continue;  
                 }
 
                 $singular_add = ExamTerm::updateOrCreate(
                     [
                         'exam_id' => $validated['exam_id'],
                         'term_year_id' => (int) $singleId
                     ],
                     [
                     'exam_id' => $validated['exam_id'],
                     'term_year_id' => (int) $singleId,
                     'from_date' => $validated['from'][$index],
                     'to_date' => $validated['to'][$index],
                     'created_by' => $userId,
                     'updated_by' => $userId,
                     ]
                 );
     
                 \Log::warning('Data ExamTerm created: ' .json_encode($singular_add));
                 $createdRecords[] = $singular_add;
             }
             
             \Log::warning('List ExamTerm created: ' .json_encode($createdRecords));
     
             return response()->json([
                 'statusCode' => '0',
                 'message' => 'Successfully created',
                 'data' => $createdRecords,
             ]);
         } catch (\Exception $e) {
             \Log::error('storing ExamTerm error: ' . $e->getMessage());
             return response()->json([
                 'statusCode' => '2',
                 'message' => 'An error occurred while processing',
                 'error' => $e->getMessage(),
             ]);
         }
     }

    public function show(ExamTerm $exam_term)
    {
        try {
            return response()->json([
                'statusCode' => '0',
                'data' => $exam_term
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'statusCode' => '2',
                'message' => 'An error occurred while fetching the data',
                'error' => $e->getMessage()
            ]);
        }
    }


    public function destroy(ExamTerm $exam_term)
    {
        try {
            $exam_term->delete();

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
