<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use Carbon\Carbon;

class OrderController extends Controller
{
  public function storeOrder(Request $request)
  {
      $user = $request->user();
    
      // Define the mapping array for company names
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
    
      // Split the lottery_code into lines
      $lotteryCodeLines = explode("\n", $request->lottery_code);
    
      // Initialize an array to store the created orders
      $createdOrders = [];
      $totalOrderAmount = 0; // Initialize the total order amount variable
    
      // Process each line separately
      foreach ($lotteryCodeLines as $line) {
          // Skip empty lines
          if (empty($line)) {
              continue;
          }
    
          $order = new Order;
          $order->remark = $request->remark;
          $order->username = $user->username;
          $order->user_id = $user->id;
          $order->workingdate = Carbon::now()->addDay()->format('d');
    
          // Process the current line in lottery_code
          list($lotteryCode, $minutes) = preg_split('/#+/', $line);
          $minutes = intval($minutes);
    
          // Apply the specified transformation rules to lottery_code only
          $lotteryCode = $request->lottery_code;
    
          $convertedCode = str_replace('###', 'A', $lotteryCode);
          $convertedCode = str_replace('##', 'S', $convertedCode);
          $convertedCode = str_replace('#', 'B', $convertedCode);
          $order->lotterycode = $convertedCode.$minutes;
          // Continue with the existing logic for companies field
          $cleanedString = trim(str_replace('#', '', $request->company_names));
          $resultString = '';
    
          foreach (str_split($cleanedString) as $char) {
              $mappedChar = isset($mapping[$char]) ? $mapping[$char] : $char;
              $resultString .= $mappedChar;
          }
    
          $order->companies = $resultString;
          $order->betcount = $minutes;
          $company = strlen($request->company_names) - 1;
          $order->totalamount = $company * $minutes;
          $totalOrderAmount += $order->totalamount;
          $order->order_count = $user->orders()->count() + 1;
          $order->status = "active";
          $order->save();
          $user->credite_limit = $user->credite_limit - $company * $minutes;
          $user->credit_used = $user->credit_used + $company * $minutes;
          $user->available_credit = $user->credit_used + $user->credite_limit - $user->credit_used;
          $user->save();
      }
    
      return response()->json([
        'totalamount' => $totalOrderAmount,
        'user' => [
            'username' => $user->username,
            'user_id' => $user->id,
        ],
        'remark' => $order->remark,
        'username' => $order->username,
        'user_id' => $order->user_id,
        'workingdate' => $order->workingdate,
        'lotterycode' => $order->lotterycode,
        'companies' => $order->companies,
        'order_count' => $order->order_count,
        'status' => $order->status,
        'total_order_amount' => $order->totalamount,
    ], 201);
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

 public function convertHash(Request $request)
 {
     $lotteryCode = $request->lottery_code;

     // Replace ### with A, ## with S, and # with B
     $convertedCode = str_replace('###', 'A', $lotteryCode);
     $convertedCode = str_replace('##', 'S', $convertedCode);
     $convertedCode = str_replace('#', 'B', $convertedCode);

     // Return the response with the converted code
     return response(['data' => $convertedCode]);
 }

}
