<div class="flex justify-between items-center">
    <div class="flex items-center gap-4">
        <a href="{{ route('hub.pages.index') }}"
           class="text-gray-600 rounded bg-gray-50 hover:bg-sky-500 hover:text-white"
           title="{{ __('pages::catalogue.pages.show.back_link_title') }}">
            <x-hub::icon ref="chevron-left"
                         style="solid"
                         class="w-8 h-8"/>
        </a>

        <h1 class="text-xl font-bold md:text-xl">
            @if ($page->id)
                {{ $page->title }}
            @else
                {{ __('pages::global.new_page') }}
            @endif
        </h1>
    </div>
    <div>
        <x-hub::model-url :model="$page" :preview="$page->status == 'draft'"/>
    </div>
</div>

<x-hub::layout.bottom-panel>
    <form wire:submit.prevent="save">
        <div class="flex justify-end gap-6">
            @include('pages::partials.pages.status-bar')

            <x-hub::button type="submit">
                {{ __('pages::catalogue.pages.show.save_btn') }}
            </x-hub::button>
        </div>
    </form>
</x-hub::layout.bottom-panel>

<div class="pb-24 mt-8 lg:gap-8 lg:flex lg:items-start">
    <div class="space-y-6 lg:flex-1">
        <div class="space-y-6">
            @foreach ($this->getSlotsByPosition('top') as $slot)
                <div id="{{ $slot->handle }}">
                    <div>
                        @livewire($slot->component, ['slotModel' => $page], key('top-slot-' . $slot->handle))
                    </div>
                </div>
            @endforeach
            <div class="shadow sm:rounded-md">
                <div class="flex-col px-4 py-5 space-y-4 bg-white rounded-md sm:p-6">
                    <header>
                        <h3 class="text-lg font-medium leading-6 text-gray-900">
                            {{ __('pages::partials.pages.basic-information.heading') }}
                        </h3>
                    </header>
                    <div class="space-y-4">
                        <x-hub::input.group
                            :label="__('pages::inputs.title.label')"
                            for="title"
                            :errors="$errors->get('page.title') ?: $errors->get('page')">
                            <x-hub::input.text id="title" wire:model="page.title"/>
                        </x-hub::input.group>
                        <x-hub::input.group
                            :label="__('pages::inputs.description.label')"
                            for="description"
                            :errors="$errors->get('page.description') ?: $errors->get('page')" >
                            <x-hub::input.richtext id="description" wire:model.defer="page.description" :initial-value="$page->description"/>
                        </x-hub::input.group>
                    </div>
                </div>
            </div>
            <div id="attributes">
                @include('adminhub::partials.attributes')
            </div>
            <div id="images">
                @include('pages::partials.image-manager', [ 'existing' => $images, 'wireModel' => 'imageUploadQueue', 'filetypes' => ['image/*'], ])
            </div>
            <div id="urls">
                @include('adminhub::partials.urls')
            </div>
            @foreach ($this->getSlotsByPosition('bottom') as $slot)
                <div id="{{ $slot->handle }}">
                    <div>
                        @livewire($slot->component, ['slotModel' => $page], key('bottom-slot-' . $slot->handle))
                    </div>
                </div>
            @endforeach
            @if ($page->id)
                <div
                    @class(['bg-white border rounded shadow',
                        'border-red-300' => !$page->deleted_at,
                        'border-gray-300' => $page->deleted_at,
                    ]) >
                    <header
                        @class(['px-6 py-4 bg-white border-b rounded-t',
                            'border-red-300 text-red-700' => !$page->deleted_at,
                            'border-gray-300 text-gray-700' => $page->deleted_at,
                        ])>
                        @if($page->deleted_at)
                            {{ __('pages::inputs.restore_zone.title') }}
                        @else
                            {{ __('pages::inputs.danger_zone.title') }}
                        @endif
                    </header>
                    <div class="p-6 text-sm">
                        <div class="grid grid-cols-12 gap-4">
                            <div class="col-span-12 lg:col-span-8">
                                <strong>
                                    @if($page->deleted_at)
                                        {{ __('pages::inputs.restore_zone.label', ['model' => 'page']) }}
                                    @else
                                        {{ __('pages::inputs.danger_zone.label', ['model' => 'page']) }}
                                    @endif
                                </strong>

                                <p class="text-xs text-gray-600">
                                    @if($page->deleted_at)
                                        {{ __('pages::catalogue.pages.show.restore_strapLine') }}
                                    @else
                                        {{ __('pages::catalogue.pages.show.delete_strapLine') }}
                                    @endif

                                </p>
                            </div>

                            <div class="col-span-6 text-right lg:col-span-4">
                                @if($page->deleted_at)
                                    <x-hub::button :disabled="false" wire:click="$set('showRestoreConfirm', true)" type="button" theme="green">
                                        {{ __('pages::global.restore') }}
                                    </x-hub::button>
                                @else
                                    <x-hub::button :disabled="false" wire:click="$set('showDeleteConfirm', true)" type="button" theme="danger">
                                        {{ __('pages::global.delete') }}
                                    </x-hub::button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <x-hub::modal.dialog wire:model="showRestoreConfirm">
                    <x-slot name="title">
                        {{ __('pages::catalogue.pages.show.restore_title') }}
                    </x-slot>
                    <x-slot name="content">
                        {{ __('pages::catalogue.pages.show.restore_strapLine') }}
                    </x-slot>
                    <x-slot name="footer">
                        <div class="flex items-center justify-end space-x-4">
                            <x-hub::button theme="gray" type="button"  wire:click.prevent="$set('showRestoreConfirm', false)">
                                {{ __('pages::global.cancel') }}
                            </x-hub::button>
                            <x-hub::button wire:click="restore" theme="green">
                                {{ __('pages::catalogue.pages.show.restore_btn') }}
                            </x-hub::button>
                        </div>
                    </x-slot>
                </x-hub::modal.dialog>
                <x-hub::modal.dialog wire:model="showDeleteConfirm">
                    <x-slot name="title">
                        {{ __('pages::catalogue.pages.show.delete_title') }}
                    </x-slot>
                    <x-slot name="content">
                        {{ __('pages::catalogue.pages.show.delete_strapLine') }}
                    </x-slot>
                    <x-slot name="footer">
                        <div class="flex items-center justify-end space-x-4">
                            <x-hub::button theme="gray" type="button" wire:click.prevent="$set('showDeleteConfirm', false)">
                                {{ __('pages::global.cancel') }}
                            </x-hub::button>
                            <x-hub::button wire:click="delete" theme="danger">
                                {{ __('pages::catalogue.pages.show.delete_btn') }}
                            </x-hub::button>
                        </div>
                    </x-slot>
                </x-hub::modal.dialog>
            @endif
            <div class="pt-12 mt-12 border-t">
                @livewire('pages.components.activity-log-feed', ['subject' => $page,])
            </div>
        </div>
    </div>

    <x-hub::layout.page-menu>
        <nav class="space-y-2"
             aria-label="Sidebar"
             x-data="{ activeAnchorLink: '' }"
             x-init="activeAnchorLink = window.location.hash">
            @foreach ($this->getSlotsByPosition('top') as $slot)
                <a href="#{{ $slot->handle }}"
                   @class([
                       'flex items-center gap-2 p-2 rounded text-gray-500',
                       'hover:bg-sky-50 hover:text-sky-700' => empty(
                           $this->getSlotErrorsByHandle($slot->handle)
                       ),
                       'text-red-600 bg-red-50' => !empty(
                           $this->getSlotErrorsByHandle($slot->handle)
                       ),
                   ])
                   aria-current="page"
                   x-data="{ linkId: '#{{ $slot->handle }}' }"
                   :class="{
                       'bg-sky-50 text-sky-700 hover:text-sky-500': linkId === activeAnchorLink
                   }"
                   x-on:click="activeAnchorLink = linkId">
                    @if (!empty($this->getSlotErrorsByHandle($slot->handle)))
                        <x-hub::icon ref="exclamation-circle"
                                     class="w-4 text-red-600"/>
                    @endif

                    <span class="text-sm font-medium">
                        {{ $slot->title }}
                    </span>
                </a>
            @endforeach

            @foreach ($this->sideMenu as $item)
                <a href="#{{ $item['id'] }}"
                   @class([
                       'flex items-center gap-2 p-2 rounded text-gray-500',
                       'hover:bg-sky-50 hover:text-sky-700' => empty($item['has_errors']),
                       'text-red-600 bg-red-50' => !empty($item['has_errors']),
                   ])
                   aria-current="page"
                   x-data="{ linkId: '#{{ $item['id'] }}' }"
                   :class="{
                       'bg-sky-50 text-sky-700 hover:text-sky-500': linkId === activeAnchorLink
                   }"
                   x-on:click="activeAnchorLink = linkId">
                    @if (!empty($item['has_errors']))
                        <x-hub::icon ref="exclamation-circle"
                                     class="w-4 text-red-600"/>
                    @endif

                    <span class="text-sm font-medium">
                        {{ $item['title'] }}
                    </span>
                </a>
            @endforeach

            @foreach ($this->getSlotsByPosition('bottom') as $slot)
                <a href="#{{ $slot->handle }}"
                   @class([
                       'flex items-center gap-2 p-2 rounded text-gray-500',
                       'hover:bg-sky-50 hover:text-sky-700' => empty(
                           $this->getSlotErrorsByHandle($slot->handle)
                       ),
                       'text-red-600 bg-red-50' => !empty(
                           $this->getSlotErrorsByHandle($slot->handle)
                       ),
                   ])
                   aria-current="page"
                   x-data="{ linkId: '#{{ $slot->handle }}' }"
                   :class="{
                       'bg-sky-50 text-sky-700 hover:text-sky-500': linkId === activeAnchorLink
                   }"
                   x-on:click="activeAnchorLink = linkId">
                    @if (!empty($this->getSlotErrorsByHandle($slot->handle)))
                        <x-hub::icon ref="exclamation-circle"
                                     class="w-4 text-red-600"/>
                    @endif

                    <span class="text-sm font-medium">
                        {{ $slot->title }}
                    </span>
                </a>
            @endforeach
        </nav>
    </x-hub::layout.page-menu>
</div>
