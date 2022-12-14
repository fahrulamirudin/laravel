<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Candidate;
use App\Models\SkillSet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CandidateController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //get data from table posts
        $models = Candidate::latest()->get();

        //make response JSON
        return response()->json([
            'success' => true,
            'message' => 'List Data Post',
            'data'    => $models
        ], 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //set validation
        $validator = Validator::make($request->all(), [
            'job_id'   => 'required',
            'name'   => 'required',
            'email' => 'required|email',
            'phone' => 'required|numeric|digits_between:10,13',
            'year' => 'required',
            'skillsets' => 'required',
        ]);

        //response error validation
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $check_email =  Candidate::where('email', $request->email)->first();
        $check_phone =  Candidate::where('phone', $request->phone)->first();
        if ($check_email != null) :
            return response()->json([
                'success' => false,
                'message' => 'Email Telah Digunakan',
            ], 409);
        endif;
        if ($check_phone != null) :
            return response()->json([
                'success' => false,
                'message' => 'Telepon Telah Digunakan',
            ], 409);
        endif;
        //save to database
        $model = Candidate::create([
            'job_id'     => $request->job_id,
            'name'     => $request->name,
            'email'   => $request->email,
            'phone'   => $request->phone,
            'year'   => $request->year,
        ]);
        $request_skills = $request->skillsets;
        $uniq_skills=array_unique($request_skills);
        //success save to database
        if ($model) {
            $skillSets=array();
            foreach ($uniq_skills as $skill) :
                $skillSets[] = SkillSet::create([
                    'candidate_id' => $model->id,
                    'skill_id' => $skill,
                ]);
            endforeach;
            $model['$skillSets'] = $skillSets;
            return response()->json([
                'success' => true,
                'message' => 'Post Created',
                'data'    => $model
            ], 201);
        }

        //failed save to database
        return response()->json([
            'success' => false,
            'message' => 'Post Failed to Save',
        ], 409);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //find post by ID
        $models = Candidate::findOrfail($id);

        //make response JSON
        return response()->json([
            'success' => true,
            'message' => 'Detail Data Post',
            'data'    => $models
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Candidate $candidate)
    {
        //set validation
        $validator = Validator::make($request->all(), [
            'job_id'   => 'required',
            'name'   => 'required',
            'email' => 'required',
            'phone' => 'required',
            'year' => 'required',
        ]);

        //response error validation
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        //find post by ID
        $candidate = Candidate::findOrFail($candidate->id);

        if ($candidate) {

            //update post
            $candidate->update([
                'job_id'     => $request->job_id,
                'name'     => $request->name,
                'email'   => $request->email,
                'phone'   => $request->phone,
                'year'   => $request->year,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Post Updated',
                'data'    => $candidate
            ], 200);
        }

        //data post not found
        return response()->json([
            'success' => false,
            'message' => 'Post Not Found',
        ], 404);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $candidate = Candidate::findOrfail($id);

        if ($candidate) {

            //delete post
            $candidate->delete();

            return response()->json([
                'success' => true,
                'message' => 'Post Deleted',
            ], 200);
        }

        //data post not found
        return response()->json([
            'success' => false,
            'message' => 'Post Not Found',
        ], 404);
    }
}
