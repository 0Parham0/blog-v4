<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Str;
use App\Traits\ApiResponses;
use Illuminate\Http\Request;
use App\Http\Requests\LoginRequest;
use App\Http\Controllers\Controller;
use App\Http\Requests\SignupRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class AuthController extends Controller
{
    use ApiResponses;

    public function login(LoginRequest $request)
    {
        if (!Auth::attempt($request->only('email', 'password'))) {
            return $this->error('Invalid Credentials', 401);
        }

        $user = User::firstWhere('email', $request->email);

        return $this->ok('Authenticated', [
            'token' => $user->createToken('API token for ' . $request->email, ['*'], now()->addWeek())->plainTextToken
        ]);
    }

    public function signup(SignupRequest $request)
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'email_verified_at' => now(),
            'password' => Hash::make($request->password),
            'remember_token' => Str::random(10),
        ]);

        Role::createOrFirst(['name' => 'author']);
        $user->assignRole('author');

        return $this->ok('Registered and authenticated.', [
            'token' => $user->createToken('API token for ' . $request->email, ['*'], now()->addWeek())->plainTextToken
        ]);
    }

    public function getUserInfo(Request $request)
    {
        return $this->ok('Authenticated', [
            'user' => [
                'name' => $request->user()->name,
                'email' => $request->user()->email
            ]
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return $this->ok('Token revoked');
    }
}
