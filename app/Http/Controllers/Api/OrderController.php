<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Notification;
use App\Models\Voucher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with(['items', 'shipping', 'address'])
            ->where('user_id', $request->user()->id);

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $orders = $query->latest()->get();

        return response()->json([
            'success' => true,
            'data' => $orders,
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'address_id' => 'required|exists:addresses,id',
            'payment_method' => 'required|string',
            'shipping_cost' => 'required|numeric',
            'voucher_code' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors(),
            ], 422);
        }

        $carts = Cart::with(['product', 'variant'])
            ->where('user_id', $request->user()->id)
            ->get();

        if ($carts->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Keranjang belanja kosong',
            ], 400);
        }

        DB::beginTransaction();

        try {
            $subtotal = $carts->sum(function ($cart) {
                return $cart->product->price * $cart->quantity;
            });

            $discount = 0;
            if ($request->voucher_code) {
                $voucher = Voucher::where('code', $request->voucher_code)
                    ->where('is_active', true)
                    ->where('expired_at', '>=', now())
                    ->first();

                if ($voucher && $subtotal >= $voucher->minimum_order) {
                    if ($voucher->type === 'fixed') {
                        $discount = $voucher->value;
                    } else {
                        $discount = $subtotal * ($voucher->value / 100);
                    }
                    $voucher->increment('used');
                }
            }

            $total = $subtotal + $request->shipping_cost - $discount;

            $order = Order::create([
                'order_number' => 'ORD-' . strtoupper(Str::random(8)),
                'user_id' => $request->user()->id,
                'address_id' => $request->address_id,
                'status' => 'Diproses',
                'subtotal' => $subtotal,
                'shipping_cost' => $request->shipping_cost,
                'discount' => $discount,
                'total' => $total,
                'payment_method' => $request->payment_method,
                'voucher_code' => $request->voucher_code,
            ]);

            foreach ($carts as $cart) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $cart->product_id,
                    'product_variant_id' => $cart->product_variant_id,
                    'product_name' => $cart->product->name,
                    'product_image' => $cart->product->images->where('is_primary', true)->first()->image_url ?? null,
                    'price' => $cart->product->price,
                    'quantity' => $cart->quantity,
                    'subtotal' => $cart->product->price * $cart->quantity,
                ]);

                $cart->variant->decrement('stock', $cart->quantity);
                $cart->product->decrement('stock', $cart->quantity);
            }

            Cart::where('user_id', $request->user()->id)->delete();

            Notification::create([
                'user_id' => $request->user()->id,
                'type' => 'Transaksi',
                'title' => 'Pesanan Berhasil Dibuat',
                'message' => 'Pesanan #' . $order->order_number . ' sedang diproses.',
                'order_id' => $order->id,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pesanan berhasil dibuat',
                'data' => $order->load(['items', 'shipping', 'address']),
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function show(Request $request, $id)
    {
        $order = Order::with(['items.product', 'items.variant', 'shipping', 'address'])
            ->where('user_id', $request->user()->id)
            ->find($id);

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Pesanan tidak ditemukan',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $order,
        ]);
    }

    public function cancel(Request $request, $id)
    {
        $order = Order::where('user_id', $request->user()->id)->find($id);

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Pesanan tidak ditemukan',
            ], 404);
        }

        if ($order->status !== 'Diproses') {
            return response()->json([
                'success' => false,
                'message' => 'Pesanan tidak dapat dibatalkan',
            ], 400);
        }

        $order->update(['status' => 'Dibatalkan']);

        Notification::create([
            'user_id' => $request->user()->id,
            'type' => 'Transaksi',
            'title' => 'Pesanan Dibatalkan',
            'message' => 'Pesanan #' . $order->order_number . ' telah dibatalkan.',
            'order_id' => $order->id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Pesanan berhasil dibatalkan',
        ]);
    }
}