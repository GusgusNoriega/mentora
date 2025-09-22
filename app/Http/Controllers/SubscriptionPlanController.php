<?php

namespace App\Http\Controllers;

use App\Models\SubscriptionPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SubscriptionPlanController extends Controller
{
    public function index(Request $request)
    {
        $query = SubscriptionPlan::query()->with(['courses']);

        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        $plans = $query->orderBy('created_at', 'desc')->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $plans,
            'meta' => [
                'message' => 'Planes de suscripción obtenidos exitosamente'
            ]
        ]);
    }

    public function show($id)
    {
        $plan = SubscriptionPlan::with(['courses', 'userSubscriptions'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $plan,
            'meta' => [
                'message' => 'Plan obtenido exitosamente'
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
                    'message' => 'No tienes permisos para crear planes'
                ]
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price_cents' => 'required|integer|min:0',
            'currency' => 'required|string|max:3',
            'interval' => 'required|in:month,year',
            'trial_days' => 'nullable|integer|min:0',
            'access_all_courses' => 'boolean',
            'is_active' => 'boolean',
            'course_ids' => 'nullable|array',
            'course_ids.*' => 'exists:courses,id',
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

        $plan = SubscriptionPlan::create($request->except('course_ids'));

        if ($request->has('course_ids')) {
            $plan->courses()->attach($request->course_ids);
        }

        return response()->json([
            'success' => true,
            'data' => $plan->load('courses'),
            'meta' => [
                'message' => 'Plan creado exitosamente'
            ]
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $plan = SubscriptionPlan::findOrFail($id);
        $user = $request->user();

        if (!$user->hasRole('admin')) {
            return response()->json([
                'success' => false,
                'data' => null,
                'meta' => [
                    'message' => 'No tienes permisos para actualizar planes'
                ]
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'price_cents' => 'sometimes|required|integer|min:0',
            'currency' => 'sometimes|required|string|max:3',
            'interval' => 'sometimes|required|in:month,year',
            'trial_days' => 'nullable|integer|min:0',
            'access_all_courses' => 'boolean',
            'is_active' => 'boolean',
            'course_ids' => 'nullable|array',
            'course_ids.*' => 'exists:courses,id',
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

        $plan->update($request->except('course_ids'));

        if ($request->has('course_ids')) {
            $plan->courses()->sync($request->course_ids);
        }

        return response()->json([
            'success' => true,
            'data' => $plan->fresh('courses'),
            'meta' => [
                'message' => 'Plan actualizado exitosamente'
            ]
        ]);
    }

    public function destroy(Request $request, $id)
    {
        $plan = SubscriptionPlan::findOrFail($id);
        $user = $request->user();

        if (!$user->hasRole('admin')) {
            return response()->json([
                'success' => false,
                'data' => null,
                'meta' => [
                    'message' => 'No tienes permisos para eliminar planes'
                ]
            ], 403);
        }

        $plan->delete();

        return response()->json([
            'success' => true,
            'data' => null,
            'meta' => [
                'message' => 'Plan eliminado exitosamente'
            ]
        ]);
    }
}