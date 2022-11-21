<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use PHPUnit\Framework\TestCase;
use App\Entity\Recipe;
use App\Entity\Ingredient;
use App\Entity\Tag;

class RecipeTest extends TestCase
{
    public function testDefault()
    {
        $cakeTag = new Tag();
        $cakeTag->setName('Cake');

        $recipe = new Recipe();
        $recipe
            ->setName('Banana Cake')
            ->setDescription('A delicious banana cake')
            ->setIngredients([
                new Ingredient('Banana', '3', 'unit'),
                new Ingredient('Flour', '250', 'g'),
                new Ingredient('Sugar', '100', 'g'),
                new Ingredient('Eggs', '2', 'unit')
            ])
            ->setPeopleNumber(4)
            ->setPreparationTime(35)
            ->setDifficulty('Medium')
            ->setRecipeCost('Cheap')
            ->addTag($cakeTag)
            /* Cake tag added one more time to test if it's added only once */
            ->addTag($cakeTag);

        /* recipe asserts */
        $this->assertEquals('Banana Cake', $recipe->getName());
        $this->assertEquals('A delicious banana cake', $recipe->getDescription());
        $this->assertEquals(35, $recipe->getPreparationTime());
        $this->assertEquals('Medium', $recipe->getDifficulty());
        $this->assertEquals('Cheap', $recipe->getRecipeCost());
        $this->assertCount(4, $recipe->getIngredients());
        $this->assertCount(1, $recipe->getTags());
        $this->assertEquals(4, $recipe->getPeopleNumber());
        $this->assertEquals(0, $recipe->getLikes());

        /* remove Cake tag */
        $recipe->removeTag($cakeTag);
        $this->assertEmpty($recipe->getTags());
    }
}
