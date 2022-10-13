<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::get("/wizkid/list",[UserController::class,"getUsers"]);
Route::get("/wizkid/filter-role-guest/{role}",[UserController::class,"filterByRoleForGuest"]);
Route::post("/wizkid/create",[UserController::class,"createUser"]);
Route::put("/wizkid/update/{id}",[UserController::class,"updateUser"]);
Route::delete("/wizkid/delete/{id}",[UserController::class,"deleteUser"]);

Route::post("login",[AuthController::class,"login"]);

Route::group(['middleware' => 'auth:api'], function () {
  Route::get("/wizkid/list-full",[UserController::class,"getUsersCompleteInformations"]);
  Route::get("/wizkid/fire/{id}",[UserController::class,"fire"]);
  Route::get("/wizkid/unfire/{id}",[UserController::class,"unfire"]);
  Route::get("/wizkid/filter-role-user/{role}",[UserController::class,"filterByRoleForUser"]);

});