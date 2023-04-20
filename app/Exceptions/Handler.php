<?php

namespace App\Exceptions;

use App\Constants\HttpStatus;
use App\Traits\Json;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Handler extends ExceptionHandler
{
    use Json;

    protected $request;

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
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
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $exception)
    {
        $this->request = $request;
        if ($request->isJson() || $request->is('api/*')){
            // 路由404异常监听
            if($exception instanceof NotFoundHttpException){
                return $this->errorJson("路由{{$request->path()}}不存在！", 404);
            }

            // 控制器不存在
            if ($exception instanceof BindingResolutionException){
                return $this->setJsonReturn($exception);
            }

            // 模型不存在
            if ($exception instanceof ModelNotFoundException){
                return $this->setJsonReturn($exception);
            }

            // 验证器类的错误监听
            if($exception instanceof ValidationException){
                return $this->errorJson($exception->validator->errors()->first());
            }

            // 路由的请求方式是否被支持
            if ($exception instanceof MethodNotAllowedHttpException){
                return $this->setJsonReturn($exception);
            }

            // 自定义Exception类的错误监听
            if($exception instanceof Exception){
                return $this->setJsonReturn($exception);
            }

            // ErrorException类的监听
            if($exception instanceof \ErrorException){
                return $this->setJsonReturn($exception);
            }

            // QueryException
            if ($exception instanceof QueryException){
                return $this->setJsonReturn($exception);
            }
            // Exception类的监听
            if($exception instanceof \Exception){
                return $this->setJsonReturn($exception);
            }
        }

        return parent::render($request, $exception);
    }

    private function setJsonReturn($exception, bool $send_mail = true)
    {
        $APP_DEBUG = env('APP_DEBUG');

        // 设置HTTP的状态码
        $http_status = method_exists($exception, 'getStatusCode')
            ? $exception->getStatusCode()
            : (method_exists($exception, 'getCode') ? $exception->getCode() : HttpStatus::BAD_REQUEST);
        // $http_status == '42S22' 数据表字段异常
        if ($http_status == 0 || is_string($http_status)){
            $http_status = HttpStatus::BAD_REQUEST;
        }
        $message = $exception->getMessage();
        if ($http_status == 23000){
            $http_status = HttpStatus::BAD_REQUEST;
            $message = '请检测SQL语句是否某些字段不可为null';
        }else if ($http_status == 1049){
            $http_status = HttpStatus::BAD_REQUEST;
            $message = '请检测数据库是否创建！';
        }

        // 发送报警邮件
        if ($send_mail){
        }

        return $this->errorJson($message, $http_status, [], $APP_DEBUG ? [
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'code' => $exception->getCode(),
            'http_status' => (int)$http_status
        ] : []);
    }
}
