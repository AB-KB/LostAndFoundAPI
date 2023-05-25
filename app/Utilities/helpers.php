<?php

use App\Exceptions\ForbiddenActionException;
use App\Exceptions\InvalidDataGivenException;
use App\Exceptions\ItemNotFoundException;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

function isValidEmailAddress(string $data): bool
{

    $validation = Validator::make(["username" => $data], [
        "username" => ["required", "email:rfc,dns"],
    ]);

    return $validation->fails() === false;
}


/**
 *
 * Retrieve the http message and status code for a given exception
 *
 * @return array
 */

function getHttpMessageAndStatusCodeFromException(Exception $exception)
{
    return [
        $exception->getMessage(),
        match (get_class($exception)) {
            InvalidDataGivenException::class => Response::HTTP_BAD_REQUEST,
            ItemNotFoundException::class => Response::HTTP_NOT_FOUND,
            ForbiddenActionException::class => Response::HTTP_FORBIDDEN,
            default => Response::HTTP_INTERNAL_SERVER_ERROR
        },
        $exception->getCode(),
    ];
}
