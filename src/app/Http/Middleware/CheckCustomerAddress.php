<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckCustomerAddress
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle($request, Closure $next)
    {
        $user = $request->user();
        $shipping = $user->profile->shippingAddress;
        $billing = $user->profile->billingAddress;

        if (!$shipping || !$billing) {
            session()->flash('error', 'プロフィールから配達先、請求先住所を設定してください。');
            return back();
        }

        return $next($request);
    }
}
