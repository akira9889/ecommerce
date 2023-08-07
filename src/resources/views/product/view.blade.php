<x-app-layout>
    <div x-data="productItem({{ json_encode([
        'id' => $product->id,
        'image' => $product->image,
        'title' => $product->title,
        'price' => $product->price,
        'quantity' => 1,
        'addToCartUrl' => route('cart.add', $product),
    ]) }})" class="container mx-auto my-12">
        <div class="grid gap-6 grid-cols-1 lg:grid-cols-5">
            <div class="lg:col-span-3">
                <div x-data="{
                    images: ['{{ $product->image }}'],
                    activeImage: null,
                    prev() {
                        let index = this.images.indexOf(this.activeImage);
                        if (index === 0)
                            index = this.images.length;
                        this.activeImage = this.images[index - 1];
                    },
                    next() {
                        let index = this.images.indexOf(this.activeImage);
                        if (index === this.images.length - 1)
                            index = -1;
                        this.activeImage = this.images[index + 1];
                    },
                    init() {
                        this.activeImage = this.images.length > 0 ? this.images[0] : null
                    }
                }">
                    <div class="relative bg-white">
                        <template x-for="image in images">
                            <div x-show="activeImage === image" class="aspect-w-3 aspect-h-2">
                                <img :src="image" alt="" class="w-auto mx-auto" />
                            </div>
                        </template>
                        <a @click.prevent="prev"
                            class="cursor-pointer bg-black/30 text-white absolute left-0 top-1/2 -translate-y-1/2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                            </svg>
                        </a>
                        <a @click.prevent="next"
                            class="cursor-pointer bg-black/30 text-white absolute right-0 top-1/2 -translate-y-1/2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                    </div>
                    <div class="border-2 border-gray-800"></div>
                    <div class="flex">
                        <template x-for="image in images">
                            <a @click.prevent="activeImage = image"
                                class="cursor-pointer w-[80px] h-[80px] border border-gray-300 hover:border-purple-500 flex items-center justify-center"
                                :class="{ 'border-purple-600': activeImage === image }">
                                <img :src="image" alt="" class="w-auto max-auto h-full" />
                            </a>
                        </template>
                    </div>
                </div>
            </div>
            <div class="lg:col-span-2">
                <h1 class="text-4xl font-semibold mb-6">
                    {{ $product->title }}
                </h1>
                <div class="text-3xl font-bold mb-6">{{ $product->price }} 円</div>
                <div class="flex items-center mb-5">
                    <label for="quantity" class="block font-bold mr-4">
                        数量
                    </label>
                    <input x-model="product.quantity" type="number" name="quantity" min="1"
                        class="w-32 focus:border-purple-500 focus:outline-none rounded" />
                    <div class="ml-3 flex">
                        <button @click="decrementQuantity"
                            class="px-4 py-2 bg-white font-bold border border-black text-xs border-r-0 hover:bg-gray-100">ー</button>
                        <button @click="incrementQuantity"
                            class="px-4 py-2 bg-white font-bold border border-black hover:bg-gray-100">+</button>
                    </div>
                </div>
                <button @click="addToCart()" class="btn-primary py-4 text-lg flex justify-center min-w-0 w-full mb-6">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    カートに追加
                </button>
                <div class="mb-6" x-data="{ expanded: false }">
                    <div x-show="expanded" x-collapse.min.120px class="text-gray-500 wysiwyg-content">
                        {{ $product->description }}
                    </div>
                    <p class="text-right">
                        <a @click="expanded = !expanded" href="javascript:void(0)"
                            class="text-purple-500 hover:text-purple-700" x-text="expanded ? '文を折りたたむ' : '全文表示'"></a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
