<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use TempestTools\Scribe\Laravel\Events\Controller\Init;
use TempestTools\Scribe\Laravel\Events\Controller\PreIndex;
use TempestTools\Scribe\Laravel\Events\Controller\PostIndex;
use TempestTools\Scribe\Laravel\Events\Controller\PreStore;
use TempestTools\Scribe\Laravel\Events\Controller\PostStore;
use TempestTools\Scribe\Laravel\Events\Controller\PreShow;
use TempestTools\Scribe\Laravel\Events\Controller\PostShow;
use TempestTools\Scribe\Laravel\Events\Controller\PreUpdate;
use TempestTools\Scribe\Laravel\Events\Controller\PostUpdate;
use TempestTools\Scribe\Laravel\Events\Controller\PreDestroy;
use TempestTools\Scribe\Laravel\Events\Controller\PostDestroy;

class EventServiceProvider extends ServiceProvider
{

    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected /** @noinspection ClassOverridesFieldOfSuperClassInspection */ $listen = [
    	Init::class =>[],
        PreIndex::class =>[],
        PostIndex::class =>[],
        PreStore::class =>[],
        PostStore::class =>[],
        PreShow::class =>[],
        PostShow::class =>[],
        PreUpdate::class =>[],
        PostUpdate::class =>[],
        PreDestroy::class =>[],
        PostDestroy::class =>[],
    ];

    /**
     * Register any other events for your application.
     *
     * @return void
     */
    public function boot():void
    {
        parent::boot();

        //
    }
}
