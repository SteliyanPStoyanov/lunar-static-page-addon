<?php

namespace Lunar\StaticPages\Http\Livewire\Components\Admin\Pages;

use Lunar\StaticPages\Models\Page;

class PageCreate extends AbstractPage
{
    /**
     * Called when the component is mounted.
     *
     * @return void
     */
    public function mount()
    {
        $this->page = new Page([
            'status' => 'draft',
            'title' => 'draft',
        ]);

    }

    /**
     * Render the livewire component.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('pages::livewire.components.admin.pages.create')
            ->layout('adminhub::layouts.base');
    }

    protected function getSlotContexts()
    {
        return ['page.all', 'page.create'];
    }
}
