<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <a href="../project">戻る</a>
            </div>
            @if($html)
                <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg border-gray-500 border-2">
                    @php
                        echo $html
                    @endphp
                </div>
            @else
                <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg border-gray-500 border-2">
                    変更点なし
                </div>
            @endif
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg border-gray-400 border-2" >
                <form action="./{{$projectName}}/storeChatText" method="post">
                    @csrf
                    <label for="type">形式:</label>
                    <input type="text" name="type" id="type">として「新」の文章を添削する
                    <input type="checkbox" name="typeNull" id="typeNull">
                    <label for="typeNull">形式なし</label>
                    <x-primary-button>
                        送信する
                    </x-primary-button>
                    <input type="hidden" name="body" value="{{$texts[0]->body}}">
                </form>
                ※例：「友人への謝罪文」、「結婚式の招待文」など
            </div>
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg border-gray-400 border-2">
                <form action="" method="post">
                    @csrf
                    新規テキスト作成:
                    <textarea name="body"></textarea>
                    <x-primary-button>
                        送信する
                    </x-primary-button>
                </form>
            </div>
            @foreach($texts as $text)
                <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                    <div class="max-w-4xl">
                        
                        <div class="toggle-container cursor-pointer bg-gray-100 p-2 sm:p-4 shadow sm:rounded-lg">
                            <div class="flex">
                                <p>ID:{{$text->id}}</p>
                                <svg class="toggle-button rotate-180 ml-2" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20">
                                    <path d="M5 13 L10 8 L15 13" stroke="currentColor" stroke-width="2" fill="none"/>
                                </svg>
                            </div>
                            <span class="initial-text overflow-hidden">{{$text->bodyHead()}}</span>
                            <span class="hidden content">{!! nl2br(e($text->body)) !!}</span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</x-app-layout>