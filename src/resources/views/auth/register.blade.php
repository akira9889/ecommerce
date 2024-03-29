<x-app-layout>
    <form action="{{ route('register') }}" method="post" class="w-[500px] mx-auto p-6 my-16">
        @csrf
        <h2 class="text-2xl font-semibold text-center mb-4">アカウント作成</h2>
        <p class="text-center text-gray-500 mb-4">
            <a href="{{ route('login') }}" class="text-sm text-purple-700 hover:text-purple-600">既存のアカウントでログイン</a>
        </p>
        <div class="mb-6 grid grid-cols-2 gap-3">
            <div>
                <x-text-input placeholder="姓" type="text" name="last_name" :value="old('last_name')" autofocus/>
                <x-input-error :messages="$errors->get('last_name')" class="mt-2" />
            </div>
            <div>
                <x-text-input placeholder="名" type="text" name="first_name" :value="old('first_name')" autofocus/>
                <x-input-error :messages="$errors->get('first_name')" class="mt-2" />
            </div>
        </div>
        <div class="mb-6 grid grid-cols-2 gap-3">
            <div>
                <x-text-input placeholder="セイ" type="text" name="last_kana" :value="old('last_kana')" autofocus/>
                <x-input-error :messages="$errors->get('last_kana')" class="mt-2" />
            </div>
            <div>
                <x-text-input placeholder="メイ" type="text" name="first_kana" :value="old('first_kana')" autofocus/>
                <x-input-error :messages="$errors->get('first_kana')" class="mt-2" />
            </div>
        </div>
        </p>
        <div class="mb-6">
            <x-text-input placeholder="メールアドレス" type="email" name="email" :value="old('email')" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>
        <div class="mb-6">
            <x-text-input placeholder="パスワード" type="password" name="password" autocomplete/>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>
        </div>
        <div class="mb-8">
            <x-text-input placeholder="パスワード確認" type="password" name="password_confirmation" autocomplete/>
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex justify-center">
            <button class="btn-primary bg-emerald-500 hover:bg-emerald-600 active:bg-emerald-700">
                登録する
            </button>
        </div>
    </form>
    </x-guest-layout>
