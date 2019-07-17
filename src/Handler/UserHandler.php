<?php


namespace App\Handler;


use App\Entity\Token;
use App\Entity\User;
use App\Services\TokenSender;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\ORMException;
use Psr\Log\LoggerInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserHandler
{
    private $objectManager;
    private $loggerInterface;

    public function __construct(ObjectManager $objectManager, LoggerInterface $loggerInterface)
    {
        $this->objectManager = $objectManager;
        $this->loggerInterface = $loggerInterface;
    }

    public function handle(
        FormInterface $form,
        Request $request,
        UserPasswordEncoderInterface $passwordEncoder,
        TokenSender $sender
    )
    {
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $user = new User();

            $user->setPassword($passwordEncoder->encodePassword(
                $user,
                $form->get('password')->getData()
            ))
            ;
            $user->setRoles(['ROLE_USER']);
            $token = new Token($user);
            $sender->sendToken($user, $token);

            try {
                $this->objectManager->persist($user);
                $this->objectManager->persist($token);
                $this->objectManager->flush();
            } catch (ORMException $e) {

                $this->loggerInterface->error($e->getMessage());
                $form->addError(new FormError('Erreur'));
                return false;
            }
        }
    }
}