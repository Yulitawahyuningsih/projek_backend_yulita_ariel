<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Voucher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class VoucherController extends Controller
{
    public function index()
    {
        $vouchers = Voucher::where('is_active', true)
            ->where('expired_at', '>=', now())
            ->get();

        return response()->json([
            'success' => true,
            'data' => $vouchers,
        ]);
    }

    public function check(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|string',
            'subtotal' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors(),
            ], 422);
        }

        $voucher = Voucher::where('code', $request->code)
            ->where('is_active', true)
            ->where('expired_at', '>=', now())
            ->first();

        if (!$voucher) {
            return response()->json([
                'success' => false,
                'message' => 'Voucher tidak valid atau sudah kadaluarsa',
            ], 404);
        }

        if ($voucher->used >= $voucher->quota) {
            return response()->json([
                'success' => false,
                'message' => 'Voucher sudah habis digunakan',
            ], 400);
        }

        if ($request->subtotal < $voucher->minimum_order) {
            return response()->json([
                'success' => false,
                'message' => 'Minimum pembelian tidak terpenuhi',
            ], 400);
        }

        $discount = 0;
        if ($voucher->type === 'fixed') {
            $discount = $voucher->value;
        } else {
            $discount = $request->subtotal * ($voucher->value / 100);
        }

        return response()->json([
            'success' => true,
            'message' => 'Voucher valid',
            'data' => $voucher,
            'discount' => $discount,
        ]);
    }
}