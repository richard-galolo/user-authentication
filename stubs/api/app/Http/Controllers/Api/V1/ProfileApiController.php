<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Profile\UpdateProfileRequest;
use App\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ProfileApiController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        return response()->json(
            [
                'data' => new UserResource($request->user()),
                'message' => trans('auth.profile_retrieved')
            ],
            Response::HTTP_OK
        );
    }

    public function update(UpdateProfileRequest $request): JsonResponse
    {
        try {
            $request->user()->fill($request->validated());

            if ($request->user()->isDirty('email')) {
                $request->user()->email_verified_at = null;
            }

            $request->user()->save();

            return response()->json(
                [
                    'data' => new UserResource($request->user()),
                    'message' => trans('auth.profile_updated')
                ],
                Response::HTTP_OK
            );
        } catch (\Exception $exception) {
            throw new \Exception(
                $exception->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
