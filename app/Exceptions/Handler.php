<?php

namespace App\Exceptions;

use Atk4\Core\Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Laravel\Lumen\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

class FixedHTMLRenderer extends \Atk4\Core\ExceptionRenderer\Html {
    public function publicProcessAll(): void
    {
        $this->processAll();
    }
    protected function parseStackTraceCall(array $call): array
    {
        $parsed = [
            'line' => (string) ($call['line'] ?? ''),
            'file' => (string) ($call['file'] ?? ''),
            'class' => $call['class'] ?? null,
            'object' => $call['object'] ?? null,
            'function' => $call['function'] ?? null,
            'args' => $call['args'] ?? [],
            'object_formatted' => null,
            'file_formatted' => null,
            'line_formatted' => null,
        ];

        try {
            $parsed['file_rel'] = $this->makeRelativePath($parsed['file']);
        } catch (Exception $e) {
            $parsed['file_rel'] = $parsed['file'];
        }

        # don't examine $object names, blows up in Lumen
//        if ($parsed['object'] !== null) {
//            $parsed['object_formatted'] = $parsed['object']->name ?? get_class($parsed['object']);
//        }

        return $parsed;
    }
    protected function makeRelativePath(string $path): string
    {
        if ($path === '' || ($pathReal = realpath($path)) === false) {
            return '';
        }

        $filePathArr = explode(\DIRECTORY_SEPARATOR, ltrim($pathReal, '/\\'));
        $vendorRootArr = explode(\DIRECTORY_SEPARATOR, ltrim($this->getVendorDirectory(), '/\\'));
        if ($filePathArr[0] !== $vendorRootArr[0]) {
            return $filePathArr;
        }

        array_pop($vendorRootArr); // assume parent directory as project directory
        while (isset($filePathArr[0]) && isset($vendorRootArr[0]) && $filePathArr[0] === $vendorRootArr[0]) {
            array_shift($filePathArr);
            array_shift($vendorRootArr);
        }

        return (count($vendorRootArr) > 0 ? str_repeat('../', count($vendorRootArr)) : '') . implode('/', $filePathArr);
    }
}

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
     * @param  \Throwable  $exception
     * @return void
     *
     * @throws \Exception
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $exception)
    {
        if ($exception instanceof \Atk4\Core\Exception) {
            $renderer = new FixedHTMLRenderer($exception);
            $renderer->publicProcessAll();
            return '<!DOCTYPE html>
<html lang="en">
  <head>
   <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/fomantic-ui/2.8.7/semantic.min.css"/>
  </head>
  <body>
   <div class="ui middle aligned grid container" style="min-height: 100%; padding: 2rem 0;">
    <div class="column">
     '.$renderer->output.'
    </div></div>
  </body>
</html>';
        }
        return parent::render($request, $exception);
    }
}
