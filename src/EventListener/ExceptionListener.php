<?php
/** @noinspection PhpUnused */

namespace App\EventListener;

use App\Service\Constant\AllowIpConstant;
use App\Service\UtilitiesService;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Throwable;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class ExceptionListener
{

    /** @var ContainerInterface */
    protected $container;

    /** @var LoggerInterface */
    private $logger;

    /** @var KernelInterface */
    protected $kernel;

    /** @var Environment */
    private $twig;


    public function __construct(
        ContainerInterface $container,
        LoggerInterface $exceptionLogger,
        KernelInterface $kernel,
        Environment $twig
    )
    {
        $this->container = $container;
        $this->logger = $exceptionLogger;
        $this->kernel = $kernel;
        $this->twig = $twig;
    }

    /**
     * Handles security related exceptions.
     *
     * @param ExceptionEvent $event An GetResponseForExceptionEvent instance
     *
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function onCoreException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        $statusCode = $this->getStatusCode($exception);

        $this->sendToLog($event);

        if (!$event->getRequest()->isXmlHttpRequest()) {
            $event->setResponse($this->responseErrorPage($exception));
        } else {
            $response = new JsonResponse([
                'code'    => $statusCode,
                'message' => $exception->getMessage(),
                'result' => null
            ], $statusCode);

            $event->setResponse($response);
        }
    }

    /**
     * @param ExceptionEvent $event
     *
     * @return void
     */
    private function sendToLog(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        $request = $event->getRequest();
        $route = $request->attributes->get('_route');
        $statusCode = $exception->getCode();

        $routeLog = '';
        if ($route) {
            $parameters = '';
            if (!empty($request->attributes->get('_route_params'))) {
                $parameters .= 'parameters: ' . json_encode($request->attributes->get('_route_params'), JSON_UNESCAPED_UNICODE);
            }
            $routeLog = $route . '. controller: ' . $request->attributes->get('_controller') . $parameters;
        }
        $errorLog = [
            'CODE'         => $statusCode,
            'MESSAGE'      => $exception->getMessage(),
            'ROUTE'        => $routeLog,
            'FILE'         => $exception->getFile() . ' (' . $exception->getLine() . ')',
            'TRACE_STRING' => $exception->getTrace(),
        ];
        $this->logger->info('parse php error: ' . json_encode($errorLog, JSON_UNESCAPED_UNICODE));
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    private function responseErrorPage($exception): Response
    {
        if ($exception instanceof BadCredentialsException || $exception->getCode() === 403) {
            $response = new RedirectResponse('/');
        } else {
            $statusCode = $this->getStatusCode($exception);
            $response   = new Response();
            $response->setStatusCode($statusCode);
            $response->setContent($this->twig->render('errors/error.html.twig', [
                'code' => $statusCode,
                'exception' => $exception
            ]));
        }

        return $response;
    }

    /**
     * @param Throwable $exception
     *
     * @return int
     */
    private function getStatusCode(Throwable $exception): int
    {
        $statusCode = $exception->getCode();
        if (($statusCode === 0) && $exception instanceof NotFoundHttpException) {
            $statusCode = 404;
        }
        if (!array_key_exists($statusCode, Response::$statusTexts)) {
            $statusCode = 500;
        }

        return $statusCode;
    }
}
