<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Category;
use App\Entity\Tag;
use App\Entity\Thematic;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $thematics_name = [
            'Indian', 'Italian', 'Chinese', 'French', 'Japanese', 'Mexican', 'Spanish',
            'American', 'Greek', 'Moroccan', 'Thai', 'Vietnamese', 'Portuguese', 'Turkish',
            'German', 'Irish', 'Swedish', 'Belgian', 'African', 'Cuban', 'Brazilian',
            'Argentinian', 'Peruvian', 'Colombian', 'Russian', 'Polish', 'Hungarian',
            'Danish', 'Dutch', 'Swiss', 'Czech', 'Romanian', 'Bulgarian', 'Slovakian',
            'Croatian', 'Albanian', 'Korean', 'Singaporean', 'Malaysian', 'Indonesian',
            'Australian', 'New Zealand', 'South African', 'Egyptian', 'Tunisian', 'Lebanese',
            'Syrian', 'Jordanian', 'Palestinian', 'Israeli', 'Iranian', 'Afghan', 'Bangladeshi',
            'Nepalese', 'Sri Lankan', 'Pakistani', 'Ukrainian', 'Belarusian', 'Georgian'
        ];

        foreach ($thematics_name as $name) {
            $thematic = new Thematic();
            $thematic->setName($name);
            $manager->persist($thematic);
        }

        $tags_name = [
            'Vegetarian', 'Vegan', 'Gluten-free', 'Dairy-free', 'Egg-free', 'Peanut-free',
            'Tree nut-free', 'Soy-free', 'Fish-free', 'Shellfish-free', 'Sesame-free',
        ];

        foreach ($tags_name as $name) {
            $tag = new Tag();
            $tag->setName($name);
            $manager->persist($tag);
        }

        $categories_name = [
            'Appetizer', 'Main course', 'Dessert', 'Bread', 'Breakfast', 'Salad', 'Soup',
            'Beverage', 'Sauce', 'Marinade', 'Fingerfood', 'Snack', 'Drink'
        ];

        foreach ($categories_name as $name) {
            $category = new Category();
            $category->setName($name);
            $manager->persist($category);
        }

        $manager->flush();
    }
}
