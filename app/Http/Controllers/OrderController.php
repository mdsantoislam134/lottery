<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use Carbon\Carbon;

class OrderController extends Controller
{
//   public function storeOrder(Request $request)
//   {
//       $user = $request->user();
//       $mapping = [
//           '1' => 'M',
//           '2' => 'P',
//           '3' => 'T',
//           '4' => 'S',
//           '5' => 'B',
//           '6' => 'K',
//           '7' => 'W',
//           '8' => 'G',
//           '9' => 'E',
//       ];
    
//       // Split the lottery_code into lines
//       $lotteryCodeLines = explode("\n", $request->lottery_code);
    
//       // Initialize an array to store the created orders
//       $createdOrders = [];
//       $totalOrderAmount = 0; // Initialize the total order amount variable
    
//       // Process each line separately
//       foreach ($lotteryCodeLines as $line) {
//           // Skip empty lines
//           if (empty($line)) {
//               continue;
//           }
    
//           $order = new Order;
//           $order->remark = $request->remark;
//           $order->username = $user->username;
//           $order->user_id = $user->id;
//           $order->workingdate = Carbon::now()->addDay()->format('d');
    
//           // Process the current line in lottery_code
//           list($lotteryCode, $minutes) = preg_split('/#+/', $line);
//           $minutes = intval($minutes);
    
//           // Apply the specified transformation rules to lottery_code only
//           $lotteryCode = $request->lottery_code;
    
//           $convertedCode = str_replace('###', 'A', $lotteryCode);
//           $convertedCode = str_replace('##', 'S', $convertedCode);
//           $convertedCode = str_replace('#', 'B', $convertedCode);
//           $order->lotterycode = $convertedCode;
//           // Continue with the existing logic for companies field
//           $cleanedString = trim(str_replace('#', '', $request->company_names));
//           $resultString = '';
    
//           foreach (str_split($cleanedString) as $char) {
//               $mappedChar = isset($mapping[$char]) ? $mapping[$char] : $char;
//               $resultString .= $mappedChar;
//           }
    
//           $order->companies = $resultString;
//           $order->betcount = $minutes;
//           $company = strlen($request->company_names) - 1;
//           $order->totalamount = $company * $minutes;
//           $totalOrderAmount += $order->totalamount;
//           $order->order_count = $user->orders()->count() + 1;
//           $order->status = "active";
//           $user->credite_limit = $user->credite_limit - $company * $minutes;
//           $user->credit_used = $user->credit_used + $company * $minutes;
//           $user->available_credit = $user->credit_used + $user->credite_limit - $user->credit_used;
         
//       }
//       $order->save();
//       $user->save();
//       return response()->json([
//         'order' => [
//             'id' => $order->id,
//             'totalamount' => $totalOrderAmount,
//             'remark' => $order->remark,
//             'username' => $order->username,
//             'user_id' => $order->user_id,
//             'workingdate' => $order->workingdate,
//             'lotterycode' => $order->lotterycode,
//             'companies' => $order->companies,
//             'order_count' => $order->order_count,
//             'status' => $order->status,
//             'created_at'=>$order->created_at,
//         ],
//         'user' => $user,
//     ], 201);    
    
//   }
  

   

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
public function transformInput(Request $request)
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

        $output = $parts[0] . ' B';

        // Accumulate the sum for values after '#'
        for ($i = 1; $i < $count; $i++) {
            $sum += intval($parts[$i]);
            $output .= ' S' . $parts[$i];
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
        '8' => 'G',
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
