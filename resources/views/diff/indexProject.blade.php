<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            プロジェクト一覧
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <h2 class="text-lg font-medium text-gray-900">
                        新規プロジェクト作成
                    </h2>
                <form action="{{route('project.store')}}" method="post" class="mt-6 space-y-6">
                    @csrf
                    <x-input-label for="name" value="プロジェクト名" />
                    <x-text-input  type="text" name="name" id="name" class="mt-1 block w-full"/>
                    <x-input-error class="mt-2" :messages="$errors->get('name')" />
                    <x-primary-button>
                        送信
                    </x-primary-button>
                </form>
            </div>
            @foreach($projects as $project)
                <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                    <div class="max-w-xl">
                        <a href="{{route('project.show', $project->name)}}">{{$project->name}}</a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</x-app-layout>