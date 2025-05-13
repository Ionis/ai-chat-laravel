<div class="flex-1 flex flex-col p-6 bg-white dark:bg-gray-800 h-screen max-h-screen">
    <h1 class="text-2xl font-semibold text-center dark:text-white mb-4">ChatGPT на Laravel (Livewire)</h1>
    <div class="flex justify-end mb-4">
        <button wire:click="clearChat" class="text-red-500 hover:text-red-600 dark:text-red-400 dark:hover:text-red-300 px-2 py-1 text-sm transition">
            Очистить чат
        </button>
    </div>

    <div
        x-data="{
            init() {
                const scrollAnchor = document.getElementById('scroll-anchor');

                const scrollToBottom = () => {
                    scrollAnchor.scrollIntoView({ behavior: 'smooth' });
                };

                Livewire.on('messageAdded', scrollToBottom);
                Livewire.on('answerUpdated', scrollToBottom);

                const observer = new MutationObserver(() => {
                    scrollToBottom();
                });

                observer.observe(this.$el, { childList: true, subtree: true });
            }
        }"
        x-init="init"
        class="flex-grow overflow-y-auto border rounded-lg p-4 bg-gray-50 dark:bg-gray-700 space-y-2"
    >
        @foreach($messages as $message)

            <div class="flex flex-col gap-2" id="message-{{ $message['id'] }}">
                <div class="flex justify-end items-center space-x-2">
                    <span class="bg-blue-500 text-white p-2 rounded-lg max-w-xs">
                        {{ $message['question'] }}
                    </span>
                    <img src="{{ asset('/images/user-avatar.jpg') }}" class="w-8 h-8 rounded-full" alt="user-avatar">
                </div>
                <div class="flex justify-start items-center space-x-2">
                    <img src="{{ asset('/images/bot-avatar.png') }}" class="w-8 h-8 rounded-full" alt="bot-avatar">
                    <span wire:stream="answer_{{ $message['id'] }}" wire:ignore class="bg-gray-200 dark:bg-gray-600 text-black dark:text-white p-2 rounded-lg">
                        {{ $message['answer'] }}
                    </span>
                </div>
            </div>
        @endforeach
        <div id="scroll-anchor"></div>
    </div>

    <form wire:submit="submitPrompt" class="mt-4 flex" onsubmit="return false;">
        <input wire:model="prompt"
               type="text" placeholder="Введите сообщение..."
               class="flex-1 p-2 border rounded-lg focus:outline-none focus:ring focus:border-blue-300 dark:bg-gray-600 dark:text-white"
               autofocus>
        <button type="submit"
                class="ml-2 bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition">
            Отправить
        </button>
    </form>
</div>
