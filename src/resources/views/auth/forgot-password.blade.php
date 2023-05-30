<x-app-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form action="{{ route('password.email')}}" method="post" class="w-[500px] mx-auto p-6 my-16">
        @csrf
        <h2 class="text-xl font-semibold text-center mb-5">
            パスワードを再設定するためにメールアドレスを入力してください
        </h2>
        <p class="text-center text-gray-500 mb-6">
            <a href="{{ route('login') }}" class="text-purple-600 hover:text-purple-500">既存のアカウントでログイン</a>
        </p>

        <div class="mb-3">
            <x-text-input id="loginEmail" type="email" name="email" placeholder="メールアドレス" :value="old('email')" required autofocus/>
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>
        <button class="btn-primary bg-emerald-500 hover:bg-emerald-600 active:bg-emerald-700 w-full">
            送信
        </button>
    </form>
</x-app-layout>
