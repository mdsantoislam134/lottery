<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{


    public function store(Request $request)
    {
        // Validate the incoming request data
        $validatedData = $request->validate([
            'username' => 'required|string',
            'username_owner_name' => 'required|string',
            'roll' => 'required|string',
            'credite_limit' => 'required|string',
            'credit_used' => 'required|string',
            'available_credit' => 'required|string',
            'cash_balance' => 'required|string',
            'outtanding_transaction' => 'required|string',
            'status' => 'required|string',
            'password' => 'required|string',
        ]);
    
        // Create a new user instance
        $user = User::create($validatedData);
    
        // Optionally, you might want to perform additional actions here
    
        // Return a response, redirect, or perform any other necessary actions
        return response()->json(['message' => 'User created successfully', 'user' => $user], 201);
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


    public function specificUserData(Request $request,$id)
    {
    
            $user = User::find($id);
       
            return response()->json(['user' => $user], 200);
     

        return response()->json(['error' => 'Invalid User'], 402);
    }



    public function Adminlogin(Request $request)
    { 
        $credentials = $request->only('email', 'password');
    
        if (Auth::attempt($credentials)) {
            $user = $request->user();
    
            if ($user->status == "Admin") {
              
                return redirect('dashboard');
            }
            Auth::logout();
            return redirect()->back()->with('error', "Invalid User");
          
        }
    
        return redirect()->back()->with('error', "Invalid User");
    }



}
