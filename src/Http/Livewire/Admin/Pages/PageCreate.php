<?php

namespace Lunar\StaticPages\Http\Livewire\Admin\Pages;

use Livewire\Component;

class PageCreate extends Component
{
    /**
     * Render the livewire component.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('pages::livewire.admin.pages.create')
            ->layout('adminhub::layouts.app', [
                'title' => 'Create Page',
            ]);
    }
}
