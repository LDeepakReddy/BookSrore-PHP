<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exceptions\BookStoreException;
use App\Models\Address;
use App\Models\Book;
use App\Models\Cart;
use App\Models\Order;
use App\Models\User;
use App\Notifications\SendOrderDetails;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class OrderController extends Controller
{

 

    public function placeOrder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'address_id' => 'required',
            'name' => 'required',
            'quantity' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }
        try {
            $currentUser = JWTAuth::parseToken()->authenticate();
            if ($currentUser) {
                $book = new Book();
                $cart = new Cart();
                $address = new Address();
                $bookDetails = $book->getBookDetails($request->input('name'));
                if ($bookDetails == '') {
                    Log::error('Book is not available');
                    throw new BookStoreException("We Do not have this book in the store...", 401);
                }

                if ($bookDetails['quantity'] < $request->input('quantity')) {
                    Log::error('Book stock is not available');
                    throw new BookStoreException("This much stock is unavailable for the book", 401);
                }

                $getAddress = $address->addressExist($request->input('address_id'));
                if (!$getAddress) {
                    throw new BookStoreException("This address id not available", 401);
                }

                $total_price = $request->input('quantity') * $bookDetails['Price'];

                $order = Order::create([
                    'user_id' => $currentUser->id,
                    'book_id' => $bookDetails['id'],
                    'address_id' => $getAddress['id'],
                    'order_id' => $this->gen_uid(10),

                ]);

                $userId = User::where('id', $currentUser->id)->first();

                $delay = now()->addSeconds(5);
                $userId->notify((new SendOrderDetails($order->order_id, $bookDetails['name'], $bookDetails['author'], $request->input('quantity'), $total_price))->delay($delay));

                $bookDetails['quantity'] -= $request->quantity;
                $bookDetails->save();
                return response()->json([
                    'message1' => 'Order Successfully Placed...',
                    'OrderId' => $order->order_id,
                    'Quantity' => $request->input('quantity'),
                    'Total_Price' => $total_price,
                    'message2' => 'Mail also sent to the user with all details',
                ], 201);
                Cache::remember('orders', 3600, function () {
                    return DB::table('orders')->get();
                });
            }
        } catch (BookStoreException $exception) {
            return $exception->message();
        }
    }

    function gen_uid($l)
    {
        return substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyz"), 0, $l);
    }
}