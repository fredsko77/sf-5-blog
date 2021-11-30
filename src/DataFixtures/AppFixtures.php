<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Comment;
use App\Entity\Contact;
use App\Entity\Post;
use App\Entity\User;
use Cocur\Slugify\Slugify;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{

    /**
     * @var UserPasswordHasherInterface $hasher
     */
    private $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager): void
    {

        $faker = Factory::create('fr_FR');
        $slugger = new Slugify();
        $users = [];
        $authors = [];

        for ($ad = 0; $ad < random_int(1, 2); $ad++) {
            $user = $this->getUser($faker)
                ->setRoles(['ROLE_ADMIN'])
            ;

            $manager->persist($user);
        }

        for ($e = 0; $e < random_int(4, 8); $e++) {
            $user = $this->getUser($faker)
                ->setRoles(['ROLE_EDITOR'])
            ;

            $manager->persist($user);
            $users[] = $user;
            $authors[] = $user;
        }

        for ($au = 0; $au < random_int(12, 18); $au++) {
            $user = $this->getUser($faker)
                ->setRoles(['ROLE_AUTHOR'])
            ;

            $manager->persist($user);
            $users[] = $user;
            $authors[] = $user;
        }

        for ($u = 0; $u < random_int(200, 500); $u++) {
            $user = $this->getUser($faker, $u)
                ->setRoles(['ROLE_USER'])
            ;

            $manager->persist($user);
            $users[] = $user;
        }

        for ($ca = 0; $ca < random_int(10, 15); $ca++) {
            $category = new Category;

            $category->setName($faker->words(random_int(1, 4), true))
                ->setSlug($slugger->slugify($category->getName()))
                ->setDescription($faker->sentences(random_int(1, 5), true))
                ->setImage($faker->imageUrl())
                ->setCreatedAt((new DateTime)->modify('-' . random_int(1, 350) . ' days'))
                ->setUpdatedAt($ca % 3 ? null : $faker->dateTimeBetween('-300 days'))
            ;

            for ($p = 0; $p < random_int(20, 70); $p++) {
                $post = new Post;

                $post->setTitle($faker->words(random_int(1, 4), true))
                    ->setSlug($slugger->slugify($post->getTitle()))
                    ->setSummary($faker->sentences(random_int(1, 2), true))
                    ->setContent($this->surround($faker->paragraphs(random_int(3, 6))))
                    ->setImage($faker->imageUrl())
                    ->setCreatedAt((new DateTime)->modify('-' . random_int(1, 350) . ' days'))
                    ->setUpdatedAt($ca % 3 ? null : $faker->dateTimeBetween('-300 days'))
                    ->setAuthor($this->array_value($authors))
                    ->setState($p % 7 ? Post::STATE_PUBLISHED : $this->array_value([Post::STATE_DRAFTED, Post::STATE_IN_ARCHIEVED, Post::STATE_IN_REVIEW, Post::STATE_TRASHED]))
                    ->setVisibility($p % 15 ? Post::VISIBILITY_PUBLIC : Post::VISIBILITY_PRIVATE)
                ;

                if ($post->getState() === 'published') {
                    for ($c = 0; $c < random_int(0, 40); $c++) {
                        $state = null;
                        $comment = new Comment;

                        $comment->setContent($faker->sentences(random_int(1, 3), true))
                            ->setAuthor($this->array_value($users))
                            ->setCreatedAt((new DateTime)->modify('-' . random_int(1, 350) . ' days'))
                            ->setFlag($c % 9 ? null : random_int(1, 10))
                        ;

                        if ($comment->getFlag() > 0) {
                            $state = 'flagged';
                        }
                        if ($comment->getFlag() === 10) {
                            $state = 'banned';
                        }

                        $comment->setState($state);

                        for ($l = 0; $l < random_int(0, 10); $l++) {
                            $post->addLike($this->array_value($users));
                        }

                        $post->setPublishedAt($post->getUpdatedAt())
                            ->addComment($comment)
                        ;

                    }
                }

                $category->addPost($post);
            }

            $manager->persist($category);
        }

        for ($co = 0; $co < random_int(40, 100); $co++) {
            $contact = new Contact;

            $contact->setName($faker->name)
                ->setEmail($faker->email)
                ->setSubject($faker->words(random_int(1, 4), true))
                ->setMessage($faker->paragraphs(random_int(1, 3), true))
                ->setCreatedAt($faker->dateTimeBetween('-6 months'))
                ->setState($faker->randomElement([Contact::STATE_NOT_REPLIED, Contact::STATE_REPLIED, Contact::STATE_OPENED]))
                ->setUpdatedAt($contact->getState() === Contact::STATE_NOT_REPLIED ? null : $faker->dateTimeBetween('-6 months'))
            ;

            $manager->persist($contact);
        }

        $manager->flush();
    }

    private function getUser($faker, int $index = 0): User
    {
        $user = new User;

        return $user->setUsername($faker->userName . ($index < 1 ? '' : $index))
            ->setEmail($faker->email)
            ->setFirstname($faker->firstName)
            ->setLastname($faker->lastName)
            ->setPassword($this->hasher->hashPassword($user, 'password'))
            ->setRegisteredAt((new DateTime)->modify('-' . random_int(1, 400) . ' days'))
            ->setConfirm(true)
            ->setUpdatedAt($faker->dateTimeBetween('-300 days'))
            ->setIdentifier()
        ;
    }

    private function array_value($array)
    {
        if (is_array($array)) {
            $length = (count($array)) - 1;

            return $array[random_int(0, $length)];
        }
    }

    private function surround(array $paragraphs = [], string $tag = 'p'): string
    {
        $text = '';
        foreach ($paragraphs as $paragraph) {
            $text .= '<p>' . $paragraph . '</p>';
        }

        return $text;
    }
}
