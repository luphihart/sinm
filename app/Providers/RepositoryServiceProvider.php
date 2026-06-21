<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(
            \App\Repositories\Contracts\JurusanRepositoryInterface::class,
            \App\Repositories\Eloquent\JurusanRepository::class
        );
        $this->app->bind(
            \App\Repositories\Contracts\KelasRepositoryInterface::class,
            \App\Repositories\Eloquent\KelasRepository::class
        );
        $this->app->bind(
            \App\Repositories\Contracts\MuridRepositoryInterface::class,
            \App\Repositories\Eloquent\MuridRepository::class
        );
        $this->app->bind(
            \App\Repositories\Contracts\MataPelajaranRepositoryInterface::class,
            \App\Repositories\Eloquent\MataPelajaranRepository::class
        );
        $this->app->bind(
            \App\Repositories\Contracts\SemesterRepositoryInterface::class,
            \App\Repositories\Eloquent\SemesterRepository::class
        );
        $this->app->bind(
            \App\Repositories\Contracts\NilaiRepositoryInterface::class,
            \App\Repositories\Eloquent\NilaiRepository::class
        );
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
