@props([
  'orderStatus',
])

@php
  $statusText = '';
  $statusClass = '';

  switch ($orderStatus) {
    case 'unpaid':
      $statusText = '未払い';
      $statusClass = 'bg-gray-400';
      break;
    case 'pending':
      $statusText = '処理中';
      $statusClass = 'bg-yellow-300';
      break;
    case 'paid':
      $statusText = '支払済み';
      $statusClass = 'bg-emerald-500';
      break;
    case 'shipped':
      $statusText = '発送済み';
      $statusClass = 'bg-orange-400';
      break;
    case 'completed':
      $statusText = '完了';
      $statusClass = 'bg-emerald-500';
      break;
    case 'canceled':
      $statusText = 'キャンセル';
      $statusClass = 'bg-red-500';
      break;
  }
@endphp

<span class="text-white p-1 rounded {{ $statusClass }}">
  {{ $statusText }}
</span>
