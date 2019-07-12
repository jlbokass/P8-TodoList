<?php


namespace App\Services;


use App\Entity\Token;
use App\Entity\User;
use Twig\Environment as Twig;
class TokenSender
{
    private $mailer;
    private $twig;
    public function __construct(\Swift_Mailer $mailer, Twig $twig)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
    }
    public function sendToken(User $user, Token $token)
    {
        $message = (new \Swift_Message('Please confirm your registration'))
            ->setFrom('contact@snowtrick.com')
            ->setTo($user->getEmail())
            ->setBody(
                $this->twig->render(
                    'registration/activation.html.twig', [
                        'token' => $token->getToken(),
                    ]
                ),
                'text/html'
            )
            ->addPart(
                $this->twig->render(
                    'registration/activation.txt.twig', [
                        'token' => $token->getToken(),
                    ]
                ),
                'text/plain'
            )
        ;
        $this->mailer->send($message);
    }
}