<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{$projectName}}
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
                    @if(isset($newId))
                        <input type="hidden" name="newId" value="{{$newId}}">
                    @elseif(isset($texts[0]))
                        <input type="hidden" name="body" value="{{$texts[0]->body}}">
                    @endif
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
                                <p>ID:{{$text->project_text_id}}</p>
                                <svg class="toggle-button rotate-180 ml-2" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20">
                                    <path d="M5 13 L10 8 L15 13" stroke="currentColor" stroke-width="2" fill="none"/>
                                </svg>
                            </div>
                            <span class="initial-text overflow-hidden">{{$text->bodyHead()}}</span>
                            <span class="hidden content">{!! nl2br(e($text->body)) !!}</span>
                        </div>
                        <div class="button-container hidden flex space-x-4">
                            <form action="./{{$projectName}}/setQuery" method="post">
                                @csrf
                                <input type="hidden" name="setToOld" value="{{$text->project_text_id}}">
                                <x-primary-button>
                                    旧にセットする
                                </x-primary-button>
                                @if(isset($newId))
                                    <input type="hidden" name="setToNew" value="{{$newId}}">
                                @endif
                            </form>
                            <form action="./{{$projectName}}/setQuery" method="post">
                                @csrf
                                <input type="hidden" name="setToNew" value="{{$text->project_text_id}}">
                                <x-primary-button>
                                    新にセットする
                                </x-primary-button>
                                @if(isset($oldId))
                                    <input type="hidden" name="setToOld" value="{{$oldId}}">
                                @endif
                            </form>
                            <button class="copyButton inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">コピーする</button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</x-app-layout>