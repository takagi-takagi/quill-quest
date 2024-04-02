<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            ID:{{$project->user_project_id}} {{$project->name}}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <x-responsive-project-link href="../project">
                <div class="max-w-xl">
                    フォルダ一覧に戻る
                </div>
            </x-responsive-project-link>
            @if(session('message'))
                <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg text-lg font-medium text-red-400">
                    {{session('message')}}
                </div>
            @endif
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
                <form action="./{{$project->user_project_id}}/storeChatText" method="post">
                    @csrf
                    <x-input-label for="type" value="形式:" />
                    <div class="flex items-center mt-1">
                        <x-text-input id="type" name="type" type="text" class="flex-1"/>
                        <span class="font-medium text-sm text-gray-700">として「新」の文章を添削する</span>
                    </div>
                    <x-input-error class="mt-2" :messages="$errors->get('type')" />
                    <span class="text-sm text-gray-600">もしくは</span>
                    <label for="typeNull" class="flex items-center mt-2">
                        <input id="typeNull" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="typeNull">
                        <span class="ms-2 font-medium text-sm text-gray-700">形式なし</span>
                    </label>
                    <x-primary-button class="normal-case mt-4">
                        ChatGPTで文章を生成する
                    </x-primary-button>
                    @if(isset($newId))
                        <input type="hidden" name="newId" value="{{$newId}}">
                    @elseif(isset($texts[0]))
                        <input type="hidden" name="body" value="{{$texts[0]->body}}">
                    @endif
                </form>
                <p class="mt-1 text-sm text-gray-600">
                    ※形式例：「友人への謝罪文」、「結婚式の招待文」など
                </p>
            </div>
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg border-gray-400 border-2">
                <form action="" method="post">
                    @csrf
                    <x-input-label for="body" value="新規テキスト作成:" />
                    <textarea name="body" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full">{{old('body')}}</textarea>
                    <x-input-error class="mt-2" :messages="$errors->get('body')" />
                    <x-primary-button>
                        送信する
                    </x-primary-button>
                </form>
            </div>
            @foreach($texts as $text)
                <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                    <div class="w-full">
                        <x-text-container>
                            <div class="flex">
                                <p>ID:{{$text->project_text_id}}</p>
                                <svg class="toggle-button rotate-180 ml-2" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20">
                                    <path d="M5 13 L10 8 L15 13" stroke="currentColor" stroke-width="2" fill="none"/>
                                </svg>
                            </div>
                            <span class="initial-text overflow-hidden">{{$text->bodyHead()}}</span>
                            <span class="hidden content break-words">{!! nl2br(e($text->body)) !!}</span>
                        </x-text-container>
                        <div class="button-container hidden  w-full mt-2 ms-4">
                            <div class="font-medium text-sm text-gray-700">
                                @if($text->is_posted == true)
                                    <p>自分で投稿した文章</p>
                                @else
                                    @if($text->type == null)
                                        <p class="text-red-400">生成された文章(形式:<span class="text-gray-400">なし</span>)</p>
                                    @else
                                        <p class="text-red-400">生成された文章(形式:{{$text->type}})</p>
                                    @endif
                                @endif
                            </div>
                            <div class="flex space-x-4 mt-2">
                                <form action="./{{$project->user_project_id}}/setQuery" method="post">
                                    @csrf
                                    <input type="hidden" name="setToOld" value="{{$text->project_text_id}}">
                                    <x-primary-button>
                                        旧にセットする
                                    </x-primary-button>
                                    @if(isset($newId))
                                        <input type="hidden" name="setToNew" value="{{$newId}}">
                                        <input type="hidden" name="oldButton" value="true">
                                    @endif
                                </form>
                                <form action="./{{$project->user_project_id}}/setQuery" method="post">
                                    @csrf
                                    <input type="hidden" name="setToNew" value="{{$text->project_text_id}}">
                                    <x-primary-button>
                                        新にセットする
                                    </x-primary-button>
                                    @if(isset($oldId))
                                        <input type="hidden" name="setToOld" value="{{$oldId}}">
                                        <input type="hidden" name="newButton" value="true">
                                    @endif
                                </form>
                                <button class="copyButton w-44 text-center justify-center inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">コピーする</button>
                                <form action="{{route('text.destroy',$text)}}" method="post">
                                    @csrf
                                    @method('delete')
                                    <x-danger-button>
                                        削除する
                                    </x-danger-button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</x-app-layout>