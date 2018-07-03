<?php

namespace App\Providers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Laravel\Dusk\DuskServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(
          \App\Repositories\User\UserRepositoryInterface::class,
          \App\Repositories\User\UserRepository::class
        );

        $this->app->singleton(
            \App\Repositories\Password\PasswordRepositoryInterface::class,
            \App\Repositories\Password\PasswordRepository::class
        );

        $this->app->singleton(
        	\App\Repositories\WorkTime\WorkTimeRepositoryInterface::class,
        	\App\Repositories\WorkTime\WorkTimeRepository::class
    	);
        
        $this->app->singleton(
            \App\Repositories\Report\ReportRepositoryInterface::class,
            \App\Repositories\Report\ReportRepository::class
        );
        
        $this->app->singleton(
            \App\Repositories\ProjectMember\ProjectMemberRepositoryInterface::class,
            \App\Repositories\ProjectMember\ProjectMemberRepository::class
        );

        $this->app->singleton(
            \App\Repositories\ProjectManager\ProjectManagerRepositoryInterface::class,
            \App\Repositories\ProjectManager\ProjectManagerRepository::class
        );

        // tags
        $this->app->singleton(
            \App\Repositories\Tag\TagRepositoryInterface::class,
            \App\Repositories\Tag\TagRepository::class
        );
        
        $this->app->singleton(
            \App\Repositories\TagProjectUser\TagWorktimeRepositoryInterface::class,
            \App\Repositories\TagProjectUser\TagWorktimeRepository::class
        );
        
        // client
        $this->app->singleton(
            \App\Repositories\Client\ClientRepositoryInterface::class,
            \App\Repositories\Client\ClientRepository::class
        );
        
        // division
        $this->app->singleton(
            \App\Repositories\Division\DivisionRepositoryInterface::class,
            \App\Repositories\Division\DivisionRepository::class
        );
        
        // role
        $this->app->singleton(
            \App\Repositories\Role\RoleRepositoryInterface::class,
            \App\Repositories\Role\RoleRepository::class
        );
        
        $this->app->singleton(
            \App\Repositories\RoleUser\RoleUserRepositoryInterface::class,
            \App\Repositories\RoleUser\RoleUserRepository::class
        );
        // assign project
        $this->app->singleton(
            \App\Repositories\AssignProject\AssignProjectRepositoryInterface::class,
            \App\Repositories\AssignProject\AssignProjectRepository::class
        );

        if ($this->app->environment('local', 'testing')) {
            $this->app->register(DuskServiceProvider::class);
        }
    }
}
