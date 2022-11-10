<?php

namespace App\Exceptions;


use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
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
     * @param  \Exception $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Exception $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
        if ($exception instanceof MethodNotAllowedHttpException)
        {
            abort(404, 'Unauthorized action.');
        }
        if (!empty($exception)) {
            if ($exception instanceof NotFoundHttpException) {
                \Log::channel('expections')->error([
                        'file' => $exception->getFile(),
                        'messgae' => $exception->getMessage(),
                        'line' => $exception->getLine(),
                        'url' => $request->url(),
                        'request' => $request->all(),
                    ]
                );
            }
        }
        return parent::render($request, $exception);
    }

    protected function unauthenticated($request, AuthenticationException $exception)
    {
        if ($request->expectsJson()) {
            return response()->json(['version'=>'NA','result' => "999", 'message' => "Unauthenticated", 'data' => []], 200);
        }
        $gurad = array_get($exception->guards(), 0);
        switch ($gurad) {
            case "admin":
                $login = 'admin.login';
                break;
            case "merchant":
                $login = '404';
                break;
            case "business-segment":
                $login = '404';
                break;
            case "hotel":
                $login = '404';
                break;
            case "franchise":
                $login = '404';
                break;
            case "corporate":
                $login = '404';
                break;
            case "api":
                return response()->json(['version'=>'NA','result' => "999", 'message' => "Unauthenticated", 'data' => []], 200);
                break;
            case "api-driver":
                return response()->json(['version'=>'NA','result' => "999", 'message' => "Unauthenticated", 'data' => []], 200);
                break;
            case "business-segment-api":
                return response()->json(['version'=>'NA','result' => "999", 'message' => "Unauthenticated", 'data' => []], 401);
                break;
            default:
                $login = 'login';
                break;
        }
        return redirect()->guest(route($login));
    }
}
