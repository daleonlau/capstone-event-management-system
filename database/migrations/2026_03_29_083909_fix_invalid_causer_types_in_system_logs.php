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
            ->whereRaw('causer_type NOT LIKE "%User%"')
            ->get();
        
        foreach ($invalidTypes as $log) {
            // Try to determine the correct class
            $correctType = null;
            
            if (stripos($log->causer_type, 'OrganizationUser') !== false) {
                $correctType = 'App\Models\OrganizationUser';
            } elseif (stripos($log->causer_type, 'User') !== false) {
                $correctType = 'App\Models\User';
            }
            
            if ($correctType && class_exists($correctType)) {
                DB::table('system_logs')
                    ->where('id', $log->id)
                    ->update(['causer_type' => $correctType]);
            } else {
                // If we can't fix it, set to null (system log)
                DB::table('system_logs')
                    ->where('id', $log->id)
                    ->update([
                        'causer_type' => null,
                        'causer_id' => null,
                        'log_type' => 'system',
                    ]);
            }
        }
    }

    public function down(): void
    {
        // Nothing to rollback
    }
};