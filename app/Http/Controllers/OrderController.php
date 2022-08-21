<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exceptions\BookStoreException;
use App\Models\Address;
use App\Models\Book;
use App\Models\Cart;
use App\Models\Order;
use App\Models\User;
use App\Mail\sendOrderDetails;
use App\Mail\sendCancelledOrderDetails;
// use App\Notifications\SendCancelOrderDetails;
// use App\Notifications\SendOrderDetails;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class OrderController extends Controller
{
    public function placeOrder(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'cart_id' => 'required|integer',
                'address_id' => 'required|integer'
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors()->toJson(), 400);
            }
            $getUser = $request->user();

            $currentUser = JWTAuth::parseToken()->authenticate();
            if ($currentUser) {
                $userCheck = new User();
                $cartCheck = new Cart();
                $bookCheck = new Book();
                $addressCheck = new Address();
                $orderCheck = new Order();
                $user = $userCheck->userVerification($currentUser->id);

                if ($user) {
                    $order = Order::getOrderByCartId($request->cart_id);
                    if (!$order) {

                        $cart = $cartCheck->getCartByIdandUserId($request->cart_id, $currentUser->id);
                        if ($cart) {
                            $book = $bookCheck->findingBook($cart->book_id);
                            if ($book) {
                                if ($cart->book_quantity <= $book->quantity) {
                                    $address = $addressCheck->addressExist($request->address_id, $currentUser->id);
                                    if ($address) {
                                        $order = $orderCheck->placeOrder($request, $currentUser, $book, $cart);
                                        if ($order) {
                                            $book->quantity  -= $cart->book_quantity;
                                            $book->save();

                                            Mail::to($getUser->email)->send(new sendOrderDetails($getUser, $order, $book));
                                            return response()->json([
                                                'message' => 'Order Placed Successfully',
                                                'OrderId' => $order->order_id,
                                                'BookName'=>$book->name,
                                                'Price'=>$book->price,
                                                'Quantity' => $cart->book_quantity,
                                                'Total_Price' => $order->total_price,
                                                'Message' => 'Mail Sent to Users Mail With Order Details',
                                            ], 201);

                                            $delay = now()->addSeconds(5);
                                            $currentUser->notify((new SendOrderDetails($order, $book, $cart, $currentUser))->delay($delay));
                                            Log::info('Order Placed Successfully');
                                            Cache::remember('orders', 3600, function () {
                                                return DB::table('orders')->get();
                                            });

                                           
                                        }
                                    }
                                    Log::error('Address Not Found');
                                    throw new BookStoreException('Address Not Found', 404);
                                }
                                Log::error('Book Stock is Not Available in The Store');
                                throw new BookStoreException('Book Stock is Not Available in The Store', 406);
                            }
                        }
                        Log::error('Cart Not Found');
                        throw new BookStoreException('Cart Not Found', 404);
                    }
                    Log::error('Already Placed an Order');
                    throw new BookStoreException('Already Placed an Order', 409);
                }
                Log::error('You are Not a User');
                throw new BookStoreException('You are Not a User', 404);
            }
            Log::error('Invalid Authorization Token');
            throw new BookStoreException('Invalid Authorization Token', 401);
        } catch (BookStoreException $exception) {
            return response()->json([
                'message' => $exception->message()
            ], $exception->statusCode());
        }
    }

    public function cancelOrder(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'order_id' => 'required|string'
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors()->toJson(), 400);
            }
            $getUser = $request->user();
            $currentUser = JWTAuth::parseToken()->authenticate();
            if ($currentUser) {
                $userCheck = new User();
                $cartCheck = new Cart();
                $bookCheck = new Book();
                $orderCheck = new Order();
                $user = $userCheck->userVerification($currentUser->id);
                if ($user) {
                    if (strlen($request->order_id) == 9) {
                        $order = $orderCheck->getOrderByOrderID($request->order_id, $currentUser->id);
                        if ($order) {
                            $cart = $cartCheck->getCartByIdandUserId($order->cart_id, $currentUser->id);
                            $book = $bookCheck->findingBook($cart->book_id);
                            if ($order->delete()) {
                                $book->quantity += $cart->book_quantity;
                                $book->save();

                                Mail::to($getUser->email)->send(new sendCancelledOrderDetails($getUser, $order, $book));
                                return response()->json([
                                    'message' => 'Order Cancelled Successfully',
                                    'OrderId' => $order->order_id,
                                    'Quantity' => $cart->book_quantity,
                                    'Total_Price' => $order->total_price,
                                    'Message' => 'Mail Sent to Users Mail With Order Details'
                                ], 200);

                
                                Log::info('Order Cancelled Successfully');
                                Cache::forget('orders');

                                
                            }
                        }
                        Log::error('Order Not Found');
                        throw new BookStoreException('Order Not Found', 404);
                    }
                    Log::error('Invalid OrderID');
                    throw new BookStoreException('Invalid OrderID', 406);
                }
                Log::error('You are Not a User');
                throw new BookStoreException('You are Not a User', 404);
            }
            Log::error('Invalid Authorization Token');
            throw new BookStoreException('Invalid Authorization Token', 401);
        } catch (BookStoreException $exception) {
            return response()->json([
                'message' => $exception->message()
            ], $exception->statusCode());
        }
    }
}
