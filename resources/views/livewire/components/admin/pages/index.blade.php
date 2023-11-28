<div class="flex-col space-y-4">
    <div class="flex items-center justify-between">
        <strong class="text-lg font-bold md:text-2xl">
            {{ __('pages::components.pages.index.title') }}
        </strong>
        <div class="text-right">
            <x-hub::button tag="a" href="{{ route('hub.pages.create') }}">
                {{ __('pages::components.pages.index.create_page') }}</x-hub::button>
        </div>
    </div>
    @livewire('pages.components.admin.pages.pages-table')
</div>
