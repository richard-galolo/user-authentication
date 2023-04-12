<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Auth\CreateLoginRequest;
use App\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class AuthenticatedApiController extends Controller
{
    public function store(CreateLoginRequest $request): JsonResponse
    {
        try {
            if (! Auth::attempt($request->only('email', 'password'), $request->boolean('remember'))) {
                throw new \Exception(
                    trans('auth.failed'),
                    Response::HTTP_UNAUTHORIZED
                );
            }

            $user = $request->user();
            $accessToken = $user->createToken(config('app.name'))->plainTextToken;

            return response()->json(
                [
                    'data' => new UserResource($user),
                    'token_type' => 'Bearer',
                    'access_token' => $accessToken,
                    'message' => trans('auth.login')
                ],
                Response::HTTP_OK
            );
        } catch (\Exception $exception) {
            throw new \Exception(
                $exception->getMessage(),
                $exception->getCode()
            );
        }
    }

    public function destroy(Request $request): Response
    {
        try {
            $request->user()->currentAccessToken()->delete();

            return response()->noContent();
        } catch (\Exception $exception) {
            throw new \Exception(
                $exception->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
