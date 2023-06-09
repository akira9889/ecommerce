<x-app-layout>
    <div class="container lg:w-2/3 xl:w-2/3 mx-auto">
        <h1 class="text-3xl font-bold mb-6">注文の詳細</h1>

        <div class="bg-white p-3 rounded-md shadow-md">
            <div>
                <table class="table-sm">
                    <tbody>
                        <tr>
                            <td class="font-bold">注文番号</td>
                            <td>{{ $order->id }}</td>
                        </tr>
                        <tr>
                            <td class="font-bold">注文日</td>
                            <td>{{ $order->created_at }}</td>
                        </tr>
                        <tr>
                            <td class="font-bold">支払い状況</td>
                            <td>
                                <span
                                    class="text-white p-1 rounded {{ $order->isPaid() ? 'bg-emerald-500' : 'bg-gray-400' }}">{{ $order->isPaid() ? '支払い済み' : '未払い' }}</span>
                            </td>
                        </tr>
                        <tr>
                            <td class="font-bold">商品の小計：</td>
                            <td>￥{{ $order->total_price }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <hr class="my-5" />

            <!-- Order Items -->
            @foreach ($order->items as $item)
                 <!-- Order Item -->
                <div class="flex flex-col sm:flex-row items-center gap-4">
                    <a href="{{ route('product.view', $item->product) }}"
                       class="w-36 h-32 flex items-center justify-center overflow-hidden">
                        <img src="{{$item->product->image}}" class="object-cover" alt=""/>
                    </a>
                    <div class="flex flex-col justify-between grow">
                        <div class="flex justify-between mb-3">
                            <h3>
                                {{$item->product->title}}
                            </h3>
                          </div>
                          <div class="flex flex-col sm:flex-row justify-between items-center">
                            <div class="flex items-center">数量: {{$item->quantity}}</div>
                            <span class="text-lg font-semibold"> ¥{{$item->unit_price}} </span>
                        </div>
                    </div>
                </div>
                <!--/ Order Item -->
                <hr class="my-3"/>
            @endforeach

            @if (!$order->isPaid())
                <form action="{{ route('cart.checkout-order', $order) }}" method="post">
                    @csrf
                    <button type="submit" class="btn-primary flex justify-center items-center w-full py-3 text-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-1" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                        </svg>
                        レジに進む
                    </button>
                </form>
            @endif
        </div>
    </div>

</x-app-layout>
