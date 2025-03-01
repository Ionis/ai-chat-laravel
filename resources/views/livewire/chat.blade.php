<div class="flex-1 flex flex-col p-6 bg-white dark:bg-gray-800">
    <h1 class="text-2xl font-semibold text-center dark:text-white mb-4">ChatGPT на Laravel (Livewire)</h1>

    <div id="messages" class="flex-grow overflow-y-auto border rounded-lg p-4 space-y-2 bg-gray-50 dark:bg-gray-700">
        @foreach($chatHistory as $msg)
            <div class="flex {{ $msg['is_bot'] ? 'justify-start' : 'justify-end' }} items-center space-x-2">
                @if($msg['is_bot'])
                    <img src="{{ asset('/images/bot-avatar.png') }}" class="w-8 h-8 rounded-full">
                    <span class="bg-gray-200 dark:bg-gray-600 text-black dark:text-white p-2 rounded-lg max-w-xs">
                        {{ $msg['message'] }}
                    </span>
                @else
                    <span class="bg-blue-500 text-white p-2 rounded-lg max-w-xs">{{ $msg['message'] }}</span>
                    <img src="{{ asset('/images/user-avatar.jpg') }}" class="w-8 h-8 rounded-full">
                @endif
            </div>
        @endforeach

        <!-- Потоковое сообщение -->
        @if($streamingReply)
            <div class="flex justify-start items-center space-x-2">
                <img src="{{ asset('/images/bot-avatar.png') }}" class="w-8 h-8 rounded-full">
                <span class="bg-gray-200 dark:bg-gray-600 text-black dark:text-white p-2 rounded-lg max-w-xs">
                    <span wire:stream="streamingReply"></span>
                </span>
            </div>
        @endif
    </div>

    <form wire:submit.prevent="sendMessage" class="mt-4 flex">
        <input type="text" wire:model="userInput" placeholder="Введите сообщение..."
               class="flex-1 p-2 border rounded-lg focus:outline-none focus:ring focus:border-blue-300 dark:bg-gray-600 dark:text-white">
        <button type="submit" class="ml-2 bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition">
            Отправить
        </button>
    </form>
</div>
