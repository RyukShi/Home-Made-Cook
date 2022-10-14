<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Recipe;
use App\Entity\Ingredient;
use App\Entity\Comment;
use App\Entity\Thematic;
use App\Entity\Category;
use App\Entity\Tag;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Faker\Factory;

class UserFixtures extends Fixture
{
    private UserPasswordHasherInterface $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager): void
    {
        $admin = new User();
        $plaintextPassword = 'adminpassword';

        $admin
            ->setRoles(['ROLE_ADMIN'])
            ->setUsername('admin')
            ->setPassword($this->hasher->hashPassword($admin, $plaintextPassword));

        $manager->persist($admin);

        $faker = Factory::create('en_US');

        $thematics = $manager->getRepository(Thematic::class)->findAll();
        $categories = $manager->getRepository(Category::class)->findAll();
        $tags = $manager->getRepository(Tag::class)->findAll();

        $difficulty = ['Easy', 'Medium', 'Hard'];
        $cost = ['Cheap', 'Medium', 'Expensive'];
        $units = ['g', 'kg', 'l', 'cl', 'ml', 'tsp', 'tbsp', 'cup', 'oz', 'lb'];
        $quantity = ['100', '200', '300', '400', '500'];

        // create 20 users
        for ($i = 0; $i < 20; $i++) {
            $user = new User();
            $user
                ->setUsername($faker->userName)
                ->setPassword($this->hasher->hashPassword($user, 'password'));

            // create 5 recipes for each user
            for ($j = 0; $j < 5; $j++) {
                $recipe = new Recipe();
                $recipe
                    ->setName($faker->sentence(3))
                    ->setPreparationTime($faker->numberBetween(5, 60))
                    ->setDifficulty($faker->randomElement($difficulty))
                    ->setPeopleNumber($faker->numberBetween(1, 10))
                    ->setRecipeCost($faker->randomElement($cost))
                    ->setDescription($faker->paragraph(3))
                    ->setThematic($faker->randomElement($thematics))
                    ->setCategory($faker->randomElement($categories))
                    // add a max 4 tags to each recipe, if the tag is already added, it will be ignored
                    ->addTag($faker->randomElement($tags))
                    ->addTag($faker->randomElement($tags))
                    ->addTag($faker->randomElement($tags))
                    ->addTag($faker->randomElement($tags))
                    ->setCooker($user);

                $ingredients = [];

                // create 5 ingredients for each recipe
                for ($k = 0; $k < 5; $k++) {
                    $ingredient = new Ingredient();
                    $ingredient
                        ->setName($faker->word)
                        ->setQuantity($faker->randomElement($quantity))
                        ->setUnit($faker->randomElement($units));

                    $ingredients[] = $ingredient;
                }

                $recipe->setIngredients($ingredients);

                // create 5 comments for each recipe
                for ($l = 0; $l < 5; $l++) {
                    $comment = new Comment();
                    $comment
                        ->setContent($faker->sentence(10))
                        ->setCooker($user)
                        ->setRecipe($recipe);

                    $manager->persist($comment);
                }
                $manager->persist($recipe);
            }
            $manager->persist($user);
        }
        $manager->flush();
    }
}
