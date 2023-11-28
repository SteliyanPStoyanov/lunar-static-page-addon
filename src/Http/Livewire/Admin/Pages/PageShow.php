<?php

namespace Lunar\StaticPages\Http\Livewire\Admin\Pages;

use Livewire\Component;
use Lunar\StaticPages\Models\Page;

class PageShow extends Component
{
    /**
     * The Product we are currently editing.
     */
    public Page $page;

    /**
     * Render the livewire component.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {

        return view('pages::livewire.admin.pages.show')
            ->layout('adminhub::layouts.app', [
                'title' => __('pages::components.pages.index.edit_page'),
            ]);
    }
}
