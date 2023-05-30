@props(['disabled' => false, 'errors'])

<?php
  $errorClasses = 'border-red-600 focus:border-red-600 ring-1 ring-red-600 focus:ring-red-600';
  $defaulClasses = '';
  $successClasses = 'border-emerald-500 focus:border-emerald-500 ring-1 ring-emerald-500 focus:ring-emerald-500'
?>
<input {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => 'border-2 border-gray-300 focus:border-purple-500 focus:outline-none focus:ring-purple-500 rounded-md w-full py-3 px-2 ' .
($errors->has($attributes['name']) ? $errorClasses : (old($attributes['name']) ? $successClasses : $defaulClasses))
]) !!}>
