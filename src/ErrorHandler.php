<?php
namespace Brave\ForumAuth;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;

/**
 * Extends Slim's error handler to add a logger.
 */
class ErrorHandler extends \Slim\Handlers\Error
{
    protected $logger;

    public function __construct($displayErrorDetails, LoggerInterface $logger)
    {
        $this->logger = $logger;

        parent::__construct($displayErrorDetails);
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, \Exception $exception)
    {
        $this->logger->critical($exception->getMessage(), ['exception' => $exception]);

        return parent::__invoke($request, $response, $exception);
    }
}
