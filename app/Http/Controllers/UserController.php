<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\FileService;
use Illuminate\Http\Request;
use Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Exception;
use InvalidArgumentException;
use Illuminate\Support\Str;



class UserController extends Controller
{

    public function createUser(Request $request)
    {
        //validate Wizkid data
        //rules of each field
        $rules = [
            'name' => 'required',
            'role' => 'required|in:boss,developer,designer,intern',
            'email' => 'email|required',
            'picture' => "required",
            'phone_number' => 'regex:/[0-9]{9}/'

        ];
        //message for each rule
        $messages = [
            'name.required' => 'name is required',
            'role.required' => "role is required",
            'role.in' => "role should be boss,developer,designer  or intern",
            'email.required' => "email is required",
            'email.email'    => "the format of the email should be valid",
            'picture.file' => "the picture should be a file",
            'picture.image' => "the picture should be file of type image",
            'picture.required' => "the picture is required",
            'phone_number.regex' => "phone format not valid"

        ];
        $validator = Validator::make($request->all(), $rules, $messages);

        // if  one onr the fieldd is not valide
        if ($validator->fails()) {
            return response()->json($validator->messages(), 400);
        } else {
            try {
                // creating  a new wizkid
                $user = new User();
                $user->name = $request->name;
                $user->role = $request->role;
                $user->email = $request->email;
                $user->phone_number = $request->phone_number;
                //generate password
                $password = Str::random(8);
                $user->password = Hash::make($password);

                //store picture
                $user->picture = date("Y_m_d_His") . '_' . $request->file("picture")->getClientOriginalName();
                FileService::Upload($request->file("picture"));
                $user->save();
                return response()->json($user, 201);
            } catch (Exception $e) {
                Log::info($e->getMessage());
                throw new InvalidArgumentException($e->getMessage());
                return response()->json("Enable to Add Wizkad", 401);
            }
        }
    }

    public function updateUser(Request $request, $id)
    {
        //validate Wizkid data
        //rules of each field
        $rules = [
            'name' => 'required',
            'role' => 'required|in:boss,developer,designer,intern',
            'email' => 'email|required',
            'picture' => "required",
            'phone_number' => 'regex:/[0-9]{9}/'

        ];
        //message for each rule
        $messages = [
            'name.required' => 'name is required',
            'role.required' => "role is required",
            'role.in' => "role should be boss,developer,designer  or intern",
            'email.required' => "email is required",
            'email.email'    => "the format of the email should be valid",
            'picture.file' => "the picture should be a file",
            'picture.image' => "the picture should be file of type image",
            'picture.required' => "the picture is required",
            'phone_number.regex' => "phone format not valid"

        ];
        $validator = Validator::make($request->all(), $rules, $messages);

        // if  one onr the fieldd is not valide
        if ($validator->fails()) {
            return response()->json($validator->messages(), 400);
        } else {
            try {
                // update  a new wizkid
                $user = User::find($id);
                $user->name = $request->name;
                $user->role = $request->role;
                $user->email = $request->email;
                $user->phone_number = $request->phone_number;
                //generate password
                $password = Str::random(8);
                $user->password = Hash::make($password);

                //update picture
                $user->picture = date("Y_m_d_His") . '_' . $request->file("picture")->getClientOriginalName();
                FileService::Upload($request->file("picture"));
                $user->save();
                return response()->json($user, 201);
            } catch (Exception $e) {
                Log::info($e->getMessage());
                throw new InvalidArgumentException($e->getMessage());
                return response()->json("Enable to update Wizkad", 401);
            }
        }
    }

    public function getUsers()
    {
        $users = User::select("name", "role", "picture")->get();
        return response()->json($users->all(), 201);
    }

    public function deleteUser($id)
    {
        try {
            User::find($id)->delete();
            return response()->json("Wizkid deleted Succussfuly", 201);
        } catch (Exception $e) {
            Log::info($e->getMessage());
            throw new InvalidArgumentException($e->getMessage());
            return response()->json("Enable to delete Wizkad", 401);
        }
    }
}
