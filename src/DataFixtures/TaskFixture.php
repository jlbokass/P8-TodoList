<?php

namespace App\DataFixtures;

use App\Entity\Task;
use App\Entity\User;
use Doctrine\Common\Persistence\ObjectManager;

class TaskFixture extends BaseFixture
{
    protected function loadData(ObjectManager $manager)
    {
        $this->createMany(Task::class, 17, function (Task $task) {

            $task->setName($this->faker->unique()->title)
                ->setContent($this->faker->paragraph(10, true));
            // publish most articles
            if ($this->faker->boolean(50)) {
                $task->setIsDone(true);
            }

            /** @var User[] $user */
            $user = $this->getRandomReferences(User::class, $this->faker->numberBetween(1, 15));
            foreach ($user as $user) {
                $task->setUser($user);
            }
        });
        $manager->flush();
    }
    public function getDependencies()
    {
        return [
            UserFixture::class,
        ];
    }
}
