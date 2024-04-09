<?php

namespace App\Providers;

use App\Models\Permission;
use App\User;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        return $this->checkPermission();
    }

    private function checkPermission()
    {
        if (!$permissions = $this->getAllPermissions()) {
            return true;
        }
        
        foreach ($permissions as $permission) {
            Gate::define($permission->name, function() use ($permission) {
                return User::getInstance()->checkPermission($permission);
            });
        }

        return false;
    }

    private function getAllPermissions()
    {
        return Permission::getInstance()->getAll();
    }
}
