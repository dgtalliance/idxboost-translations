<?php

namespace App\Controller;

use App\JWT\JWT;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

class LoginController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     * @param Request $request
     * @param EventDispatcherInterface $dispatcher
     * @return RedirectResponse
     */
    public function index(Request $request, EventDispatcherInterface $dispatcher)
    {
        $publicKey = <<<EOD
            -----BEGIN PUBLIC KEY-----
            MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQC8kGa1pSjbSYZVebtTRBLxBz5H
            4i2p/llLCrEeQhta5kaQu/RnvuER4W8oDH3+3iuIYW4VQAzyqFpwuzjkDI+17t5t
            0tyazyZ8JXw+KgXTxldMPEL95+qVhgXvwtihXC1c5oGbRlEDvDF6Sa53rcFVsYJ4
            ehde/zUxo6UvS7UrBQIDAQAB
            -----END PUBLIC KEY-----
            EOD;

        $token = $_GET['token'];
        try {
            $decoded = JWT::decode($token, $publicKey, array('RS256'));
            $decoded_array = (array)$decoded;
            $user = $decoded_array['username'];

            $token = new UsernamePasswordToken($user, 'main', ['ROLE_USER']);
            $this->get("security.token_storage")->setToken($token);
            $event = new InteractiveLoginEvent($request, $token);
            $dispatcher->dispatch($event, "security.interactive_login");

        } catch (\Exception $ex) {
            throw new UnauthorizedHttpException('main');
        }


        return $this->redirectToRoute('app_language_index');
    }
}
