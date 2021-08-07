<?php

namespace App\Security;

use App\Manager\NotificationManager;
use App\Service\Notification\Notification;
use App\Service\Notification\Notifier;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\PassportInterface;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class LoginFormAuthentificatorAuthenticator extends AbstractLoginFormAuthenticator
{
    use TargetPathTrait;

    public const LOGIN_ROUTE = 'app_login';

    private UrlGeneratorInterface $urlGenerator;

    private NotificationManager $notificationManager;

    private Notifier $notifier;

    public function __construct(UrlGeneratorInterface $urlGenerator, NotificationManager $notificationManager, Notifier $notifier)
    {
        $this->urlGenerator = $urlGenerator;
        $this->notificationManager = $notificationManager;
        $this->notifier = $notifier;
    }

    public function authenticate(Request $request): PassportInterface
    {
        $username = $request->request->get('username', '');

        $request->getSession()->set(Security::LAST_USERNAME, $username);

        return new Passport(
            new UserBadge($username),
            new PasswordCredentials($request->request->get('password', '')),
            [
                new CsrfTokenBadge('authenticate', $request->get('_csrf_token')),
            ]
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        if ($targetPath = $this->getTargetPath($request->getSession(), $firewallName)) {
            return new RedirectResponse($targetPath);
        }

        if (!in_array('ROLE_ADMIN', $token->getUser()->getRoles(), true)) {
            $notification = $this->notificationManager->createFromUser($token->getUser(), 'login');
            $data['message'] = $notification->getContent();
            $this->notifier->send(new Notification(['notifications'], $data, false));

            return new RedirectResponse($this->urlGenerator->generate('get_files'));
        }

        return new RedirectResponse($this->urlGenerator->generate('get_notifications'));
    }

    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }
}
