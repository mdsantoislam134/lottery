<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class WebController extends Controller
{

    public function home() {
        // Check if the user is logged in using Laravel's Auth::check()
        if (Auth::check()) {
            // User is logged in, show home page content
            return view('Admin.dashboard');
            // You can replace 'home' with the actual view or template you want to display
        } else {
            // User is not logged in, redirect to the login page
            return view('Admin.login');
            // You can replace 'login' with the actual route name or URL of your login page
        }
    }
    
    public function logout(){
        Auth::logout();
 
         return redirect('/');
    }



    public function users(){
       $user = User::all();
        return view('Admin.users',['users' => $user ]);
       }

   public function create_user(){
    return view('Admin.createuser');
   }

   public function createUser(Request $request)
   {
    
     
     
           // Create a new user
           $user = new User([
               'username' => $request['username'],
               'username_owner_name' => $request['username_owner_name'],
               'roll' => $request['roll'],
               'credite_limit' => $request['credite_limit'],
               'email' => $request['email'],
               'credit_used' => "0",
               'available_credit' => "0",
               'cash_balance' => $request['cash_balance'],
               'outtanding_transaction' => $request['outtanding_transaction'],
               'status' => $request['status'],
               'password' => Hash::make($request['password']),
           ]);

          
           $user->save();

           return redirect('admin/users')->with('massage',"User update Successfully");
      
   }
        



   public function edit($id){
     $user = User::find($id);
     return view('Admin.edituser',['user'=>$user]);
   }


   public function edituser(Request $request,$id){
        
    $user = User::find($id);
    $user->email = $request->email;
           
           $user->update([
            'username' => $request['username'],
            'username_owner_name' => $request['username_owner_name'],
            'roll' => $request['roll'],
            'credite_limit' => $request['credite_limit'],
            'credit_used' => "0",
            'available_credit' => "0",
            'cash_balance' => $request['cash_balance'],
            'outtanding_transaction' => $request['outtanding_transaction'],
            'status' => $request['status'],
            'password' => Hash::make($request['password']),
        ]);

       
        $user->save();

        return redirect('admin/users')->with('massage',"User update Successfully");
   }

}
