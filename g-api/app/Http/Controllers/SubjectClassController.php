<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use App\Models\ClassYear;
use App\Models\SubjectClass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SubjectClassController extends Controller
{
    public function index($class_year_id)
    {
        try {
            $data = DB::table('subject_classes')
            ->join('subjects', 'subjects.id', '=', 'subject_classes.subject_id')
            ->join('class_years', 'class_years.id', '=', 'subject_classes.class_year_id')
            ->join('classses', 'classses.id', '=', 'class_years.class_id')
            ->select(
                'subject_classes.id as id',
                'classses.id as class_id',
                'classses.name as class_name',
                'subjects.id as subject_id',
                'subjects.name as subject_name',
                'subjects.code as subject_code',
            )
            ->where('subject_classes.class_year_id', $class_year_id)
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
                'subject_id' => 'required|array',
                'subject_id.*' => 'required',
                'class_year_id' => 'required',
            ]);
    
            $checkExist = ClassYear::findOrFail($validated['class_year_id']);
            if(!$checkExist){
               return response()->json([
                   'statusCode' => '1',
                   'message' => 'Class does not exist',
               ]);
            }
            \Log::error('storing SubjectClass payload: ' . json_encode($validated));
    
            $createdRecords = [];
            $userId = auth()->user()->id;
    
            $checkToDelete = SubjectClass::where('class_year_id', $validated['class_year_id'])
                ->whereNotIn('subject_id', $validated['subject_id'])
                ->delete();
            foreach ($validated['subject_id'] as $subjectId) {

                $checkExistminor = Subject::where('id', (int) $subjectId)->exists();
                if (!$checkExistminor) {
                    continue;  
                }
                $subject_class = SubjectClass::updateOrCreate(
                        [
                        'class_year_id' => $validated['class_year_id'],
                        'subject_id' => (int) $subjectId,
                        ],
                        [
                        'class_year_id' => $validated['class_year_id'],
                        'subject_id' => (int) $subjectId,  
                        'created_by' => $userId,
                        'updated_by' => $userId,
                    ]
                );
    
                $createdRecords[] = $subject_class;
            }
    
            return response()->json([
                'statusCode' => '0',
                'message' => 'Successfully created',
                'data' => $createdRecords,
            ]);
        } catch (\Exception $e) {
            \Log::error('storing SubjectClass error: ' . $e->getMessage());
            return response()->json([
                'statusCode' => '2',
                'message' => 'An error occurred while processing',
                'error' => $e->getMessage(),
            ]);
        }
    }
    
    

    public function show(SubjectClass $subject_class)
    {
        try {
            return response()->json([
                'statusCode' => '0',
                'data' => $subject_class
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'statusCode' => '2',
                'message' => 'An error occurred while fetching the data',
                'error' => $e->getMessage()
            ]);
        }
    }

    public function update(Request $request, SubjectClass $subject_class)
    {
        try {
            // Validate the request data
            $validator = Validator::make($request->all(), [
                'subject_id' => 'required',
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
            $subject_class->update([
                'class_id' => $validated['class_id'],
                'subject_id' => $validated['subject_id'],
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

    public function destroy(SubjectClass $subject_class)
    {
        try {
            $subject_class->delete();

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
