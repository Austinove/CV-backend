<?php

namespace App\Http\Controllers;

use App\Models\Career;
use Illuminate\Http\Request;

class CareerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return Career::where(["person_id" => $request->id])->get();
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
        $request->endDate != "" ?
        $end_time = date('Y-m-d', strtotime($request->endDate)):
        $end_time = null;
        $createCareer = Career::create([
            "person_id" => $request->user_id, 
            "company" => $request->company,
            "job_title" => $request->jobTitle,
            "job_intro" => $request->jobIntro,
            "job_desc" => $request->jobDesc,
            "start_date" => date('Y-m-d', strtotime($request->startDate)),
            "end_date" => $end_time,
        ]);
        return Career::where(["person_id" => $request->user_id ])->get();
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Career  $career
     * @return \Illuminate\Http\Response
     */
    public function show(Career $career)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Career  $career
     * @return \Illuminate\Http\Response
     */
    public function edit(Career $career)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Career  $career
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $update_career = Career::find($request->id);
        $request->endDate != "" ?
        $end_time = date('Y-m-d', strtotime($request->endDate)):
        $end_time = null;
        $update_career->update([
            "company" => $request->company,
            "job_title" => $request->jobTitle,
            "job_intro" => $request->jobIntro,
            "job_desc" => $request->jobDesc,
            "start_date" => date('Y-m-d', strtotime($request->startDate)),
            "end_date" => $end_time,
        ]);
        return Career::where(["person_id" => $update_career->person_id ])->get();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Career  $career
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        Career::find($request->id)->delete();
        return Career::where(["person_id" => $request->userId])->get();
    }
}
