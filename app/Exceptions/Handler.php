<?php

namespace App\Exceptions;

use App\Enums\Logs\EventType;
use App\Enums\Logs\ExceptionType;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Session\TokenMismatchException;
use Italia\SPIDAuth\Exceptions\SPIDLoginException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param Exception $exception
     *
     * @throws Exception
     *
     * @return void
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param \Illuminate\Http\Request $request
     * @param Exception $exception
     *
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
        if ($exception instanceof NotFoundHttpException ||
            $exception instanceof ModelNotFoundException ||
            $exception instanceof MethodNotAllowedHttpException) {
            return response()->view('errors.404', ['not_found_path' => request()->path()]);
        }

        if ($exception instanceof HttpException) {
            $user = auth()->check() ? 'User ' . auth()->user()->getInfo() : 'Anonymous user';
            $statusCode = $exception->getStatusCode();
            switch ($statusCode) {
                case 403:
                    logger()->warning(
                        $user . ' requested an unauthorized resource [' . $request->url() . '].',
                        [
                            'event' => EventType::EXCEPTION,
                            'type' => ExceptionType::UNAUTHORIZED_ACCESS,
                        ]
                    );
                    break;
                default:
                    logger()->warning(
                        'A server error (status code: ' . $statusCode . ') occurred [' . $request->url() . ' visited by ' . $user . '].',
                        [
                            'event' => EventType::EXCEPTION,
                            'type' => ExceptionType::GENERIC,
                        ]
                    );
                    // TODO: notify me!
            }
        }

        if ($exception instanceof TokenMismatchException) {
            return redirect()->home()->withMessage(['warning' => __('ui.session_expired')]);
        }

        if ($exception instanceof SPIDLoginException) {
            return redirect()->home()->withMessage(['error' => __('auth.spid_failed')]);
        }

        return parent::render($request, $exception);
    }

    protected function context(): array
    {
        $context = [
            'event' => EventType::EXCEPTION,
            'type' => ExceptionType::GENERIC,
        ];
        if (auth()->check()) {
            $context['user'] = auth()->user()->uuid;
        }

        return array_merge(
            parent::context(),
            $context
        );
    }
}
