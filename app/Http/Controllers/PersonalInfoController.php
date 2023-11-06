<?php

namespace App\Http\Controllers;

use App\Models\PersonalInfo;
use Illuminate\Http\Request;
use Carbon\Carbon;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Illuminate\Support\Facades\File;

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
                    "lang" => $request->lang,
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

    public function generate(Request $request){
        $userInfo = PersonalInfo::with('education', 'career', 'project', 'referee')->find($request->person_id);
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

        //delete file if exists
        if (file_exists(public_path('cvs/'.$file_name))) {
            File::delete('cvs/' . $file_name);
        }

        //saving file
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

    // public function send_email() {
    //     require base_path("vendor/autoload.php");
    //     $mail = new PHPMailer(true);     // Passing `true` enables exceptions
 
    //     try {
 
    //         // Email server settings
    //         $mail->SMTPDebug = SMTP::DEBUG_SERVER;
    //         $mail->isSMTP();
    //         $mail->Host = 'academicmasterug.com';             //  smtp host
    //         $mail->SMTPAuth = true;
    //         $mail->Username ='';   //  sender username
    //         $mail->Password = '';       // sender password
    //         $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;                  // encryption - ssl/tls
    //         $mail->Port = 456;                          // port - 587/465
 
    //         $mail->setFrom('', 'Bryan');
    //         $mail->addAddress('bryanovicaustenove@gmail.com');
    //         // $mail->addCC($request->emailCc);
    //         // $mail->addBCC($request->emailBcc);
 
    //         $mail->addReplyTo('kbryan@academicmasterug.com', 'Bryan');
 
    //         // if(isset($_FILES['emailAttachments'])) {
    //         //     for ($i=0; $i < count($_FILES['emailAttachments']['tmp_name']); $i++) {
    //         //         $mail->addAttachment($_FILES['emailAttachments']['tmp_name'][$i], $_FILES['emailAttachments']['name'][$i]);
    //         //     }
    //         // }
 
 
    //         $mail->isHTML(true);                // Set email content format to HTML
 
    //         $mail->Subject = "Testing Email server";
    //         $mail->Body    = "This is for testing email server";
 
    //         // $mail->AltBody = plain text version of email body;
 
    //         if( !$mail->send() ) {
    //             return $mail->ErrorInfo;
    //         }
            
    //         else {
    //             return "Email has been sent.";
    //         }
 
    //     } catch (Exception $e) {
    //         return 'Message could not be sent.';
    //     }
    // }

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
