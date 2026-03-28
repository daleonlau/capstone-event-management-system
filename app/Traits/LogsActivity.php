<?php

namespace App\Traits;

use App\Services\LogService;
use Illuminate\Support\Facades\Request;

trait LogsActivity
{
    /**
     * Log an action with the authenticated user
     */
    protected function logAction($action, $description, $details = [])
    {
        $user = $this->getCurrentUser();
        
        if ($user) {
            return LogService::action($action, $description, $user, $details);
        }
        
        // If no user, log as system action
        return LogService::system($action, $description, $details);
    }

    /**
     * Log an authentication event
     */
    protected function logAuth($action, $description, $user, $details = [])
    {
        return LogService::auth($action, $description, $user, $details);
    }
    
    /**
     * Log a security event (failed attempts, suspicious activity)
     */
    protected function logSecurity($action, $description, $details = [])
    {
        $user = $this->getCurrentUser();
        return LogService::security($action, $description, $user, $details);
    }
    
    /**
     * Log a system event
     */
    protected function logSystem($action, $description, $details = [])
    {
        return LogService::system($action, $description, $details);
    }
    
    /**
     * Log an error event
     */
    protected function logError($action, $description, $exception = null, $details = [])
    {
        return LogService::error($action, $description, $exception, $details);
    }
    
    /**
     * Get the current authenticated user from any guard
     */
    protected function getCurrentUser()
    {
        // Check web guard (admin users)
        $user = auth()->user();
        
        if ($user) {
            return $user;
        }
        
        // Check org_user guard (president, adviser, treasurer)
        $orgUser = auth()->guard('org_user')->user();
        
        if ($orgUser) {
            return $orgUser;
        }
        
        return null;
    }
    
    /**
     * Log model changes (create, update, delete)
     */
    protected function logModelChange($model, $action, $details = [])
    {
        $user = $this->getCurrentUser();
        $modelName = class_basename($model);
        
        $logDetails = array_merge($details, [
            'model_id' => $model->id,
            'model_type' => $modelName,
        ]);
        
        // For updates, include what changed
        if ($action === 'updated' && method_exists($model, 'getDirty')) {
            $logDetails['changes'] = $model->getDirty();
            $logDetails['original'] = $model->getOriginal();
        }
        
        return LogService::action(
            $action . '_' . strtolower($modelName),
            ucfirst($action) . ' ' . $modelName . ': ' . ($model->name ?? $model->title ?? $model->id),
            $user,
            $logDetails
        );
    }
}