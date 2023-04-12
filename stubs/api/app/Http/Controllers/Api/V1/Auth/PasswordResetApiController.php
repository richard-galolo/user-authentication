<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Auth\CreatePasswordResetRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Password;

class PasswordResetApiController extends Controller
{
    public function store(CreatePasswordResetRequest $request): JsonResponse
    {
        try {
            $status = Password::sendResetLink(
                $request->only('email')
            );

            if ($status !== Password::RESET_LINK_SENT) {
                throw new \Exception(
                    "Please try again after " . config('auth.passwords.users.throttle') . ' seconds',
                    Response::HTTP_UNPROCESSABLE_ENTITY
                );
            }

            return response()->json(
                ['message' => trans('passwords.sent')],
                Response::HTTP_OK
            );
        } catch (\Exception $exception) {
            throw new \Exception(
                $exception->getMessage(),
                $exception->getCode()
            );
        }
    }
}
