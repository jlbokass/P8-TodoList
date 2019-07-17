<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixture extends BaseFixture
{
    private $encoder;

    private static $username = [
        'jlbokass',
        'john'
        ];

    private static $email = [
        'test1@gmail.com',
        'test2@gmail.com'
    ];

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function loadData(ObjectManager $manager)
    {
        $this->createMany(User::class, 30, function (User $user) {
            $user->setEmail($this->faker->unique()->email);
            $user->setPassword($this->encoder->encodePassword($user, 'test123456'));
            $user->setRoles(['ROLE_USER']);
            $user->setUsername($this->faker->unique()->userName);

            if($this->faker->boolean(40)) {
                $user->setIsEnable(true);
            }
        });

        $manager->flush();
    }
}
