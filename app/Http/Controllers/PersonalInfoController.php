<?php

namespace App\Http\Controllers;

use App\Models\PersonalInfo;
use Illuminate\Http\Request;
use Carbon\Carbon;

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
    }

    public function generate(){
        $this->send_email();
        $userInfo = PersonalInfo::with('education', 'career', 'project', 'referee')->find(4);
        $path = public_path('clients/');
        $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor(public_path('template/cv_template.docx'));
        $templateProcessor->setImageValue('UserLogo', array('path' => $path.$userInfo->image, 'width' => 100, 'height' => 100, 'ratio' => false));
        //entering assigning details of user
        $templateProcessor->setValue('fullnames', $userInfo->full_names);
        $templateProcessor->setValue('introduction', $userInfo->statement);
        $templateProcessor->setValue('email', $userInfo->email);
        $templateProcessor->setValue('contact', $userInfo->mob_number);
        $templateProcessor->setValue('location', $userInfo->address);
        $templateProcessor->setValue('dob', $userInfo->dob);
        $templateProcessor->setValue('skills', $userInfo->skills);

        //setting up referees
        $replacements = array();
        foreach ($userInfo->referee as $key => $referee_details) {
            array_push($replacements, [
                'referee_name' => $referee_details->name.', '.$referee_details->title, 
                'referee_company' => $referee_details->company,
                'referee_contacts' => $referee_details->number.', '.$referee_details->email,
                'referee_spacer' => ''
            ]);
        }
        $templateProcessor->cloneBlock('referee', 0, true, false, $replacements);

        //setting up education
        $education_block = array();
        foreach ($userInfo->education as $key => $education_details) {
            $start_date = new Carbon($education_details->start_date);
            $end_date = new Carbon($education_details->end_date);
            array_push($education_block, [
                'school' => $education_details->school.',     '.$start_date->format('M Y').' - '.$end_date->format('M Y'), 
                'schoolcourse' => $education_details->qualification,
                'schoolgrade' => $education_details->grade,
                'schoolspacer' => ''
            ]);
        }
        $templateProcessor->cloneBlock('education', 0, true, false, $education_block);

        //setting up experience
        $experience_block = array();
        foreach ($userInfo->career as $key => $career_details) {
            $start_date = new Carbon($career_details->start_date);
            if($career_details->end_date == '') {
                $end_date = 'Current';
            } else {
                $date = new Carbon($career_details->end_date);
                $end_date = $date->format('M Y');
            }
            array_push($experience_block, [
                'emptitle' => $career_details->job_title, 
                'empdate' => $start_date->format('M Y').' - '.$end_date,
                'empintr' => $career_details->empintr,
                'empdesc' => $career_details->job_desc,
                'empdesc' => $career_details->job_desc,
                'empspacer' => ''
            ]);
        }
        $templateProcessor->cloneBlock('work', 0, true, false, $experience_block);

        //setting up projects
        $projects_block = array();
        foreach ($userInfo->project as $key => $project_detail) {
            $start_date = new Carbon($project_detail->start_date);
            $end_date = new Carbon($project_detail->end_date);
            array_push($projects_block, [
                'projtitle' => $project_detail->project_title, 
                'projdate' => $start_date->format('M Y').' - '.$end_date->format('M Y'),
                'projdesc' => $project_detail->project_desc,
                'projspacer' => ''
            ]);
        }
        $templateProcessor->cloneBlock('project', 0, true, false, $projects_block);

        $file_name = strtolower(str_replace(" ","_",$userInfo->full_names)).'_cv_'.$userInfo->id.'.docx';
        $file_full_path = public_path('cvs/'.$file_name);
        try {
            $templateProcessor->saveAs($file_full_path);
        } catch (Exception $e) {
            return $e;
        }
        return response()->json(
            [
                "msg" => "Document Saved Successfully", 
                "status" => 200
            ], 200);
    }

    public function send_email() {
        // // Recipient
        // $to = "bryanovicaustenove@gmail.com"; 
        
        // // Sender 
        // $from = "brian.kulaba@auto-hoevel.de";
        // $fromName = "Bryan";

        // // Email subject
        // $subject = 'This is a test';

        // // Attachment file
        // $file = public_path('cvs/kulaba_brian_cv_4.docx');

        // // Email body content
        // $htmlContent = ' 
        //     <h3>This is a test</h3> 
        //     <p>This is a test PHP email with and attachment.</p> 
        // ';

        // // Header for sender info
        // $headers = "From: $fromName" . " <" . $from . ">";

        // // Boundary
        // $semi_rand = md5(time());
        // $mime_boundary = "==Multipart_Boundary_x{$semi_rand}x";

        // // Headers for attachment
        // $headers .= "\nMIME-Version: 1.0\n" . "Content-Type: multipart/mixed;\n" . " boundary=\"{$mime_boundary}\"";

        // // Multipart boundary
        // $message = "--{$mime_boundary}\n" . "Content-Type: text/html; charset=\"UTF-8\"\n" . "Content-Transfer-Encoding: 7bit\n\n" . $htmlContent . "\n\n";

        // // Preparing attachment
        // if (!empty($file) > 0)
        // {
        //     if (is_file($file))
        //     {
        //         $message .= "--{$mime_boundary}\n";
        //         $fp = fopen($file, "rb");
        //         $data = fread($fp, filesize($file));

        //         fclose($fp);
        //         $data = chunk_split(base64_encode($data));
        //         $message .= "Content-Type: application/octet-stream; name=\"" . basename($file) . "\"\n" . "Content-Description: " . basename($file) . "\n" . "Content-Disposition: attachment;\n" . " filename=\"" . basename($file) . "\"; size=" . filesize($file) . ";\n" . "Content-Transfer-Encoding: base64\n\n" . $data . "\n\n";
        //     }
        // }
        // $message .= "--{$mime_boundary}--";
        // $returnpath = "-f" . $from;

        // // Send email
        // $mail = mail($to, $subject, $message, $headers, $returnpath);
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
