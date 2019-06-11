<?php

namespace App\Exceptions;

use App\Enums\Logs\EventType;
use App\Enums\Logs\ExceptionType;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Illuminate\Session\TokenMismatchException;
use Italia\SPIDAuth\Exceptions\SPIDLoginException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Base exceptions handler.
 */
class Handler extends ExceptionHandler
{
    /**
     * Report or log an exception.
     *
     * @param Exception $exception the raised exception
     *
     * @throws Exception if an error occurred during reporting
     */
    public function report(Exception $exception): void
    {
        if ($exception instanceof HttpException) {
            $user = auth()->check() ? 'User ' . auth()->user()->uuid : 'Anonymous user';
            $statusCode = $exception->getStatusCode();
            switch (true) {
                case $statusCode < 500:
                    logger()->info(
                        'A client error (status code: ' . $statusCode . ') occurred [' . request()->url() . ' visited by ' . $user . '].',
                        [
                            'event' => EventType::EXCEPTION,
                            'type' => ExceptionType::HTTP_CLIENT_ERROR,
                        ]
                    );
                    break;
                default:
                    logger()->error(
                        'A server error (status code: ' . $statusCode . ') occurred [' . request()->url() . ' visited by ' . $user . '].',
                        [
                            'event' => EventType::EXCEPTION,
                            'type' => ExceptionType::SERVER_ERROR,
                        ]
                    );
            }
        }

        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param Request $request the request
     * @param Exception $exception the raised exception
     *
     * @return \Illuminate\Http\Response the response
     */
    public function render($request, Exception $exception)
    {
        if ($exception instanceof NotFoundHttpException ||
            $exception instanceof ModelNotFoundException ||
            $exception instanceof MethodNotAllowedHttpException) {
            return response()->view('errors.404', ['not_found_path' => request()->path()]);
        }

        if ($exception instanceof TokenMismatchException) {
            return redirect()->home()->withMessage(['warning' => __('ui.session_expired')]);
        }

        if ($exception instanceof SPIDLoginException) {
            return redirect()->home()->withMessage(['error' => __('auth.spid_failed')]);
        }

        return parent::render($request, $exception);
    }

    /**
     * Get the context variables for logging.
     *
     * @return array the context variables
     */
    protected function context(): array
    {
        $context = [
            'event' => EventType::EXCEPTION,
            'type' => ExceptionType::GENERIC,
        ];
        if (auth()->check()) {
            $context['user'] = auth()->user()->uuid;
        }

        return $context;
    }
}
