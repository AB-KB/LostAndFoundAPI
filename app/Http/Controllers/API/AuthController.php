<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\AppBaseController;
use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;

class AuthController extends AppBaseController
{

    public function register(RegisterUserRequest $request)
    {


    }

    public function login(LoginUserRequest $request)
    {

        $username = $request->username;
        $password = $request->password;

        /** @var User */
        $user = User::query()
            ->when(isValidEmailAddress($username, fn($qb) => $qb->where("email", $username)))
            ->when(!isValidEmailAddress($username, fn($qb) => $qb->where("phone_number", $username)))
            ->first();

        if (is_null($user)) {

            return $this->sendError(__("Wrong credentials", Response::HTTP_UNAUTHORIZED));
        }

        $matchPassword = Hash::check($password, $user->password);
        if (!$matchPassword) {

            return $this->sendError(__("Wrong credentials", Response::HTTP_UNAUTHORIZED));
        }

        $expiresIn = now()->addMonth();
        $token = $user->createToken("ApiToken", ["*"], $expiresIn);

        return $this->sendResponse([
            "token" => $token->plainTextToken,
            "tokenType" => "Bearer",
            "expiresIn" => $expiresIn,
        ], __("Login was successful"));
    }
}
