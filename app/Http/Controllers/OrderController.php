<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use Carbon\Carbon;

class OrderController extends Controller
{
    public function storeOrder(Request $request){

       $user = $request->user();
        $order = New Order;
        $order->remark = $request->remark;
        $order->username = $user->username;
        $order->user_id = $user->id;
        $order->workingdate =  Carbon::now()->addDay()->format('d');
        $mapping = [
            '1' => 'M',
            '2' => 'P',
            '3' => 'T',
            '4' => 'P',
            '5' => 'B',
            '6' => 'K',
            '7' => 'W',
            '8' => 'O',
            '9' => 'E',
        ];
        
        // Remove spaces and # character
        $cleanedString = trim(str_replace('#', '', $request->company_names));
        
        // Initialize the result string
        $resultString = '';
        
        // Loop through each character and map it
        for ($i = 0; $i < strlen($cleanedString); $i++) {
            $char = $cleanedString[$i];
        
            // Append the mapped value to the result string
            $resultString .= isset($mapping[$char]) ? $mapping[$char] : $char;
        }
        
        $order->companies = $resultString;
        $order->lotterycode = str_replace('#', ' B', $request->lottery_code);
        list($lotteryCode, $minutes) = explode('#', $request->lottery_code);
        $minutes = intval($minutes);
        $order->betcount =  $minutes;
        $company =  strlen($request->company_names) - 1;
        $order->totalamount = $company * $minutes;
        $order->order_count = $user->orders()->count() + 1;
        $order->status = "active";

      $order->save();
   
      $user->credite_limit = $user->credite_limit - $company * $minutes; 
      $user->credit_used = $user->credit_used + $company * $minutes; 
      $user->available_credit = $user->credit_used +  $user->credite_limit - $user->credit_used; 

$user->save();


        return response()->json(['order' => $order,$user], 201);
    }


 public function getorders(Request $request){

   $user = $request->user();
   $orders = $user->orders;
   
   return response()->json(['order' => $orders], 201);

 }


 public function cancelorders(Request $request,$id)
 {
    $order = Order::find($id);
    $order->status = "cancel";
    $order->save();

    $user = $request->user();
    $user->credite_limit = $user->credite_limit + $order->totalamount; 
      $user->credit_used = $user->credit_used - $order->totalamount; 
      $user->available_credit = $user->credit_used +  $user->credite_limit - $user->credit_used; 
$user->save();

return response()->json(['order' => $order,$user], 201);
 }



}
