<x-app-layout>

    <form action="{{ route('login') }}" method="post" class="w-[500px] mx-auto p-6 my-16">
        @csrf
        <h2 class="text-2xl font-semibold text-center mb-5">
            アカウントにログイン
        </h2>
        <p class="text-center text-gray-500 mb-6">

            <a href="{{ route('register') }}" class="text-sm text-purple-700 hover:text-purple-600">新規登録する</a>
        </p>
        <div class="mb-4">
            <x-text-input :errors="$errors" id="loginEmail" type="email" name="email" placeholder="メールアドレス"
                :value="old('email')" autofocus/>
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>
        <div class="mb-4">
            <x-text-input id="loginPassword" type="password" name="password" placeholder="パスワード"
                class="border-2 border-gray-300 focus:border-purple-500 focus:outline-none focus:ring-purple-500 rounded-md w-full  py-3 px-2" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="flex justify-between items-center mb-7">
            <div class="flex items-center relative">
                    <input id="loginRememberMe" type="checkbox" name="remember" class="cursor-pointer">
                    <span class="mr-3"></span>
                <label for="loginRememberMe">次回自動ログイン</label>
            </div>
            <a href="{{ route('password.request') }}" class="text-sm text-purple-700 hover:text-purple-600">パスワードを忘れた場合はこちら</a>
        </div>
        <div class="flex justify-center">
            <button class="btn-primary bg-emerald-500 hover:bg-emerald-600 active:bg-emerald-700">
                ログイン
            </button>
        </div>
    </form>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

</x-app-layout>
