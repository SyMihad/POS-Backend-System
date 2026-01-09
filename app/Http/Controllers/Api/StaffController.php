<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreStaffRequest;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Hash;

class StaffController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $this->authorize('viewAny', User::class);

        return User::where('tenant_id', auth()->user()->tenant_id)
            ->where('role', 'staff')
            ->select('id', 'name', 'email', 'created_at')
            ->get();
    }

    public function store(StoreStaffRequest $request)
    {
        $this->authorize('create', User::class);

        $staff = User::create([
            'tenant_id' => auth()->user()->tenant_id,
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'staff',
        ]);

        return response()->json([
            'message' => 'Staff created successfully',
            'staff_id' => $staff->id,
        ], 201);
    }
}
