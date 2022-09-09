<?php

namespace App\DataFixtures;

use App\Entity\Task;
use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $lorem = 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Libero dolores architecto illum? Minima harum nihil provident fuga inventore laboriosam accusantium amet natus porro reprehenderit quas, non itaque, voluptatem animi recusandae.';
        
        $user = new User();
        $user->setUsername('anonyme');
        $user->setPassword('$2y$13$rQJeJZzPvYTrqY4LalaxMeTH8mJY21CewW0s7Ri/i1wWukwhkjr3u'); // 123
        $user->setEmail('user@anonyme.fr');
        $user->setRoles(['ROLE_USER']);
        $manager->persist($user);

        for ($i = 0; $i < 6; $i++){
            $task = new Task();
            $task->setTitle('Ma super tâche ' . $i);
            $task->setContent($lorem);
            $task->setCreatedAt(new \DateTimeImmutable);
            $task->setAuthor($user);
            $rand = rand(1, 2);
            $done = false;
            if ($rand == 1){
                $done = true;
            }
            $task->setIsDone($done);
            $manager->persist($task);
        }



        $user = new User();
        $user->setUsername('Admin');
        $user->setPassword('$2y$13$rQJeJZzPvYTrqY4LalaxMeTH8mJY21CewW0s7Ri/i1wWukwhkjr3u'); // 123
        $user->setEmail('admin@admin.fr');
        $user->setRoles(['ROLE_ADMIN']);
        $manager->persist($user);

        for ($i = 0; $i < 4; $i++){
            $task = new Task();
            $task->setTitle('Ma super tâche ' . $i);
            $task->setContent($lorem);
            $task->setCreatedAt(new \DateTimeImmutable);
            $task->setAuthor($user);
            $rand = rand(1, 2);
            $done = false;
            if ($rand == 1){
                $done = true;
            }
            $task->setIsDone($done);
            $manager->persist($task);
        }

        for ($i = 0; $i < 3; $i++){
            $user = new User();
            $user->setUsername('User ' . $i);
            $user->setPassword('$2y$13$rQJeJZzPvYTrqY4LalaxMeTH8mJY21CewW0s7Ri/i1wWukwhkjr3u'); // 123
            $user->setEmail('user' .$i . '@user.fr');
            $user->setRoles(['ROLE_USER']);
            $manager->persist($user);
    
            for ($x = 0; $x < 3; $x++){
                $task = new Task();
                $task->setTitle('Ma super tâche ' . $x);
                $task->setContent($lorem);
                $task->setCreatedAt(new \DateTimeImmutable);
                $task->setAuthor($user);
                $rand = rand(1, 2);
                $done = false;
                if ($rand == 1){
                    $done = true;
                }
                $task->setIsDone($done);
                $manager->persist($task);
            }
        }

        $manager->flush();
    }
}
