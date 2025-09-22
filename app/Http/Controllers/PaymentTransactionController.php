<?php

namespace App\Http\Controllers;

use App\Models\PaymentTransaction;
use Illuminate\Http\Request;

class PaymentTransactionController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $query = PaymentTransaction::query()->with(['user', 'course', 'subscription']);

        if (!$user->hasRole('admin')) {
            $query->where('user_id', $user->id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $transactions = $query->orderBy('created_at', 'desc')->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $transactions,
            'meta' => [
                'message' => 'Transacciones obtenidas exitosamente'
            ]
        ]);
    }

    public function show(Request $request, $id)
    {
        $transaction = PaymentTransaction::with(['user', 'course', 'subscription'])->findOrFail($id);
        $user = $request->user();

        if (!$user->hasRole('admin') && $transaction->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'data' => null,
                'meta' => [
                    'message' => 'No tienes permisos para ver esta transacción'
                ]
            ], 403);
        }

        return response()->json([
            'success' => true,
            'data' => $transaction,
            'meta' => [
                'message' => 'Transacción obtenida exitosamente'
            ]
        ]);
    }
}