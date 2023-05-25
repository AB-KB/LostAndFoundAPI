<?php

namespace App\Http\Controllers\API;

use App\Exceptions\InvalidDataGivenException;
use App\Exceptions\ItemNotFoundException;
use App\Http\Controllers\AppBaseController;
use App\Http\Requests\API\LoginUserRequest;
use App\Http\Requests\API\RegisterUserRequest;
use App\Models\User;
use App\Models\Village;
use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends AppBaseController
{

    public function register(RegisterUserRequest $request)
    {


        try {

            $name = $request->name;
            $email = $request->email;
            $password = bcrypt($request->password);
            $village_id = $request->village_id;
            $phone_number = $request->phone_number;


            $emailExists = User::where("email", $email)->exists();
            if ($emailExists) {

                throw new InvalidDataGivenException(__("Email already exists"));
            }

            $villageExists = Village::where("id", $village_id)->exists();
            if (!$villageExists) {

                throw new ItemNotFoundException(__("Village not found"));
            }

            $phoneExists = User::where("phone_number", $phone_number)->exists();
            if ($phoneExists && !is_null($phone_number)) {

                throw new InvalidDataGivenException(__("Phone number already exists"));
            }


            $user = User::create([
                "name" => $name,
                "email" => $email,
                "password" => $password,
                "phone_number" => $phone_number,
                "village_id" => $village_id,
            ]);

            return $this->sendResponse([
                "user" => $user->only(["id", "name"])
            ], __("User :name successfully created", ["name" => $user->name]));
        } catch (Exception $e) {

            return $this->sendExceptionError($e);
        }
    }

    public function login(LoginUserRequest $request)
    {

        try {
            $username = $request->username;
            $password = $request->password;


            /** @var User */
            $userQuery = User::query();
            if (isValidEmailAddress($username)) {
                $userQuery->where("email", $username);
            } else {
                $userQuery->where("phone_number", $username);
            }

            $user = $userQuery->first();
            if (is_null($user)) {

                return $this->sendError(__("Wrong credentials"), Response::HTTP_UNAUTHORIZED);
            }

            $matchPassword = Hash::check($password, $user->password);
            if (!$matchPassword) {

                return $this->sendError(__("Wrong credentials"),  Response::HTTP_UNAUTHORIZED);
            }

            $expiresIn = now()->addMonth();
            $token = $user->createToken("ApiToken", ["*"], $expiresIn);

            return $this->sendResponse([
                "token" => $token->plainTextToken,
                "tokenType" => "Bearer",
                "expiresIn" => $expiresIn,
            ], __("Login was successful"));
        } catch (Exception $e) {

            return $this->sendExceptionError($e);
        }
    }

    public function logout(){

        request()->user()->currentAccessToken()->delete();

        return $this->sendSuccess(__("Logout success"));
    }
}
