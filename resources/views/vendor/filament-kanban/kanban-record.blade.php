<div id="{{ $record->id }}" wire:click="recordClicked('{{ $record->id }}', {{ @json_encode($record) }})"
    class="record transition bg-white dark:bg-gray-700 rounded-lg px-4 py-2 cursor-grab font-medium text-gray-600 dark:text-gray-200"
    @if ($record->just_updated) x-data
        x-init="
            $el.classList.add('animate-pulse-twice', 'bg-primary-100', 'dark:bg-primary-800')
            $el.classList.remove('bg-white', 'dark:bg-gray-700')
            setTimeout(() => {
                $el.classList.remove('bg-primary-100', 'dark:bg-primary-800')
                $el.classList.add('bg-white', 'dark:bg-gray-700')
            }, 3000)
        " @endif>
    <div class="flex justify-between">
        <div>
            {{ $record->title }}

            @if ($record->urgent)
                <x-heroicon-s-star class="inline-block text-purple-500 w-4 h-4" />
            @endif
        </div>
        <div class="text-xs text-right {{ auth()->user()->id === $record->user->id ? 'text-primary-500':'text-gray-400' }}">{{ $record->user->name }}</div>
    </div>

    <div class="text-xs text-gray-400 border-l-4 pl-2 mt-2 mb-2">
        {{ $record->description }}
    </div>

    <div class="flex hover:-space-x-1 -space-x-3">
        @foreach ($record->team as $member)
            <div class="flex items-center gap-x-2.5">
                <div class="flex gap-1.5">
                    <img src="{{ asset('storage/' . $member->image) }}" style="height: 2.5rem; width: 2.5rem;"
                        class="max-w-none object-cover object-center rounded-full ring-white dark:ring-gray-900"
                        alt="{{ $member->name }}">
                </div>
            </div>
        @endforeach
    </div>

    <div class="mt-3 relative">
        <div class="absolute h-1 bg-primary-500 rounded-full" style="width: {{ $record->progress }}%"></div>
        <div class="h-1 bg-gray-200 rounded-full"></div>
    </div>
</div>
