<?php
// src/Controller/ExceptionController.php
namespace App\Controller;
use FOS\RestBundle\Util\ExceptionValueMap;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Log\DebugLoggerInterface;
use FOS\RestBundle\Controller\FOSRestController;
use Twig\Environment;
use Symfony\Bundle\TwigBundle\Controller\ExceptionController as TwigExceptionController;
use Symfony\Component\Debug\Exception\FlattenException;

/**
 * Custom ExceptionController that renders to json
 *     
 */
class ExceptionController extends TwigExceptionController
{
    /**
     * @var ExceptionValueMap
     */
    private $exceptionCodes;
    public function __construct(ExceptionValueMap $exceptionCodes, Environment $twig, $debug = null) {
        // , 
       $this->exceptionCodes = $exceptionCodes;
       $this->twig = $twig;
       $this->debug = $debug;
    }
    /**
     * Converts an Exception to a Response.
     *
     * @param Request                   $request
     * @param \Exception|\Throwable     $exception
     * @param DebugLoggerInterface|null $logger
     *
     * @throws \InvalidArgumentException
     *
     * @return Response
     */
    public function showAction(Request $request, $exception, DebugLoggerInterface $logger = null)
    {
        // namespace Symfony\Bundle\TwigBundle\Controller;
        $code = $this->getStatusCode($exception);
        $currentContent = $this->getAndCleanOutputBuffering($request->headers->get('X-Php-Ob-Level', -1));
        $showException = $request->attributes->get('showException', $this->debug); // As opposed to an additional parameter, this maintains BC

        if (!$exception instanceof FlattenException) {
            $exception = FlattenException::create($exception);
        }
        if (substr( $request->getPathInfo(), 0, 4 ) !== "/api") {
            return parent::showAction($request, $exception, $logger);
        }

        $errorInfo = [
            "code" => $code,
            'message' => $exception->getMessage()
        ];

        if ($this->debug) {
            $errorInfo['trace'] = $exception->getTrace();
        }
        
        return new Response(
            json_encode(
                [
                    'error' => $errorInfo
                ],
                JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
            ),
            $code,
            ['Content-type' => 'application/json']
        );
        
    }
    


    /**
     * Determines the status code to use for the response.
     *
     * @param \Exception $exception
     *
     * @return int
     */
    protected function getStatusCode(\Exception $exception)
    {
        // If matched
        if ($statusCode = $this->exceptionCodes->resolveException($exception)) {
            return $statusCode;
        }
        // Otherwise, default
        if ($exception instanceof HttpExceptionInterface) {
            return $exception->getStatusCode();
        }
        return 500;
    }
}