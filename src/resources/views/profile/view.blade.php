<x-app-layout>
    <div x-data="{
        flashMessage: '{{ Illuminate\Support\Facades\Session::get('flash_message') }}',
        init() {
            if (this.flashMessage) {
                setTimeout(() => this.$dispatch('notify', { message: this.flashMessage }), 200)
            }
        }
    }" class="container mx-auto lg:w-2/3 p-5">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-start">
            <div class="bg-white p-3 shadow rounded-lg md:col-span-2">
                <form x-data="{
                    countries: {{ json_encode($countries) }},
                    billingAddress: {{ json_encode([
                        'address1' => old('billing.address1', $billingAddress->address1),
                        'address2' => old('billing.address2', $billingAddress->address2),
                        'city' => old('billing.city', $billingAddress->city),
                        'state' => old('billing.state', $billingAddress->state),
                        'country_code' => old('billing.country_code', $billingAddress->country_code),
                        'zipcode' => old('billing.zipcode', $billingAddress->zipcode),
                    ]) }},
                    shippingAddress: {{ json_encode([
                        'address1' => old('shipping.address1', $shippingAddress->address1),
                        'address2' => old('shipping.address2', $shippingAddress->address2),
                        'city' => old('shipping.city', $shippingAddress->city),
                        'state' => old('shipping.state', $shippingAddress->state),
                        'country_code' => old('shipping.country_code', $shippingAddress->country_code),
                        'zipcode' => old('shipping.zipcode', $shippingAddress->zipcode),
                    ]) }},
                    get billingCountryStates() {
                        const country = this.countries.find(c => c.code === this.billingAddress.country_code)
                        if (country && country.states) {
                            return JSON.parse(country.states)
                        }
                        return null
                    },
                    get shippingCountryStates() {
                        const country = this.countries.find(c => c.code === this.shippingAddress.country_code)
                        if (country && country.states) {
                            return JSON.parse(country.states)
                        }
                        return null
                    }
                }" action="{{ route('profile.update') }}" method="post">
                    @csrf
                    <h2 class="text-xl font-semibold mb-2">プロフィール設定</h2>
                    <div class="grid grid-cols-2 gap-3 mb-3">
                        <x-text-input type="text" name="last_name"
                            value="{{ old('last_name', $customer->last_name) }}" placeholder="姓"
                            class="w-full focus:border-purple-600 focus:ring-purple-600 border-gray-300 rounded" />
                        <x-text-input type="text" name="first_name"
                            value="{{ old('first_name', $customer->first_name) }}" placeholder="名"
                            class="w-full focus:border-purple-600 focus:ring-purple-600 border-gray-300 rounded" />
                    </div>
                    <div class="mb-3">
                        <x-text-input type="text" name="email" value="{{ old('email', $user->email) }}"
                            placeholder="メールアドレス"
                            class="w-full focus:border-purple-600 focus:ring-purple-600 border-gray-300 rounded" />
                    </div>
                    <div class="mb-3">
                        <x-text-input type="text" name="phone" value="{{ old('phone', $customer->phone) }}"
                            placeholder="電話番号"
                            class="w-full focus:border-purple-600 focus:ring-purple-600 border-gray-300 rounded" />
                    </div>

                    <h2 class="text-xl mt-6 font-semibold mb-2">請求先住所</h2>
                    <div class="grid grid-cols-2 gap-3 mb-3">
                        <div>
                            <x-text-input type="select" name="billing[country_code]"
                                x-model="billingAddress.country_code"
                                class="w-full focus:border-purple-600 focus:ring-purple-600 border-gray-300 rounded">
                                <option value="">国 • 地域を選択してください</option>
                                <template x-for="country of countries" :key="country.code">
                                    <option :selected="country.code === billingAddress.country_code"
                                        :value="country.code" x-text="country.name"></option>
                                </template>
                            </x-text-input>
                        </div>
                        <div>
                            <x-text-input type="text" name="billing[zipcode]" x-model="billingAddress.zipcode"
                                placeholder="郵便番号"
                                class="w-full focus:border-purple-600 focus:ring-purple-600 border-gray-300 rounded" />
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-3 mb-3">
                        <div>
                            <template x-if="billingCountryStates">
                                <x-text-input type="select" name="billing[state]" x-model="billingAddress.state"
                                    class="w-full focus:border-purple-600 focus:ring-purple-600 border-gray-300 rounded">
                                    <option value="">都道府県を選択してください</option>
                                    <template x-for="[code, state] of Object.entries(billingCountryStates)"
                                        :key="code">
                                        <option :selected="code === billingAddress.state" :value="code"
                                            x-text="state"></option>
                                    </template>
                                </x-text-input>
                            </template>
                            <template x-if="!billingCountryStates">
                                <x-text-input type="text" name="billing[state]" x-model="billingAddress.state"
                                    placeholder="都道府県"
                                    class="w-full focus:border-purple-600 focus:ring-purple-600 border-gray-300 rounded" />
                            </template>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-3 mb-3">
                        <div>
                            <x-text-input type="text" name="billing[city]" x-model="billingAddress.city"
                                placeholder="市町村"
                                class="w-full focus:border-purple-600 focus:ring-purple-600 border-gray-300 rounded" />
                        </div>
                        <div>
                            <x-text-input type="text" name="billing[address1]" x-model="billingAddress.address1"
                                placeholder="丁目・番地・号"
                                class="w-full focus:border-purple-600 focus:ring-purple-600 border-gray-300 rounded" />
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-3 mb-3">
                        <div>
                            <x-text-input type="text" name="billing[address2]" x-model="billingAddress.address2"
                                placeholder="建物名"
                                class="w-full focus:border-purple-600 focus:ring-purple-600 border-gray-300 rounded" />
                        </div>
                    </div>

                    <div class="flex justify-between mt-6 mb-2 items-center">
                        <h2 class="text-xl font-semibold">配送先住所</h2>
                        <label for="sameAsBillingAddress" class="text-gray-700">
                            <input @change="$event.target.checked ? shippingAddress = {...billingAddress} : ''"
                                id="sameAsBillingAddress" type="checkbox"
                                class="text-purple-600 focus:ring-purple-600 mr-2">請求先住所と同じ
                        </label>
                    </div>
                    <div class="grid grid-cols-2 gap-3 mb-3">
                        <div>
                            <x-text-input type="select" name="shipping[country_code]"
                                x-model="shippingAddress.country_code" x-on:change="shippingAddress.state = ''"
                                class="w-full focus:border-purple-600 focus:ring-purple-600 border-gray-300 rounded">
                                <option value="">国 • 地域を選択してください</option>
                                <template x-for="country of countries" :key="country.code">
                                    <option :selected="country.code === shippingAddress.country_code"
                                        :value="country.code" x-text="country.name"></option>
                                </template>
                            </x-text-input>
                        </div>
                        <div>
                            <x-text-input type="text" name="shipping[zipcode]" x-model="shippingAddress.zipcode"
                                placeholder="郵便番号"
                                class="w-full focus:border-purple-600 focus:ring-purple-600 border-gray-300 rounded" />
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-3 mb-3">
                        <div>
                            <template x-if="shippingCountryStates">
                                <x-text-input type="select" name="shipping[state]" x-model="shippingAddress.state"
                                    class="w-full focus:border-purple-600 focus:ring-purple-600 border-gray-300 rounded">
                                    <option selected="code !== shippingAddress.state" value="">都道府県を選択してください
                                    </option>
                                    <template x-for="[code, state] of Object.entries(shippingCountryStates)"
                                        :key="code">
                                        <option :selected="code === shippingAddress.state" :value="code"
                                            x-text="state"></option>
                                    </template>
                                </x-text-input>
                            </template>
                            <template x-if="!shippingCountryStates">
                                <x-text-input type="text" name="shipping[state]" x-model="shippingAddress.state"
                                    placeholder="都道府県"
                                    class="w-full focus:border-purple-600 focus:ring-purple-600 border-gray-300 rounded" />
                            </template>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-3 mb-3">
                        <div>
                            <x-text-input type="text" name="shipping[city]" x-model="shippingAddress.city"
                                placeholder="市町村"
                                class="w-full focus:border-purple-600 focus:ring-purple-600 border-gray-300 rounded" />
                        </div>
                        <div>
                            <x-text-input type="text" name="shipping[address1]" x-model="shippingAddress.address1"
                                placeholder="丁目・番地・号"
                                class="w-full focus:border-purple-600 focus:ring-purple-600 border-gray-300 rounded" />
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-3 mb-3">
                        <div>
                            <x-text-input type="text" name="shipping[address2]" x-model="shippingAddress.address2"
                                placeholder="建物名"
                                class="w-full focus:border-purple-600 focus:ring-purple-600 border-gray-300 rounded" />
                        </div>
                    </div>

                    <x-button class="w-full">更新</x-button>
                </form>
            </div>

            <div class="bg-white p-3 shadow rounded-lg">
                <form action="{{ route('profile_password.update') }}" method="post">
                    @csrf
                    <h2 class="text-xl font-semibold mb-2">パスワード更新</h2>
                    <div class="mb-3">
                        <x-text-input type="password" name="old_password" placeholder="現在のパスワード"
                            class="w-full focus:border-purple-600 focus:ringt-purple-600 border-gray-300 rounded" />
                        <x-input-error :messages="$errors->get('old_password')" class="mt-2" />

                    </div>
                    <div class="mb-3">
                        <x-text-input type="password" name="new_password" placeholder="新しいパスワード"
                            class="w-full focus:border-purple-600 focus:ringt-purple-600 border-gray-300 rounded" />
                        <x-input-error :messages="$errors->get('new_password')" class="mt-2" />
                    </div>
                    <div class="mb-3">
                        <x-text-input type="password" name="new_password_confirmation" placeholder="新しいパスワード確認"
                            class="w-full focus:border-purple-600 focus:ringt-purple-600 border-gray-300 rounded" />
                        <x-input-error :messages="$errors->get('new_password_confirmation')" class="mt-2" />
                    </div>
                    <x-button>更新</x-button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
