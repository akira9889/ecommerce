<x-app-layout>
    <div class="container lg:w-2/3 xl:w-2/3 mx-auto">
        <h1 class="text-3xl font-bold mb-6">ショッピングカート</h1>
        @if (session('error'))
            <div class="w-full md:w-[400px] py-2 px-4 pb-4 mb-4 mx-auto bg-red-500 text-white">
                <p class="font-semibold">{{ session('error') }}</p>
                <p class="text-right underline text-xs"><a href="{{ route('profile') }}">プロフィール設定に進む→</a></p>
            </div>
        @endif
        <div x-data="{
            cartItems: {{ json_encode(
                $products->map(
                    fn($product) => [
                        'id' => $product->id,
                        'slug' => $product->slug,
                        'image' => $product->image,
                        'title' => $product->title,
                        'price' => $product->price,
                        'quantity' => $cartItems[$product->id]['quantity'],
                        'href' => route('product.view', $product),
                        'removeUrl' => route('cart.remove', $product),
                        'updateQuantityUrl' => route('cart.update-quantity', $product),
                    ],
                ),
            ) }},
            get cartTotal() {
                return this.cartItems.reduce((accum, next) => accum + next.price * next.quantity, 0)
            }
        }" class="bg-white p-4 rounded-lg shadow">
            <template x-if="cartItems.length">
                <!-- Cart Items -->
                <div>
                    <div>
                        <!-- Cart Item -->
                        <template x-for="product of cartItems" :key="product.id">
                            <div>
                                <div x-data="productItem(product)"
                                    class="w-full flex flex-col sm:flex-row items-center gap-4">
                                    <a :href="product.href"
                                        class="w-36 h-32 flex items-center justify-center overflow-hidden">
                                        <img :src="product.image" class="object-cover" alt="" />
                                    </a>
                                    <div class="flex-1 flex flex-col justify-between">
                                        <div class="flex justify-between mb-3">
                                            <h3 x-text="product.title"></h3>
                                            <span class="text-lg font-semibold">
                                                <span x-text=`${product.price}円`></span>
                                            </span>
                                        </div>
                                        <div class="flex justify-between items-center">
                                            <div class="flex items-center">
                                                数量:
                                                <input type="number" x-model="product.quantity"
                                                    @change="changeQuantity()" min="1"
                                                    class="ml-3 py-1 border-gray-200 focus:border-purple-600 focus:ring-purple-600 w-16" />
                                                <div class="ml-3 flex">
                                                    <button @click="handleDecrement"
                                                        class="px-4 py-2 bg-white font-bold border border-black text-xs border-r-0 hover:bg-gray-100">ー</button>
                                                    <button @click="handleIncrement"
                                                        class="px-4 py-2 bg-white font-bold border border-black hover:bg-gray-100">+</button>
                                                </div>
                                            </div>
                                            <a @click.prevent="removeItemFromCart()" href="#"
                                                class="text-purple-600 hover:text-purple-500">削除</a>
                                        </div>
                                    </div>
                                </div>
                                <hr class="my-5" />
                            </div>
                        </template>
                        <!-- Cart Item -->
                    </div>
                    <!--/ Cart Items -->

                    <div class="border-t border-gray-300 pt-4">
                        <div class="flex justify-between">
                            <span class="font-semibold">小計</span>
                            <span class="text-xl" x-text=`${cartTotal}円`></span>
                        </div>
                        <p class="text-gray-500 mb-6">
                            送料と税金は購入時に計算されます。
                        </p>
                        <form action="{{ route('cart.checkout') }}" method="post">
                            @csrf
                            <button type="submit" class="btn-primary w-full py-3 text-lg">
                                レジに進む
                            </button>
                        </form>
                    </div>
                </div>
            </template>
            <template x-if="!cartItems.length">
                <div class="text-center py-8 text-gray-500">お客様のカートに商品はありません。</div>
            </template>
        </div>
    </div>
</x-app-layout>
