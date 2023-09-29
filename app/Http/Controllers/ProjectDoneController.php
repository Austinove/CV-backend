<?php

namespace App\Http\Controllers;

use App\Models\ProjectDone;
use Illuminate\Http\Request;

class ProjectDoneController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return ProjectDone::where(["person_id" => $request->id])->get();
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
        $createProj = ProjectDone::create([
            "person_id" => $request->userId, 
            "project_desc" => $request->projectDesc,
            "project_title" => $request->projectTitle,
            "start_date" => date('Y-m-d', strtotime($request->startDate)),
            "end_date" => date('Y-m-d', strtotime($request->endDate)),
        ]);
        return ProjectDone::where(["person_id" => $request->userId ])->get();
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ProjectDone  $projectDone
     * @return \Illuminate\Http\Response
     */
    public function show(ProjectDone $projectDone)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ProjectDone  $projectDone
     * @return \Illuminate\Http\Response
     */
    public function edit(ProjectDone $projectDone)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ProjectDone  $projectDone
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ProjectDone $projectDone)
    {
        $update_proj = ProjectDone::find($request->id);
        $update_proj->update([
            "project_desc" => $request->projectDesc,
            "project_title" => $request->projectTitle,
            "start_date" => date('Y-m-d', strtotime($request->startDate)),
            "end_date" => date('Y-m-d', strtotime($request->endDate)),
        ]);
        return ProjectDone::where(["person_id" => $update_proj->person_id ])->get();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ProjectDone  $projectDone
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        ProjectDone::find($request->id)->delete();
        return ProjectDone::where(["person_id" => $request->userId])->get();
    }
}
