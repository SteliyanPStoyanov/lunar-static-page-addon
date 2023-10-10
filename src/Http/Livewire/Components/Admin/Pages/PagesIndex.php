<?php

namespace Lunar\StaticPages\Http\Livewire\Components\Admin\Pages;

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
        return view('pages::livewire.components.admin.pages.index')
            ->layout('adminhub::layouts.base');
    }
}
