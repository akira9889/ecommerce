<x-app-layout>
    <div class="container lg:w-2/3 xl:w-2/3 mx-auto">
        <h1 class="text-3xl font-bold mb-6">注文履歴</h1>

        <div class="bg-white p-3 rounded-md shadow-md">
            <table class="table table-auto w-full">
                <thead class="border-b-2">
                    <tr class="text-left">
                        <th>注文</th>
                        <th>日付</th>
                        <th>注文状況</th>
                        <th>注文合計</th>
                        <th>商品数</th>
                        <th class="w-64">支払い</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($orders as $order)
                        <tr class="border-b">
                            <td class="py-1 px-2">
                                <a href="{{ route('order.view', $order) }}" class="text-purple-600 hover:text-purple-500">
                                    #{{ $order->id }}
                                </a>
                            </td>
                            <td class="py-1 px-2">{{ $order->created_at }}</td>
                            <td class="py-1 px-2">
                                <small><x-order-status :order-status="$order->status" /></small>
                            </td>
                            <td class="py-1 px-2">¥{{ $order->total_price }}</td>
                            <td class="py-1 px-2">{{ $order->items->sum('quantity') }}個</td>
                            <td class="flex gap-2 w-[150px]">
                                @if ($order->isunPaid())
                                <form action="{{ route('cart.checkout-order', $order) }}" method="post">
                                    @csrf
                                    <button class="btn-primary py-1 px-2 flex items-center" type="submit">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-1" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                        </svg>
                                        支払う
                                    </button>
                                </form>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-3">
            {{ $orders->links(); }}
        </div>
    </div>
</x-app-layout>
