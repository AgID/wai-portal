<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Session\TokenMismatchException;
use Italia\SPIDAuth\Exceptions\SPIDLoginException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;

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
     * @param  Exception $exception
     * @return void
     * @throws Exception
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Exception  $exception
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
            $user = auth()->check() ? 'User '.auth()->user()->getInfo() : 'Anonymous user';
            $statusCode = $exception->getStatusCode();
            switch($statusCode) {
                case 403:
                    logger()->warning($user.' requested an unauthorized resource [' . $request->url() . '].');
                    break;
                default:
                    logger()->warning('A server error (status code: ' . $statusCode . ') occurred [' . $request->url() . ' visited by ' . $user . '].');
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
}
