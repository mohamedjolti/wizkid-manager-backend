<?php

namespace App\Http\Controllers;

use App\Mail\FireMail;
use App\Mail\unFireMail;
use App\Models\User;
use App\Services\FileService;
use Illuminate\Http\Request;
use Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Exception;
use InvalidArgumentException;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;




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
            'picture' => "required",
            'phone_number' => 'regex:/[0-9]{9}/'

        ];
        //message for each rule
        $messages = [
            'name.required' => 'name is required',
            'role.required' => "role is required",
            'role.in' => "role should be boss,developer,designer  or intern",
            'email.email'    => "the format of the email should be valid",
            'picture.file' => "the picture should be a file",
            'picture.image' => "the picture should be file of type image",
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
                if ($request->email)
                    $user->email = $request->email;

                if ($request->phone_number)
                    $user->phone_number = $request->phone_number;

                if ($request->file("picture")) {
                    //update picture
                    $user->picture = date("Y_m_d_His") . '_' . $request->file("picture")->getClientOriginalName();
                    FileService::Upload($request->file("picture"));
                }
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
        $users = User::select("id", "name", "role", "picture")->get();
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

    public function getUsersCompleteInformations()
    {
        $users = User::all();
        return response()->json($users->all(), 201);
    }

    public function fire($id)
    {
        if (auth()->user()->id == $id) {
            return response()->json("Sorry you can't fire yourself", 401);
        }
        $user = User::find($id);
        if (!$user) {
            return response()->json("Wizkid not found", 401);
        }
        if ($user->is_fired == 0) {
            $user->is_fired = 1;
            $user->save();
            Mail::to($user->email)->queue(new FireMail());
            return response()->json("Wizkid is fired", 200);
        } else {
            return response()->json("Wizkid is alredey fired", 401);
        }
    }

    public function unfire($id)
    {
        if (auth()->user()->id == $id) {
            return response()->json("Sorry you can't unfire yourself", 401);
        }
        $user = User::find($id);
        if (!$user) {
            return response()->json("Wizkid not found ", 401);
        }
        if ($user->is_fired == 1) {
            $user->is_fired = 0;
            $user->save();
            Mail::to($user->email)->queue(new unFireMail());
            return response()->json("Wizkid is back", 200);
        } else {
            return response()->json("Wizkid is not fired yet !", 401);
        }
    }
}
