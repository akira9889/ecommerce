<?php

namespace App\Http\Controllers\Api;

use App\Enums\ProfileType;
use App\Enums\UserStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\UpdateUserRequest;
use Illuminate\Http\Response;
use App\Http\Resources\UserResource;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $search = request('search', false);
        $perPage = request('per_page', 10);
        $sortField = request('sort_field', 'updated_at');
        $sortDirection = request('sort_direction', 'desc');

        $users = User::query()
            ->with('adminProfile')
            ->where('is_admin', true);

        if ($search) {
            $users->whereHas('adminProfile', function ($query) use ($search) {
                $query->whereRaw("CONCAT(last_kana, first_kana) LIKE ?", ["%{$search}%"]);
            });
        }


        if ($sortField === 'name') {
            $users = $users->addSelect(['sort_name' => Profile::selectRaw("CONCAT(last_kana, first_kana)")
                ->whereColumn('users.id', 'profiles.user_id')
                ->where('type', 'admin')
                ->limit(1)])
                ->orderBy('sort_name', $sortDirection);
        } elseif ($sortField === 'updated_at') {
            $users = $users->addSelect(['profile_updated_at' => Profile::select("updated_at")
                ->whereColumn('users.id', 'profiles.user_id')
                ->where('type', 'admin')
                ->limit(1)])
                ->orderBy('profile_updated_at', $sortDirection);
        } else {
            $users = $users->orderBy($sortField, $sortDirection);
        }

        $users = $users->paginate($perPage);

        return UserResource::collection($users);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\UserRequest  $request
     * @return \Illuminate\Http\Response
     */

    public function store(CreateUserRequest $request)
    {
        $data = $request->validated();

        DB::beginTransaction();

        try {
            $userData = [];
            $userData['email'] = $data['email'];
            $userData['email_verified_at'] = date('Y-m-d H:i:s');
            $userData['password'] = Hash::make($data['password']);
            $userData['status'] = UserStatus::Active->value;
            $userData['is_admin'] = true;

            $user = User::create($userData);
            unset($data['email']);
            unset($data['password']);

            $data['user_id'] = $user->id;
            $data['type'] = ProfileType::Admin->value;

            Profile::create($data);
            $data['type'] = ProfileType::Customer->value;
            Profile::create($data);

            DB::commit();

            return new UserResource($user);
        } catch (\Exception $e) {
            DB::rollback();

            return response()->json(
                [
                    'errors' => [
                        'network' => 'データの通信に失敗しました'
                    ]
                ],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UserRequest  $request
     * @param  \App\Models\User  $product
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        $data = $request->validated();

        $profileData = [];
        $profileData['first_name'] = $data['first_name'];
        $profileData['last_name'] = $data['last_name'];
        $profileData['first_kana'] = $data['first_kana'];
        $profileData['last_kana'] = $data['last_kana'];

        $user->adminProfile->update($profileData);

        $userData['email'] = $data['email'];
        if (!empty($data['password'])) {
            $userData['password'] = Hash::make($data['password']);
        }

        $user->update($userData);

        return new UserResource($user);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        $user->delete();

        return response()->noContent();
    }
}
