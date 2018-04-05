<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Illuminate\Session\TokenMismatchException;

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

        if ($exception instanceof HttpException && $exception->getStatusCode() == 403) {
            $user = auth()->check() ? 'User '.auth()->user()->getInfo() : 'Anonymous user';
            logger()->warning($user.' requested an unauthorized resource [' . $request->url() . '].');
        }

        if ($exception instanceof TokenMismatchException) {
            return redirect()->home()->withMessage(['warning' => __('ui.session_expired')]);
        }

        return parent::render($request, $exception);
    }
}
