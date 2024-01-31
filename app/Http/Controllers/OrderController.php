<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use Carbon\Carbon;

class OrderController extends Controller
{

  public function getorders(Request $request)
  {
      $user = $request->user();
  
      // Get orders for the authenticated user
      $orders = $user->orders;
  
      // Sort the orders by order ID in descending order
      $sortedOrders = $orders->sortByDesc('id')->values();
  
      // Return the sorted orders as a JSON response
      return response()->json(['order' => $sortedOrders], 201);
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

return response()->json(['order' => $order,'user'=>$user], 200);
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
 public function getTotalAcceptedRejected(Request $request)
 {
     // Get the authenticated user
     $user = $request->user();
 
     // Get the sum of totalamount where status is 'accept' and user_id is the authenticated user's ID
     $acceptedTotal = Order::where('status', 'active')->where('user_id', $user->id)->sum('totalamount');
 
     // Get the sum of totalamount where status is 'cancel' and user_id is the authenticated user's ID
     $rejectedTotal = Order::where('status', 'cancel')->where('user_id', $user->id)->sum('totalamount');
 
     // Return the totals as a JSON response
     return response()->json([
         'accepted_total' => $acceptedTotal,
         'rejected_total' => $rejectedTotal,
     ]);
 }

//transform
public function makeOrder(Request $request)
{
   
    $input = $request->input('data');
    $lines = explode("\n", $input);
    $output = '';
    $totalSum = 0;
    $hashLineLength = 0;

    foreach ($lines as $line) {
        $result = $this->transformDynamicInput($line);
        $output .= $result['output'] . "\n";
        $totalSum += $result['sum'];
        if (strpos($line, '#') === 0) {
            $hashLineLength = strlen($line) - 1; // Exclude the '#' character
        }
    }

    // Remove the last newline character
    $output = rtrim($output, "\n");


    $user = $request->user();

          $order = new Order;
          $order->remark = $request->remark;
          $order->username = $user->username;
          $order->user_id = $user->id;
          $order->workingdate = Carbon::now()->addDay()->format('d');
          $order->lotterycode = $output;
          $order->betcount = $totalSum;
          $order->totalamount = $totalSum * $hashLineLength;
          $order->order_count = $user->orders()->count() + 1;
          $order->status = "active";
        
   
          if ($user->credite_limit < $order->totalamount) {
            return response()->json(['error' => 'Insufficient credit limit. Order rejected.'], 400);
        }
       $user->credite_limit = $user->credite_limit - $order->totalamount;
       $user->credit_used = $user->credit_used + $order->totalamount;
       $user->available_credit = $user->credit_used + $user->credite_limit - $user->credit_used;
       $order->save();
       $user->save();

      return response()->json([
        'order' => [
            'id' => $order->id,
            'totalamount' =>  $order->totalamount,
            'remark' => $order->remark,
            'username' => $order->username,
            'user_id' => $order->user_id,
            'workingdate' => $order->workingdate,
            'lotterycode' => $order->lotterycode,
        
            'order_count' => $order->order_count,
            'status' => $order->status,
            'created_at'=>$order->created_at,
        ],
        'user' => $user,
    ], 201);  

    return response()->json(['result' => $output, 'totalSum' => $totalSum * $hashLineLength, 'hashLineLength' => $hashLineLength]);
}

private function transformDynamicInput($input)
{
    // Initialize the sum variable
    $sum = 0;

    // Check for patterns and transform accordingly
    if (strpos($input, '**') === 0) {
        // Pattern: **1435#1
          // Sum calculation for lines starting with '**'
          $value = strtok(substr($input, 1), '#');
    
        $output = '[' . strtok(substr($input, 2), '#') . '] B1 [iBox]';
      
        // Sum calculation for lines starting with '*'
        $sum = 1 * intval(substr($input, strlen($value) + 2));
    } elseif (strpos($input, '*') === 0) {
        // Pattern: *1435#1
        $value = strtok(substr($input, 1), '#');
        $output = '(' . $value . ') B1 [x24]';
        // Sum calculation for lines starting with '*'
        $sum = 24 * intval(substr($input, strlen($value) + 2));
    } 
    
    elseif (strpos($input, '#') === 0) {
        // Pattern: #1435
        $wordWithoutHash = substr($input, 1);
        $output = $this->transformWordWithRules($wordWithoutHash);
      
    }
    else {
        $parts = explode('#', $input);
        $count = count($parts);

        switch ($count) {
            case 2:
                // Pattern: 1435#1
                $output = $parts[0] . ' B' . $parts[1];
                $sum += intval($parts[1]);
                break;
            case 3:
                // Pattern: 1435#1#1
                $output = $parts[0] . ' B' . $parts[1] . ' S' . $parts[2];
                $sum =$sum +intval($parts[1])+ intval($parts[2]);
                break;
            case 4:
                // Pattern: 1435#1#1#1
                $output = $parts[0] . ' B' . $parts[1] . ' S' . $parts[2] . ' A' . $parts[3];
                $sum =$sum +intval($parts[1])+ intval($parts[2])+ intval($parts[3]);
                break;
            default:
                // Default pattern
                $output = $input . ' H';
                break;
        }
    }

    return ['output' => $output, 'sum' => $sum];
}

private function transformWordWithRules($word)
{
    // Transformation rules for words starting with '#'
    $rules = [
        '1' => 'M',
        '2' => 'P',
        '3' => 'T',
        '4' => 'S',
        '5' => 'B',
        '6' => 'K',
        '7' => 'W',
        '8' => 'H',
        '9' => 'E',
    ];

    $transformedWord = '';

    // Transform each character in the word based on the rules
    for ($i = 0; $i < strlen($word); $i++) {
        $character = $word[$i];

        if (isset($rules[$character])) {
            $transformedWord .= $rules[$character];
        } else {
            // If the character is not in the rules, keep it unchanged
            $transformedWord .= $character;
        }
    }



    return $transformedWord;
} 
 
}
