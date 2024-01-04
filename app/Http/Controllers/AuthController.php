<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{


    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            
            'full_name' => 'required|string',
            'email' => 'required|email|unique:users',
            'phone_number' => 'required|unique:users',
            'password' => 'required|min:6',
            
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 405);
        }

        $user = new User;
        $user->full_name = $request->full_name;
        $user->email = $request->email;
        $user->phone_number = $request->phone_number;
        $user->balance = "0";
        $user->password = bcrypt($request->password);
        $user->profile_image = "profileimage/user.jpg";
        $user->save();

        $token = $user->createToken('MyApp')->plainTextToken;
        
        return response()->json(['user' => $user, 'token' => $token], 200);
    }        


         
// api login  $ update

public function profileupdate(Request $request,)
{
    $user = User::find($request->user()->id);

    if ($request->user()->email !== $request->email) {
        $validator = Validator::make($request->all(), [
            'email' => 'email|unique:users',
        ]);
    
        if ($validator->fails()) {
           
        }else{

            if ($request->email) {
                $user->email = $request->email;
            }
        }
   
  
    }if ($request->user()->phone_number !== $request->phone_number) {
        $validator = Validator::make($request->all(), [
            'phone_number' => 'unique:users',
        ]);
    
        if ($validator->fails()) {
           
        }else{
            if($request->phone_number){
                $user->phone_number = $request->phone_number;
            } 
        }
   
  
    }
    if($request->full_name){
        $user->full_name = $request->full_name;
    }
 

    if ($request->hasFile('profile_image')) {
        $image = $request->file('profile_image');

                $imageName = $image->getClientOriginalName(); // Get the original image name
                $image->move(public_path('profileimage'), $imageName); // Move the image to the desired directory
                $user->profile_image = "/profileimage/$imageName";
            }

    $user->save();

    
    return response()->json(['user' => $user, ], 200);
}        




    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $token = $user->createToken('MyApp')->plainTextToken;
            return response()->json(['user' => $user, 'token' => $token], 200);
        }

        return response()->json(['error' => 'Invalid User'], 402);
    }
}
