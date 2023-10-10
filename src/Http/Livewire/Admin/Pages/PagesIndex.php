<?php

namespace Lunar\StaticPages\Http\Livewire\Admin\Pages;

use Livewire\Component;

class PagesIndex extends Component
{
    /**
     * Render the livewire component.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('pages::livewire.admin.pages.index')
            ->layout('adminhub::layouts.app', [
                'title' => 'Pages',
            ]);
    }
}
