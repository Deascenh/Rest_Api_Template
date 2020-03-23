<?php
namespace App\EventListener;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class JWTCreatedListener
{
    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @param RequestStack $requestStack
     */
    public function __construct(
        RequestStack $requestStack,
        EntityManagerInterface $entityManager
    ) {
        $this->requestStack = $requestStack;
        $this->entityManager = $entityManager;
    }

    /**
     * @param JWTCreatedEvent $event
     *
     * @return void
     */
    public function onJWTCreated(JWTCreatedEvent $event)
    {
        $request = $this->requestStack->getCurrentRequest();
        $requestContent = json_decode($request->getContent(), true);

        $user = $this->entityManager
            ->getRepository(User::class)
            ->findOneBy(['email' => $requestContent['username']]);

        $payload = $event->getData();

        $payload['id'] = $user->getId();
        $payload['ip'] = $request->getClientIp();
        $payload['iss'] = getenv('JWT_ISSUER');

        $event->setData($payload);
    }
}
