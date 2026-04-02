<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Stancl\Tenancy\Database\Models\Domain;

class YayasanController extends Controller
{
    /**
     * List all Yayasans.
     */
    public function index()
    {
        if (! in_array(request()->getHost(), config('tenancy.central_domains', []))) {
            abort(404);
        }

        $yayasans = Tenant::with('domains')->get();

        return view('admin.yayasans.index', compact('yayasans'));
    }

    /**
     * Create a new Yayasan.
     */
    public function store(Request $request)
    {
        if (! in_array($request->getHost(), config('tenancy.central_domains', []))) {
            abort(404);
        }

        $request->validate([
            'name' => 'required|string|max:150',
            'domain' => 'required|string|unique:domains,domain',
            'email' => 'required|email',
        ]);

        // 1. Create Tenant
        $tenant = Tenant::create([
            'id' => str($request->name)->slug()->toString(),
            'name' => $request->name,
        ]);

        // 2. Create Domain
        Domain::create([
            'domain' => $request->domain,
            'tenant_id' => $tenant->id,
        ]);

        // 3. Provision SuperAdmin inside Tenant
        $tenant->run(function () use ($request) {
            $user = User::updateOrCreate(
                ['email' => $request->email],
                [
                    'name' => "Admin {$request->name}",
                    'password' => Hash::make('password'),
                    'uuid' => (string) Str::uuid(),
                ]
            );

            $user->assignRole('superadmin');
        });

        return redirect()->route('admin.yayasans.index')->with('success', "Yayasan {$request->name} berhasil dibuat!");
    }
}
