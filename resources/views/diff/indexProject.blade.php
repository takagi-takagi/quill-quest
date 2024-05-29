<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            イベント一覧
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-1">
            @if(session('message'))
                <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg text-lg font-medium text-red-400">
                    {{session('message')}}
                </div>
            @endif
            @if($projects->isEmpty())
                <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg text-lg font-medium text-red-400">
                    イベントがありません。まずはイベントを作成しましょう！
                </div>
            @endif
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <h2 class="text-lg font-medium text-gray-900 mb-6">
                        新規イベント作成
                    </h2>
                <form action="{{route('project.store')}}" method="post" class="space-y-6">
                    @csrf
                    <x-input-label for="name" value="イベント名" />
                    <x-text-input  type="text" name="name" id="name" class="mt-1 block w-full" value="{{old('body')}}"/>
                    <x-input-error class="mt-2" :messages="$errors->get('name')" />
                    <x-primary-button>
                        作成
                    </x-primary-button>
                </form>
            </div>
            @foreach($projects as $project)
                <x-responsive-project-link :href="route('project.show', $project->user_project_id)">
                <div class="w-full flex justify-between items-center">
    <div class="flex-1 overflow-hidden">
        <p class="whitespace-normal break-words">
            {{$project->user_project_id}}: {{$project->name}}
        </p>
    </div>
    <div class=mx-2>
        <x-danger-button x-data="" x-on:click.prevent="$dispatch('open-modal', 'delete-{{$project->user_project_id}}')">
            削除
        </x-danger-button>
    </div>
</div>

                </x-responsive-project-link>
                <x-modal name="delete-{{$project->user_project_id}}" :show="$errors->userDeletion->isNotEmpty()" focusable>
                    <div class="p-6">
                        <p>{{$project->user_project_id}}:</p>
                        <p>{{$project->name}}</p>
                        <div class="mt-8">
                            <h2 class="text-lg font-medium text-red-400">
                                本当に削除しますか？
                            </h2>
                            <form action="{{route('project.destroy',$project)}}" method="post">
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