<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PersonalInfoController;
use App\Http\Controllers\EducationController;
use App\Http\Controllers\CareerController;
use App\Http\Controllers\ProjectDoneController;
use App\Http\Controllers\RefereeController;
use App\Http\Middleware\CheckStatus;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

// Route::get('/info', function () {
//     return view('welcome');
// });
Route::middleware([CheckStatus::class])->group(function(){
    Route::post('/info', [PersonalInfoController::class, "fetch_info"]);
    Route::post('/addInfo', [PersonalInfoController::class, "store"]);
    Route::put('/upload_profile/{id}', [PersonalInfoController::class, "upload_profile"]);
    Route::post('/generate', [PersonalInfoController::class, "generate"]);

    //education
    Route::post('/education', [EducationController::class, "index"]);
    Route::post('/add_education', [EducationController::class, "store"]);
    Route::post('/edit_education', [EducationController::class, "update"]);
    Route::post('/delete_education', [EducationController::class, "destroy"]);

    //career
    Route::post('/career', [CareerController::class, "index"]);
    Route::post('/add_career', [CareerController::class, "store"]);
    Route::post('/edit_career', [CareerController::class, "update"]);
    Route::post('/delete_career', [CareerController::class, "destroy"]);

    //project
    Route::post('/project', [ProjectDoneController::class, "index"]);
    Route::post('/add_project', [ProjectDoneController::class, "store"]);
    Route::post('/edit_project', [ProjectDoneController::class, "update"]);
    Route::post('/delete_project', [ProjectDoneController::class, "destroy"]);

    //referees
    Route::post('/referee', [RefereeController::class, "index"]);
    Route::post('/add_referee', [RefereeController::class, "store"]);
    Route::post('/edit_referee', [RefereeController::class, "update"]);
    Route::post('/delete_referee', [RefereeController::class, "destroy"]);
});