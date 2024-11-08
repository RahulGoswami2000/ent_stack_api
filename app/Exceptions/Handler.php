<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use App\Mail\ExceptionOccured;
/**
 * This is your application's exception handler
 *
 * Class Handler
 */
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
     * @param  Throwable  $exception
     * @return void
     *
     * @throws \Exception
     */
    public function report(Throwable $exception)
    {

       /* if ($this->shouldReport($exception)) {
            $this->sendEmail($exception); // sends an email
        }*/

        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Throwable  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Throwable $exception)
    {
        return parent::render($request, $exception);
    }

    public function sendEmail(Throwable $exception)
    {
        try {
            $html = ExceptionHandler::convertExceptionToResponse($exception)->getContent();
            $url = (!empty(\Request::fullUrl())) ? \Request::fullUrl() : "From console";
            \Mail::to('ishan.siliconithub@gmail.com')->send(new ExceptionOccured($html, 'Exception url : ' . $url));
        } catch (Exception $ex) {
            Log::error($ex);
        }
    }
}
