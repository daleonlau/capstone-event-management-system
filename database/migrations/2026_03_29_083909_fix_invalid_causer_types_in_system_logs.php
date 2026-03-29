<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Fix any invalid causer_type values
        $invalidTypes = DB::table('system_logs')
            ->whereNotNull('causer_type')
            ->whereNotIn('causer_type', ['App\Models\User', 'App\Models\OrganizationUser'])
            ->get();
        
        foreach ($invalidTypes as $log) {
            // Try to determine the correct type
            $correctType = null;
            $typeLower = strtolower($log->causer_type);
            
            if (str_contains($typeLower, 'organizationuser') || str_contains($typeLower, 'org_user')) {
                $correctType = 'App\Models\OrganizationUser';
            } elseif (str_contains($typeLower, 'user')) {
                $correctType = 'App\Models\User';
            }
            
            if ($correctType && class_exists($correctType)) {
                DB::table('system_logs')
                    ->where('id', $log->id)
                    ->update(['causer_type' => $correctType]);
            } else {
                // Set to null (system log)
                DB::table('system_logs')
                    ->where('id', $log->id)
                    ->update([
                        'causer_type' => null,
                        'causer_id' => null,
                    ]);
            }
        }
        
        // Also fix any records where causer_id doesn't exist in the related table
        // Fix users that don't exist in users table
        $userIds = DB::table('users')->pluck('id')->toArray();
        DB::table('system_logs')
            ->where('causer_type', 'App\Models\User')
            ->whereNotNull('causer_id')
            ->whereNotIn('causer_id', $userIds)
            ->update([
                'causer_type' => null,
                'causer_id' => null,
            ]);
        
        // Fix organization users that don't exist
        $orgUserIds = DB::table('organization_users')->pluck('id')->toArray();
        DB::table('system_logs')
            ->where('causer_type', 'App\Models\OrganizationUser')
            ->whereNotNull('causer_id')
            ->whereNotIn('causer_id', $orgUserIds)
            ->update([
                'causer_type' => null,
                'causer_id' => null,
            ]);
    }

    public function down(): void
    {
        // Nothing to rollback
    }
};