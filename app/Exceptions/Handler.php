<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Laravel\Lumen\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        AuthorizationException::class,
        HttpException::class,
        ModelNotFoundException::class,
        ValidationException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $e
     * @return void
     */
    public function report(Exception $e)
    {
        parent::report($e);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $e
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $e)
    {
        if ($e instanceof HttpResponseException) {
            return response()->json([
                'ack' => '05',
                'msg' => 'INTERNAL SERVER ERROR'
            ], 500);
        } elseif ($e instanceof NotFoundHttpException) {
            return response()->json([
                'ack' => '05',
                'msg' => 'PAGE NOT FOUND'
            ], 404);
        } elseif ($e instanceof ModelNotFoundException) {
            return response()->json([
                'ack' => '05',
                'msg' => 'RESOURCE NOT FOUND'
            ], 404);
        } elseif ($e instanceof AuthorizationException) {
            return response()->json([
                'ack' => '05',
                'msg' => 'UNAUTHORIZED'
            ], 403);
        } elseif ($e instanceof ValidationException && $e->getResponse()) {
            return response()->json([
                'ack' => '05',
                'msg' => 'INVALID PARAMETER',
                'err' => $e->errors()
            ], 422);
        }

        return parent::render($request, $e);
    }
}
