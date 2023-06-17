@props(['disabled' => false, 'errors', 'type' => 'text'])

<?php
  $errorClasses = 'border-red-600 focus:border-red-600 ring-1 ring-red-600 focus:ring-red-600';
  $defaulClasses = '';
  $successClasses = 'border-emerald-500 focus:border-emerald-500 ring-1 ring-emerald-500 focus:ring-emerald-500';

  $attributeName = preg_replace('/(\w+)\[(\w+)]/', '$1.$2', $attributes['name']);
?>
<div>
  @if ($type === 'select')
    <select {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => 'border-2 border-gray-300 focus:border-purple-500 focus:outline-none focus:ring-purple-500 rounded-md w-full py-3 px-2 ' .
    ($errors->has($attributeName) ? $errorClasses : (old($attributeName) ? $successClasses : $defaulClasses))
    ]) !!}>
      {{ $slot }}
    </select>
  @else
    <input {{ $disabled ? 'disabled' : '' }} type="{{ $type }}" {!! $attributes->merge(['class' => 'border-2 border-gray-300 focus:border-purple-500 focus:outline-none focus:ring-purple-500 rounded-md w-full py-3 px-2 ' .
    ($errors->has($attributeName) ? $errorClasses : (old($attributeName) ? $successClasses : $defaulClasses))
    ]) !!}>
  @endif
</div>
