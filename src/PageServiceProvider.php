<?php

namespace Lunar\StaticPages;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use Lunar\Base\AttributeManifestInterface;
use Lunar\Hub\Auth\Manifest;
use Lunar\Hub\Auth\Permission;
use Lunar\StaticPages\Http\Livewire\Admin\Pages\PageCreate;
use Lunar\StaticPages\Http\Livewire\Admin\Pages\PagesIndex;
use Lunar\StaticPages\Http\Livewire\Components\ActivityLogFeed;
use Lunar\StaticPages\Http\Livewire\Components\Admin\Pages\PageShow;
use Lunar\StaticPages\Http\Livewire\Components\Admin\Pages\PagesTable;
use Lunar\StaticPages\Models\Page;
use Illuminate\Support\Facades\Config;
use Lunar\StaticPages\Search\PageIndexer;

class PageServiceProvider extends ServiceProvider
{

    public function register()
    {

    }

    public function boot()
    {
        $manifestAttribute = app(AttributeManifestInterface::class);
        Route::bind('page', function ($id) {
            return Page::withTrashed()->findOrFail($id);
        });

        $manifestAttribute->addType(Page::class);
        $manifest = $this->app->get(Manifest::class);

        $manifest->addPermission(function (Permission $permission) {
            $permission->name = 'Manage Pages';
            $permission->handle = 'manage-pages'; // or 'group:handle to group permissions
            $permission->description = 'Allow the staff member to manage pages';
        });


        $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');

        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'pages');
        $this->loadTranslationsFrom(__DIR__ . '/../resources/lang', 'pages');

        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        Config::set('lunar.search.models', array_merge(config('lunar.search.models'),
            [Page::class]));
        Config::set('lunar.search.indexers', array_merge(config('lunar.search.indexers'), [
            Page::class => PageIndexer::class,
        ]));

        $this->registerLivewireComponents();

    }

    public function registerLivewireComponents()
    {
        Livewire::component('pages.admin.pages.pages-index', PagesIndex::class);
        Livewire::component('pages.admin.pages.pages-create', PageCreate::class);
        Livewire::component('pages.components.admin.pages.pages-index', \Lunar\StaticPages\Http\Livewire\Components\Admin\Pages\PagesIndex::class);
        Livewire::component('pages.components.admin.pages.page-create', \Lunar\StaticPages\Http\Livewire\Components\Admin\Pages\PageCreate::class);
        Livewire::component('pages.components.admin.pages.pages-table', PagesTable::class);
        Livewire::component('pages.components.admin.pages.page-show', PageShow::class);
        Livewire::component('pages.components.activity-log-feed', ActivityLogFeed::class);
    }
}
