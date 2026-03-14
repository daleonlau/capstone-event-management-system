<?php

namespace App\Services;

use App\Models\User;
use App\Models\OrganizationUser;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class OrganizationUserCreationService
{
    public function createUsers($organizationId, $data)
    {
        DB::transaction(function () use ($organizationId, $data) {
            OrganizationUser::create([
                'organization_id' => $organizationId,
                'name' => $data['president_name'],
                'email' => $data['president_email'],
                'password' => Hash::make($data['president_password']),
                'role' => 'president',
            ]);

            OrganizationUser::create([
                'organization_id' => $organizationId,
                'name' => $data['adviser_name'],
                'email' => $data['adviser_email'],
                'password' => Hash::make($data['adviser_password']),
                'role' => 'adviser',
            ]);

            OrganizationUser::create([
                'organization_id' => $organizationId,
                'name' => $data['treasurer_name'],
                'email' => $data['treasurer_email'],
                'password' => Hash::make($data['treasurer_password']),
                'role' => 'treasurer',
            ]);
        });
    }
}