<?php

namespace App\Http\Controllers;

use App\Models\Education;
use Illuminate\Http\Request;

class EducationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return Education::where(["person_id" => $request->id])->get();
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
        $createEduc = Education::create([
            "person_id" => $request->userId, 
            "school" => $request->school,
            "qualification" => $request->qualification,
            "grade" => $request->grade,
            "start_date" => date('Y-m-d', strtotime($request->startDate)),
            "end_date" => date('Y-m-d', strtotime($request->endDate)),
        ]);
        return Education::where(["person_id" => $request->userId ])->get();
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Education  $education
     * @return \Illuminate\Http\Response
     */
    public function show(Education $education)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Education  $education
     * @return \Illuminate\Http\Response
     */
    public function edit(Education $education)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Education  $education
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $update_educ = Education::find($request->id);
        $update_educ->update([
            "school" => $request->school,
            "qualification" => $request->qualification,
            "grade" => $request->grade,
            "start_date" => date('Y-m-d', strtotime($request->startDate)),
            "end_date" => date('Y-m-d', strtotime($request->endDate)),
        ]);
        return Education::where(["person_id" => $update_educ->person_id ])->get();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Education  $education
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        Education::find($request->id)->delete();
        return Education::where(["person_id" => $request->userId])->get();
    }
}
