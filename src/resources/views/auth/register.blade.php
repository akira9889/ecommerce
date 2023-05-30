<x-app-layout>
    <form action="{{ route('register') }}" method="post" class="w-[500px] mx-auto p-6 my-16">
        @csrf
        <h2 class="text-2xl font-semibold text-center mb-4">アカウント作成</h2>
        <p class="text-center text-gray-500 mb-4">
            <a href="{{ route('login') }}" class="text-sm text-purple-700 hover:text-purple-600">既存のアカウントでログイン</a>
        </p>
        <div class="mb-6">
            <x-text-input placeholder="氏名" type="text" name="name" :value="old('name')" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>
        </p>
        <div class="mb-6">
            <x-text-input placeholder="メールアドレス" type="email" name="email" :value="old('email')" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>
        <div class="mb-6">
            <x-text-input placeholder="パスワード" type="password" name="password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>
        </div>
        <div class="mb-8">
            <x-text-input placeholder="パスワード確認" type="password" name="password_confirmation" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex justify-center">
            <button class="btn-primary bg-emerald-500 hover:bg-emerald-600 active:bg-emerald-700">
                登録する
            </button>
        </div>
    </form>
    </x-guest-layout>
