<?php

namespace App\Http\Controllers\Auth;

use App\Enums\ProfileType;
use App\Enums\UserStatus;
use App\Http\Controllers\Controller;
use App\Http\Helpers\Cart;
use App\Models\Profile;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'first_kana' => ['required', 'string', 'max:255'],
            'last_kana' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        DB::beginTransaction();

        try {
            $user = User::create([
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'status' => UserStatus::Active->value,
            ]);

            event(new Registered($user));

            $customer = new Profile();
            $customer->user_id = $user->id;
            $customer->first_name = $request->first_name;
            $customer->first_kana = $request->first_kana;
            $customer->last_name = $request->last_name;
            $customer->last_kana = $request->last_kana;
            $customer->type = ProfileType::Customer->value;
            $customer->save();

            Auth::login($user);

            Cart::moveCartItemsIntoDb();

            DB::commit();

            return redirect(RouteServiceProvider::HOME);
        } catch (\Exception $e) {
            DB::rollback();
        }
    }
}
