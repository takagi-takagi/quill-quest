<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            フォルダ一覧
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if(session('message'))
                <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg text-lg font-medium text-red-400">
                    {{session('message')}}
                </div>
            @endif
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <h2 class="text-lg font-medium text-gray-900">
                        新規フォルダ作成
                    </h2>
                <form action="{{route('project.store')}}" method="post" class="mt-6 space-y-6">
                    @csrf
                    <x-input-label for="name" value="フォルダ名" />
                    <x-text-input  type="text" name="name" id="name" class="mt-1 block w-full" value="{{old('body')}}"/>
                    <x-input-error class="mt-2" :messages="$errors->get('name')" />
                    <x-primary-button>
                        送信
                    </x-primary-button>
                </form>
            </div>
            @foreach($projects as $project)
                <x-responsive-project-link :href="route('project.show', $project->user_project_id)">
                    <div class="w-full">
                        <p>ID:{{$project->user_project_id}}</p>
                        <p>{{$project->name}}</p>
                        <form action="{{route('project.destroy',$project)}}" method="post">
                                @csrf
                                @method('delete')
                                <x-danger-button>
                                    削除する
                                </x-danger-button>
                            </form>
                    </div>
                </x-responsive-project-link>
            @endforeach
        </div>
    </div>
</x-app-layout>