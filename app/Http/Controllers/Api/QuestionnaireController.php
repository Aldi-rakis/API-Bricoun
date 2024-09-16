<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Questionnaire;
use Illuminate\Http\Request;

class QuestionnaireController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

      $question=Questionnaire::all();

      return response()->json([
        'message' => 'Sukses menampikan pertanyaan ',
        'data' => $question,
        
    ], 200);
}

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $question=Questionnaire::create([
            'question' => $request->question,
        ]);

        return response()->json([
            'message' => 'Sukses menambahkan pertanyaan ',
            'data' => $question,
            
        ], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
