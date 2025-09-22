<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CouponController extends Controller
{
    public function index(Request $request)
    {
        $query = Coupon::query();

        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        $coupons = $query->orderBy('created_at', 'desc')->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $coupons,
            'meta' => [
                'message' => 'Cupones obtenidos exitosamente'
            ]
        ]);
    }

    public function show($id)
    {
        $coupon = Coupon::with(['redemptions'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $coupon,
            'meta' => [
                'message' => 'Cupón obtenido exitosamente'
            ]
        ]);
    }

    public function store(Request $request)
    {
        $user = $request->user();
        if (!$user->hasRole('admin')) {
            return response()->json([
                'success' => false,
                'data' => null,
                'meta' => [
                    'message' => 'No tienes permisos para crear cupones'
                ]
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'code' => 'required|string|unique:coupons',
            'type' => 'required|in:fixed,percentage',
            'amount' => 'required|integer|min:1',
            'max_redemptions' => 'nullable|integer|min:1',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date|after:starts_at',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'data' => null,
                'meta' => [
                    'message' => 'Datos de entrada inválidos',
                    'errors' => $validator->errors()
                ]
            ], 422);
        }

        $coupon = Coupon::create($request->all());

        return response()->json([
            'success' => true,
            'data' => $coupon,
            'meta' => [
                'message' => 'Cupón creado exitosamente'
            ]
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $coupon = Coupon::findOrFail($id);
        $user = $request->user();

        if (!$user->hasRole('admin')) {
            return response()->json([
                'success' => false,
                'data' => null,
                'meta' => [
                    'message' => 'No tienes permisos para actualizar cupones'
                ]
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'code' => 'sometimes|string|unique:coupons,code,' . $coupon->id,
            'type' => 'sometimes|in:fixed,percentage',
            'amount' => 'sometimes|integer|min:1',
            'max_redemptions' => 'nullable|integer|min:1',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date|after:starts_at',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'data' => null,
                'meta' => [
                    'message' => 'Datos de entrada inválidos',
                    'errors' => $validator->errors()
                ]
            ], 422);
        }

        $coupon->update($request->all());

        return response()->json([
            'success' => true,
            'data' => $coupon->fresh(),
            'meta' => [
                'message' => 'Cupón actualizado exitosamente'
            ]
        ]);
    }

    public function destroy(Request $request, $id)
    {
        $coupon = Coupon::findOrFail($id);
        $user = $request->user();

        if (!$user->hasRole('admin')) {
            return response()->json([
                'success' => false,
                'data' => null,
                'meta' => [
                    'message' => 'No tienes permisos para eliminar cupones'
                ]
            ], 403);
        }

        $coupon->delete();

        return response()->json([
            'success' => true,
            'data' => null,
            'meta' => [
                'message' => 'Cupón eliminado exitosamente'
            ]
        ]);
    }
}