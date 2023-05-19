<?php

use Illuminate\Support\Facades\Validator;

function isValidEmailAddress(string $data): bool
{

    $validation = Validator::make(["username" => $data], [
        "username" => ["required", "email:rfc,dns"],
    ]);

    return $validation->fails() === false;
}
