<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class RefreshTokenApiController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            $user->tokens()->delete();

            $accessToken = $user->createToken(config('app.name'))->plainTextToken;

            return response()->json(
                [
                    'token_type' => 'Bearer',
                    'access_token' => $accessToken,
                    'message' => trans('auth.refresh_token')
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
