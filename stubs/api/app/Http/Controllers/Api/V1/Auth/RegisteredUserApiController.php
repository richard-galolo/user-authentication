<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Auth\CreateRegisterUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class RegisteredUserApiController extends Controller
{
    public function store(CreateRegisterUserRequest $request): JsonResponse
    {
        try {
            $user = User::create($request->validated());

            $accessToken = $user->createToken(config('app.name'))->plainTextToken;

            event(new Registered($user));

            return response()->json(
                [
                    'data' => new UserResource($user),
                    'token_type' => 'Bearer',
                    'access_token' => $accessToken,
                    'message' => trans('auth.registered')
                ],
                Response::HTTP_CREATED
            );
        } catch (\Exception $exception) {
            throw new \Exception(
                $exception->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
