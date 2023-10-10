<?php

use Illuminate\Support\Facades\Route;
use Lunar\Hub\Http\Middleware\Authenticate;
use Lunar\StaticPages\Http\Livewire\Admin\Pages\PageCreate;
use Lunar\StaticPages\Http\Livewire\Admin\Pages\PageShow;
use Lunar\StaticPages\Http\Livewire\Admin\Pages\PagesIndex;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::group([
    'prefix' => config('lunar-hub.system.path', 'hub'),
    'middleware' => config('lunar-hub.system.middleware', ['web']),
], function () {
    Route::group([
        'middleware' => [
            Authenticate::class,
        ],
    ], function ($router) {
        Route::group([
            'prefix' => 'pages',
             'middleware' => 'can:manage-pages',
        ], function () {
            Route::get('/', PagesIndex::class)->name('hub.pages.index');
            Route::get('create', PageCreate::class)->name('hub.pages.create');
            Route::get('/{page}', PageShow::class)->name('hub.pages.show');
        });
    });
});

