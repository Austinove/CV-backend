<?php

namespace App\Http\Controllers;

use App\Models\Referee;
use Illuminate\Http\Request;

class RefereeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return Referee::where(["person_id" => $request->id])->get();
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
        $create_ref = Referee::create([
            "person_id" => $request->userId, 
            "name" => $request->fullName,
            "title" => $request->title,
            "company" => $request->company,
            "number" => $request->mobNumber,
            "email" => $request->email
        ]);
        return Referee::where(["person_id" => $request->userId ])->get();
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Referee  $referee
     * @return \Illuminate\Http\Response
     */
    public function show(Referee $referee)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Referee  $referee
     * @return \Illuminate\Http\Response
     */
    public function edit(Referee $referee)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Referee  $referee
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $update_ref = Referee::find($request->id);
        $update_ref->update([ 
            "name" => $request->fullName,
            "title" => $request->title,
            "company" => $request->company,
            "number" => $request->mobNumber,
            "email" => $request->email
        ]);
        return Referee::where(["person_id" => $update_ref->person_id ])->get();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Referee  $referee
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        Referee::find($request->id)->delete();
        return Referee::where(["person_id" => $request->userId])->get();
    }
}
