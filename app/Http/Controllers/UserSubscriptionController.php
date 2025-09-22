<?php

namespace App\Http\Controllers;

use App\Models\UserSubscription;
use Illuminate\Http\Request;

class UserSubscriptionController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $query = UserSubscription::query()->with(['user', 'plan']);

        if (!$user->hasRole('admin')) {
            $query->where('user_id', $user->id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $subscriptions = $query->orderBy('created_at', 'desc')->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $subscriptions,
            'meta' => [
                'message' => 'Suscripciones obtenidas exitosamente'
            ]
        ]);
    }

    public function show(Request $request, $id)
    {
        $subscription = UserSubscription::with(['user', 'plan', 'paymentTransactions'])->findOrFail($id);
        $user = $request->user();

        if (!$user->hasRole('admin') && $subscription->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'data' => null,
                'meta' => [
                    'message' => 'No tienes permisos para ver esta suscripción'
                ]
            ], 403);
        }

        return response()->json([
            'success' => true,
            'data' => $subscription,
            'meta' => [
                'message' => 'Suscripción obtenida exitosamente'
            ]
        ]);
    }

    public function store(Request $request)
    {
        // Lógica para crear suscripción, probablemente integrada con pagos
        // Por simplicidad, solo admin puede crear
        $user = $request->user();
        if (!$user->hasRole('admin')) {
            return response()->json([
                'success' => false,
                'data' => null,
                'meta' => [
                    'message' => 'No tienes permisos para crear suscripciones'
                ]
            ], 403);
        }

        // Implementar validación y creación
        // ...
    }

    public function update(Request $request, $id)
    {
        $subscription = UserSubscription::findOrFail($id);
        $user = $request->user();

        if (!$user->hasRole('admin') && $subscription->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'data' => null,
                'meta' => [
                    'message' => 'No tienes permisos para actualizar esta suscripción'
                ]
            ], 403);
        }

        // Lógica para cancelar, etc.
        // ...
    }

    public function destroy(Request $request, $id)
    {
        $subscription = UserSubscription::findOrFail($id);
        $user = $request->user();

        if (!$user->hasRole('admin')) {
            return response()->json([
                'success' => false,
                'data' => null,
                'meta' => [
                    'message' => 'Solo administradores pueden eliminar suscripciones'
                ]
            ], 403);
        }

        $subscription->delete();

        return response()->json([
            'success' => true,
            'data' => null,
            'meta' => [
                'message' => 'Suscripción eliminada exitosamente'
            ]
        ]);
    }
}