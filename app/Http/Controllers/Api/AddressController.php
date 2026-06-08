<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AddressController extends Controller
{
    public function index(Request $request)
    {
        $addresses = Address::where('user_id', $request->user()->id)->get();

        return response()->json([
            'success' => true,
            'data' => $addresses,
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'recipient_name' => 'required|string|max:255',
            'phone' => 'required|string',
            'full_address' => 'required|string',
            'postal_code' => 'required|string',
            'label' => 'required|in:Rumah,Kantor,Lainnya',
            'is_default' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors(),
            ], 422);
        }

        if ($request->is_default) {
            Address::where('user_id', $request->user()->id)
                ->update(['is_default' => false]);
        }

        $address = Address::create([
            'user_id' => $request->user()->id,
            'recipient_name' => $request->recipient_name,
            'phone' => $request->phone,
            'full_address' => $request->full_address,
            'postal_code' => $request->postal_code,
            'label' => $request->label,
            'is_default' => $request->is_default ?? false,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Alamat berhasil ditambahkan',
            'data' => $address,
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $address = Address::where('user_id', $request->user()->id)->find($id);

        if (!$address) {
            return response()->json([
                'success' => false,
                'message' => 'Alamat tidak ditemukan',
            ], 404);
        }

        if ($request->is_default) {
            Address::where('user_id', $request->user()->id)
                ->update(['is_default' => false]);
        }

        $address->update([
            'recipient_name' => $request->recipient_name ?? $address->recipient_name,
            'phone' => $request->phone ?? $address->phone,
            'full_address' => $request->full_address ?? $address->full_address,
            'postal_code' => $request->postal_code ?? $address->postal_code,
            'label' => $request->label ?? $address->label,
            'is_default' => $request->is_default ?? $address->is_default,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Alamat berhasil diupdate',
            'data' => $address,
        ]);
    }

    public function destroy(Request $request, $id)
    {
        $address = Address::where('user_id', $request->user()->id)->find($id);

        if (!$address) {
            return response()->json([
                'success' => false,
                'message' => 'Alamat tidak ditemukan',
            ], 404);
        }

        $address->delete();

        return response()->json([
            'success' => true,
            'message' => 'Alamat berhasil dihapus',
        ]);
    }
}