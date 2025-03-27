<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class NIDAController extends Controller
{
    public function verification(Request $request) {
        try {

            $validated = $request->validate([
                'NIN' => 'required|digits:20',
                'RQCODE' => 'nullable|string',
                'ANSWER' => 'nullable|string',
            ]);

            $validated = (object) $validated;

            $data = [
                'NIN' => $validated->NIN,
                'RQCODE' => $validated->RQCODE ?? "",
                'ANSWER' => $validated->ANSWER ?? ""
            ];

            $nida = Http::post('http://41.59.227.238:8888/NIDAVerifier', $data);

            if($nida->successful()){

                $nida = $nida->object();

                if($nida->CODE == '120'){
                    return response()->json([
                        'statusCode' => '0',
                        'CODE' => $nida->CODE,
                        'message' => 'Successfully retrieved question',
                        'data' => [
                            'NIN' => $nida->NIN,
                            'RQCODE' => $nida->RQCode,
                            'QN' => [
                                'qnEN' => $nida->EN,
                                'qnSW' => $nida->SW,
                            ]
                        ]
                    ]);
                }
                if($nida->CODE == '00'){
                    return response()->json([
                        'statusCode' => '0',
                        'CODE' => $nida->CODE,
                        'message' => 'Successfully retrieved information',
                        'data' => [
                            'nin' => $nida->NIN,
                            'first_name' => $nida->FIRSTNAME,
                            'middle_name' => $nida->MIDDLENAME,
                            'last_name' => $nida->SURNAME,
                            'sex' => $nida->SEX,
                            'date_of_birth' => $nida->DATEOFBIRTH,
                            'birth_country' => $nida->BIRTHCOUNTRY,
                            'birth_region' => $nida->BIRTHREGION,
                            'birth_district' => $nida->BIRTHDISTRICT,
                            'birth_ward' => $nida->BIRTHWARD,
                            'nationality' => $nida->NATIONALITY,
                            'photo' => $nida->PHOTO,
                            'signature' => $nida->SIGNATURE
                        ]
                    ]);
                }
            }

            return response()->json([
                'statusCode' => '1',
                'message' => 'An error occurred while processing',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'statusCode' => '2',
                'message' => 'An error occurred while processing',
                'error' => $e->getMessage()
            ]);
        }
    }
}
