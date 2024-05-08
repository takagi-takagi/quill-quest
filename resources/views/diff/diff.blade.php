<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            ID:{{$project->user_project_id}} {{$project->name}}
        </h2>
    </x-slot>

    <div class="py-12" x-data="{ dropdownOldId: '', dropdownNewId: '' ,dropdownTextHisoty: ''}">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-1">
            <x-responsive-project-link href="../project">
                <div class="max-w-xl">
                    イベント一覧に戻る
                </div>
            </x-responsive-project-link>
            @if(session('message'))
                <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg text-lg font-medium text-red-400">
                    {{session('message')}}
                </div>
            @endif
            
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg" >
                <form action="./{{$project->user_project_id}}/checkAndStore" method="post" class="flex flex-col md:flex-row items-center md:items-stretch">
                    @csrf
                    @php
                        $textareaContent = old('body') !== null ? old('body') : ($texts[0]->body ?? '');
                    @endphp
                    
                    <div class="flex flex-col w-full md:w-1/2  border border-gray-300 rounded-md p-1">
                        <label>
                            <input type="radio" name="textFormType" value="history">
                            テキスト履歴
                            <select id="dropdownTextHisoty" name="dropdownTextHisoty" x-model="dropdownTextHisoty">
                                <option value="">選択してください</option>
                                @foreach($texts as $text)
                                    <option value="{{$text->project_text_id}}" 
                                            {{ old('dropdownTextHisoty', $loop->first ? $text->project_text_id : null) == $text->project_text_id ? 'selected' : '' }}>
                                        {{$text->project_text_id}}{{ $loop->first ? ' - 最新' : '' }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('dropdownTextHisoty')" />
                            <p class="mt-1 text-sm text-gray-600">
                                ※変換後のテキストのみが履歴に追加されます
                            </p>
                        </label>
                        <label>
                            <input type="radio" name="textFormType" value="textarea">
                            新規入力
                            <textarea name="body" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full">{{ $textareaContent }}</textarea>
                            <x-input-error class="mt-2" :messages="$errors->get('body')" />
                            <p class="mt-1 text-sm text-gray-600">
                                ※入力されたテキストと変換後のテキストが履歴に追加されます
                            </p>
                        </label>
                        <x-input-error class="mt-2" :messages="$errors->get('textFormType')" />
                    </div>
                    <div class="md:mt-16 md:ml-2">
                        を
                    </div>
                    <div class="flex flex-col ml-2 w-full md:w-1/2">
                        <div  class="flex flex-col border border-gray-300 rounded-md p-1">
                            <label  class="w-full">
                                <input type="radio" name="storeType" value="transform_dropdown">
                                ChatGPTで
                                <select id="dropdownTextType" name="dropdownTextType" x-model="dropdownOldId" class="md:w-auto w-full">
                                    <option value="">選択してください</option>
                                    @foreach($uniqueTypeTexts as $uniqueTypeText)
                                        <option value="{{$uniqueTypeText->type}}">履歴：{{$uniqueTypeText->type}}</option>
                                    @endforeach
                                    <option value="謝罪文">例　：謝罪文</option>
                                    <option value="お礼のメール">例　：お礼のメール</option>
                                    <option value="犬">例　：犬</option>
                                </select>
                                風に変換
                                <x-input-error class="mt-2" :messages="$errors->get('dropdownTextType')" />
                            </label>
                            <label class="w-full">
                                <input type="radio" name="storeType" value="transform_input">
                                ChatGPTで
                                <input type="text" name="inputTextType" id="inputTextType" placeholder="入力してください" class="md:w-auto w-full">
                                風に変換
                                <x-input-error class="mt-2" :messages="$errors->get('inputTextType')" />
                            </label>
                            <label>
                                <input type="radio" name="storeType" value="proofread">
                                ChatGPTで校正
                            </label>
                            <!-- <label>
                                <input type="radio" name="storeType" value="save_new">
                                新規テキストとして保存
                            </label> -->
                            <x-input-error class="mt-2" :messages="$errors->get('storeType')" />
                        </div>
                        <div>
                            <x-primary-button class="mt-2">
                                実行
                            </x-primary-button>
                        </div>
                    </div>
                </form>
            </div>
            @if(session('createText'))
                <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg text-lg font-medium">
                    @foreach($texts as $text)
                        @if($loop->first)
                            <p class="text-red-400">
                                {{$text->project_text_id}}:
                                @if($text->is_posted == true)
                                        (入力されたテキスト)
                                    @else
                                        @if($text->type == null)
                                            (校正された後のテキスト)
                                        @else
                                            (生成された文章({{$text->type}}風))
                                        @endif
                                    @endif
                            </p>
                            <p>{{$text->body}}</p>
                        @endif
                    @endforeach
                </div>
            @endif
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                @if($html)
                    @php
                        echo $html
                    @endphp
                @else
                <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg text-lg font-medium text-red-400">
                    変更点なし
                </div>
                @endif
                <form action="./{{$project->user_project_id}}/setQuery2" method="post" class="mt-2">
                    @csrf
                    <select id="dropdownOldId" name="dropdownOldId" x-model="dropdownOldId">
                        <option value="">選択してください</option>
                        @foreach($texts as $text)
                            @if($loop->first)
                                <option value="{{$text->project_text_id}}">{{$text->project_text_id}} - 最新</option>
                            @else
                                <option value="{{$text->project_text_id}}">{{$text->project_text_id}}</option>
                            @endif
                        @endforeach
                    </select>
                    と
                    <select id="dropdownNewId" name="dropdownNewId" x-model="dropdownNewId">
                        <option value="">選択してください</option>
                        @foreach($texts as $text)
                        <option value="{{$text->project_text_id}}" 
                                        {{ (old('dropdownNewId') == $text->project_text_id) ? 'selected' : '' }}>
                                {{$text->project_text_id}}{{ $loop->first ? ' - 最新' : '' }}
                            </option>
                        @endforeach
                    </select>
                    を比較する
                    <br class="block md:hidden">
                    <x-primary-button class=" mt-2 md:mt-0">
                        実行
                    </x-primary-button>
                    <x-input-error class="mt-2" :messages="$errors->get('dropdownOldId')" />
                    <x-input-error class="mt-2" :messages="$errors->get('dropdownNewId')" />
                </form>
            </div>
            
            <div class="mb-4">
                {{ $textsWithPagination->appends(Request::except('page'))->links() }}
            </div>
            @foreach($textsWithPagination as $text)
                <x-responsive-project-link>
                    <div class="w-full flex justify-between" x-data=""
                        x-on:click.prevent="$dispatch('open-modal', {{$text->project_text_id}})">
                        <div class="flex space-x-1">
                            <x-primary-button x-on:click="dropdownTextHisoty = {{$text->project_text_id}}" x-on:click.stop class="hidden lg:block">
                                変換対象にセット
                            </x-primary-button>
                            <x-primary-button x-on:click="dropdownOldId = {{$text->project_text_id}}" x-on:click.stop class="hidden lg:block">
                                左にセット
                            </x-primary-button>
                            <x-primary-button x-on:click="dropdownNewId = {{$text->project_text_id}}" x-on:click.stop class="hidden lg:block">
                                右にセット
                            </x-primary-button>
                            <div x-data="{ text: '{{$text->body}}' }">
                                <x-primary-button  x-on:click="navigator.clipboard.writeText(text).then(() => alert('`{{$text->body}}`\n\nをコピーしました！')).catch(err => console.error('コピーに失敗しました:', err))"  x-on:click.stop class="hidden lg:block">
                                    コピー
                                </x-primary-button>
                            </div>
                            <p>{{$text->project_text_id}}: {{$text->bodyHead()}}</p>
                        </div>
                        
                            <x-danger-button  x-data="" x-on:click.prevent="$dispatch('open-modal', 'delete-{{$text->project_text_id}}')"  x-on:click.stop class="hidden lg:block">
                                削除
                            </x-danger-button>
                        
                    </div>
                </x-responsive-project-link>
                <x-modal name="{{$text->project_text_id}}" :show="$errors->userDeletion->isNotEmpty()" focusable>
                    <div class="p-6">
                        <p>
                            {{$text->project_text_id}}:
                            @if($text->is_posted == true)
                                    (入力されたテキスト)
                                @else
                                    @if($text->type == null)
                                        (校正された後のテキスト)
                                    @else
                                        (生成された文章({{$text->type}}風))
                                    @endif
                                @endif
                        </p>
                        <p>{{$text->body}}</p>
                        <div class="w-full flex sm:justify-between  flex-col sm:flex-row" x-data="{ text: '{{$text->body}}' }"
                            x-on:click.prevent="$dispatch('open-modal', {{$text->project_text_id}})">
                            <div class="flex sm:space-x-1 space-y-1 flex-col sm:flex-row">
                                <x-primary-button x-on:click="dropdownTextHisoty = {{$text->project_text_id}}">
                                    変換対象にセット
                                </x-primary-button>
                                <x-primary-button x-on:click="dropdownOldId = {{$text->project_text_id}}">
                                    左にセット
                                </x-primary-button>
                                <x-primary-button x-on:click="dropdownNewId = {{$text->project_text_id}}">
                                    右にセット
                                </x-primary-button>
                                
                                    <x-primary-button  x-on:click="navigator.clipboard.writeText(text).then(() => alert('`{{$text->body}}`\n\nをコピーしました！')).catch(err => console.error('コピーに失敗しました:', err))">
                                        コピー
                                    </x-primary-button>
                            </div>
                                <x-danger-button  class="mt-1" x-data="" x-on:click.prevent="$dispatch('open-modal', 'delete-{{$text->project_text_id}}')">
                                    削除
                                </x-danger-button>
                        </div>
                    </div>
                </x-modal>
                <x-modal name="delete-{{$text->project_text_id}}" :show="$errors->userDeletion->isNotEmpty()" focusable>
                    <div class="p-6">
                        <p>
                            {{$text->project_text_id}}:
                            @if($text->is_posted == true)
                                    (入力されたテキスト)
                                @else
                                    @if($text->type == null)
                                        (校正された後のテキスト)
                                    @else
                                        (生成された文章({{$text->type}}風))
                                    @endif
                                @endif
                        </p>
                        <p>{{$text->body}}</p>
                        <div class="mt-8">
                            <h2 class="text-lg font-medium text-red-400">
                                本当に削除しますか？
                            </h2>
                            <form action="{{route('text.destroy',$text)}}" method="post" class="flex-1 mt-8">
                                @csrf
                                @method('delete')
                                <x-danger-button>
                                    削除
                                </x-danger-button>
                            </form>
                        </div>
                    </div>
                </x-modal>
            @endforeach
        </div>
    </div>
</x-app-layout>