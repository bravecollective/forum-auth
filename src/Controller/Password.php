<?php
namespace Brave\ForumAuth\Controller;

use Brave\ForumAuth\Model\CharacterRepository;
use Brave\ForumAuth\SessionHandler;
use Brave\Sso\Basics\EveAuthentication;
use Doctrine\ORM\EntityManagerInterface;
use Interop\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use Slim\Http\Response;

class Password
{
    /**
     * @var SessionHandler
     */
    private $sessionHandler;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var CharacterRepository
     */
    private $characterRepository;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(ContainerInterface $container)
    {
        $this->sessionHandler = $container->get(SessionHandler::class);
        $this->logger = $container->get(LoggerInterface::class);
        $this->characterRepository = $container->get(CharacterRepository::class);
        $this->entityManager = $container->get(EntityManagerInterface::class);
    }

    public function reset(ServerRequestInterface $request, Response $response)
    {
        // check login
        $auth = $this->sessionHandler->get('eveAuth');
        if (! $auth instanceof EveAuthentication) {
            return $response->withRedirect('/login');
        }

        // get and update character
        $character = $this->characterRepository->find($auth->getCharacterId());
        if ($character === null) {
            return $response->withRedirect('/?pw-success=0');
        }
        $character->setPassword($character->generatePassword());

        // save
        try {
            $this->entityManager->flush();
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage(), ['exception' => $e]);
            return $response->withRedirect('/?pw-success=0');
        }

        return $response->withRedirect('/?pw-success=1');
    }
}
