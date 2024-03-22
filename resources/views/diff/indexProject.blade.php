<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            プロジェクト一覧
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <form action="{{route('project.store')}}" method="post">
                    @csrf
                    <h1>新規プロジェクト作成</h1>
                    <label for="name">プロジェクト名: </label>
                    <input type="text" name="name" id="name">
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