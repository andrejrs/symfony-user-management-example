<?php

namespace App\Security;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Guard\Authenticator\AbstractFormLoginAuthenticator;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

/**
 * A Login class to make form login authentication
 *
 * @category Controller
 * @package  SrcSecurity
 * @author   Andrej <akicay@gmail.com>
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/andrejrs
 */
class LoginFormAuthenticator extends AbstractFormLoginAuthenticator
{
    use TargetPathTrait;

    /**
     * EntityManager interface
     *
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * Define the different types of resource references that
     * are declared in RFC 3986
     *
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    /**
     * Manages CSRF tokens
     *
     * @var CsrfTokenManagerInterface
     */
    private $csrfTokenManager;

    /**
     * The interface for the password encoder service
     *
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    /**
     * Set manager for the entity, urlGenerator, csrfTokenManager and passwordEncoder
     *
     * @param EntityManagerInterface       $entityManager    EntityManager interface
     * @param UrlGeneratorInterface        $urlGenerator     Resource references
     * @param CsrfTokenManagerInterface    $csrfTokenManager Manages CSRF tokens
     * @param UserPasswordEncoderInterface $passwordEncoder  Password encoder service
     */
    public function __construct(EntityManagerInterface $entityManager, UrlGeneratorInterface $urlGenerator, CsrfTokenManagerInterface $csrfTokenManager, UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->entityManager = $entityManager;
        $this->urlGenerator = $urlGenerator;
        $this->csrfTokenManager = $csrfTokenManager;
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * Support the request
     *
     * @param  Request $request Request represents an HTTP request
     * @return void
     */
    public function supports(Request $request)
    {
        return 'app_login' === $request->attributes->get('_route')
            && $request->isMethod('POST');
    }

    /**
     * Getting credentials of the request params
     *
     * @param  Request $request Request represents an HTTP request
     * @return void
     */
    public function getCredentials(Request $request)
    {
        $credentials = [
            'email' => $request->request->get('email'),
            'password' => $request->request->get('password'),
            'csrf_token' => $request->request->get('_csrf_token'),
        ];
        $request->getSession()->set(
            Security::LAST_USERNAME,
            $credentials['email']
        );

        return $credentials;
    }

    /**
     * Getting user by the credentials
     *
     * @param  Array                 $credentials  Array of the user credential data
     * @param  UserProviderInterface $userProvider Represents a class that loads 
     *                                             UserInterface objects from some source for the 
     *                                             authentication system.
     * @return void
     */
    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        $token = new CsrfToken('authenticate', $credentials['csrf_token']);
        if (!$this->csrfTokenManager->isTokenValid($token)) {
            throw new InvalidCsrfTokenException();
        }

        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $credentials['email']]);

        if (!$user) {
            // fail authentication with a custom error
            throw new CustomUserMessageAuthenticationException('Email could not be found.');
        }

        return $user;
    }

    /**
     * Undocumented function
     *
     * @param  Array         $credentials Array of the user credential data
     * @param  UserInterface $user        The user authentication interface
     * @return void
     */
    public function checkCredentials($credentials, UserInterface $user)
    {
        return $this->passwordEncoder->isPasswordValid($user, $credentials['password']);
    }

    /**
     * Redirect response on authentication success proccess 
     *
     * @param  Request        $request     Request represents an HTTP request
     * @param  TokenInterface $token       The interface for the user authentication information.
     * @param  String         $providerKey The URL (if any) the user visited that forced them to login.
     * @return void
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        if ($targetPath = $this->getTargetPath($request->getSession(), $providerKey)) {
            return new RedirectResponse($targetPath);
        }

        return new RedirectResponse(
            $this->urlGenerator->generate('admin_dashboard')
        );
    }

    /**
     * Generates a URL for a login route
     *
     * @return void
     */
    protected function getLoginUrl()
    {
        return $this->urlGenerator->generate('app_login');
    }
}
