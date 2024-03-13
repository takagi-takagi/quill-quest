<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                @php
                    echo $html
                @endphp
            </div>
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
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
                    <div class="max-w-xl">
                        <p>ID:{{$text->id}}</p>
                        <p>{!! nl2br(e($text->body)) !!}</p>
                    </div>
                </div>
            @endforeach
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    <!-- @include('profile.partials.update-password-form') -->
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    <!-- @include('profile.partials.delete-user-form') -->
                </div>
            </div>
        </div>
    </div>
</x-app-layout>