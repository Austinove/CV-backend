<?php

namespace App\Http\Controllers;

use App\Models\PersonalInfo;
use Illuminate\Http\Request;

class PersonalInfoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // response()->json(["msg" => "Messages Sent Successfuly"]);
        // return PersonalInfo::all()->first();
    }
    public function fetch_info(Request $request)
    {
        $userInfo = PersonalInfo::find($request->id);
        return response()->json($userInfo);
    }
    
    // public function fetchInfo()
    // {

    // }

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
        // return date('Y-m-d', strtotime($request->dob));
        //when registering a new user
        if($request->id){
            
            if($request->skills){
                //updating skills
                $update_info = PersonalInfo::find($request->id);
                $update_info->update(["skills" => $request->skills]);
                return $update_info;
            }else if($request->statement){
                //updating statement
                 $update_info = PersonalInfo::find($request->id);
                 $update_info->update(["statement" => $request->statement]);
                 return $update_info;
            } else {
                //updating personal info
                $update_info = PersonalInfo::find($request->id);
                $update_info->update([
                    "full_names" => $request->fullName,
                    "address" => $request->address,
                    "email" => $request->email,
                    "mob_number" => $request->mobNumber,
                    "dob" => date('Y-m-d', strtotime($request->dob)),
                ]);
                return $update_info;
            }
        } else{
            $userInfo =  PersonalInfo::where([
                "email" => $request->email,
                "mob_number" => $request->mobNumber,
                "dob" => date('Y-m-d', strtotime($request->dob))
            ])->first();
            if($userInfo){
                return $userInfo;
            } else {
                //new User
                $createUser = PersonalInfo::create([
                    "full_names" => $request->fullName,
                    "address" => $request->address,
                    "email" => $request->email,
                    "mob_number" => $request->mobNumber,
                    "dob" => date('Y-m-d', strtotime($request->dob)),
                ]);
                return $createUser;
            }
        }
    }

    public function upload_profile(Request $request, $id){
        $saveName = "";
        if ($request->file("photo")) {
            $file = $request->file("photo");
            $nameWithExt = $file->getClientOriginalName();
            $name = pathinfo($nameWithExt, PATHINFO_FILENAME);
            $extension = $file->getClientOriginalExtension();
            $saveName = $name . "_" . time() . "." . $extension;
            $file->move("clients", $saveName);
        }
        //updating personal info with image.
        $update_info = PersonalInfo::find($id);
        $update_info->update([
            "image" => $saveName,
        ]);
        return $update_info;
        try {
            return response()->json(["msg" => "Student Saved Successfully"], 200);
        } catch (QueryException $th) {
            throw $th;
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\PersonalInfo  $personalInfo
     * @return \Illuminate\Http\Response
     */
    public function show(PersonalInfo $personalInfo)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\PersonalInfo  $personalInfo
     * @return \Illuminate\Http\Response
     */
    public function edit(PersonalInfo $personalInfo)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\PersonalInfo  $personalInfo
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, PersonalInfo $personalInfo)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\PersonalInfo  $personalInfo
     * @return \Illuminate\Http\Response
     */
    public function destroy(PersonalInfo $personalInfo)
    {
        //
    }
}
