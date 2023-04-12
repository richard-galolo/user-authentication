<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->renderable(function (Throwable $exception, $request) {
            return $this->handleApiException($request, $exception);
        });
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param Request $request
     * @param Throwable $exception
     * @return Response|JsonResponse
     */
    private function handleApiException($request, Throwable $exception)
    {
        if ($request->wantsJson()) {
            $response = [];
            $code = ( $exception->getCode() == 0 || !is_numeric($exception->getCode()) )
                ? 500
                : $exception->getCode();

            if ($exception instanceof ValidationException) {
                return $this->convertValidationExceptionToResponse($exception, $request);
            } else {
                $default = [
                    'code' => $code,
                    'detail' => $exception->getMessage() ?:
                        'The requested URI is invalid, or the resource does not exist.'
                ];

                $response['errors'] = [$default];
            }

            return response()->json($response, $code);
        }
    }
}
