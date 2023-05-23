<?php

namespace App\Http\Controllers\API;

use App\Exceptions\InvalidDataGivenException;
use App\Exceptions\ItemNotFoundException;
use App\Http\Controllers\AppBaseController;
use App\Http\Requests\API\LoginUserRequest;
use App\Http\Requests\API\RegisterUserRequest;
use App\Models\User;
use App\Models\Village;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;

class AuthController extends AppBaseController
{

    public function register(RegisterUserRequest $request)
    {

        $name = $request->name;
        $email = $request->email;
        $password = bcrypt($request->password);
        $village_id = $request->village_id;

        $emailExists = User::where("email", $email)->exists();
        if($emailExists){

            throw new InvalidDataGivenException(__("Email already exists"));
        }

        $villageExists = Village::where("id", $village_id)->exists();
        if(!$villageExists){

            throw new ItemNotFoundException(__("Village not found"));
        }

        $user = User::create([
            "name"=> $name,
            "email"=> $email,
            "password"=> $password,
            "village_id"=> $village_id,
        ]);

        return $this->sendResponse([
            "user"=> $user->only(["id", "name"])
        ], __("User :name successfully created", ["name"=> $user->name]));

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
