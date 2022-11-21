<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\Repository\RecipeRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;

class RecipeControllerTest extends WebTestCase
{
    private ?KernelBrowser $client = null;
    private ?RecipeRepository $recipeRepository = null;
    private ?UserRepository $userRepository = null;

    public function setUp(): void
    {
        $this->client = static::createClient();
        $this->recipeRepository = static::getContainer()->get(RecipeRepository::class);
        $this->userRepository = static::getContainer()->get(UserRepository::class);
        $this->urlGenerator = static::getContainer()->get('router.default');
    }

    /**
     * @dataProvider userIdProvider
     */
    public function testUserFavorites(int $userId)
    {
        /* get user from database */
        $testUser = $this->userRepository->find($userId);
        /* login testUser */
        $this->client->loginUser($testUser);

        $recipeId = 1;
        /* simulate a user request to get a recipe */
        $crawler = $this->client->request('GET', $this->urlGenerator->generate('app_recipe_show', ['id' => $recipeId]));
        /* assert that the response is OK (200) */
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        /* get the recipe from database */
        $recipe = $this->recipeRepository->find($recipeId);
        /* save the recipe's likes count */
        $saveLikes = $recipe->getLikes();

        $this->assertStringContainsString($recipe->getName(), $crawler->filter('h1.main-title')->text());
        /* if the recipe is in the user's favorites */
        $isFavorite = $testUser->getFavorites() !== null && in_array($recipe->getId(), $testUser->getFavorites(), true);
        /* submit button name to get favorite form */
        $selector = (!$isFavorite) ? 'Add to favorites' : 'Remove from favorites';
        /* favorite form */
        $form = $crawler->selectButton($selector)->form();
        /* submit the form */
        $this->client->submit($form);
        /* assert code 303 (HTTP_SEE_OTHER) redirection */
        $this->assertEquals(303, $this->client->getResponse()->getStatusCode());

        /* logout testUser */
        $this->client->request('GET', $this->urlGenerator->generate('app_logout'));
        /* get one more time testUser from the database to update his favorites */
        $testUser = $this->userRepository->find($userId);
        /* get one more time the recipe from the database to update its likes */
        $recipe = $this->recipeRepository->find($recipeId);

        if (!$isFavorite) {
            /* assert that the recipe is in the user's favorites */
            $this->assertTrue(in_array($recipeId, $testUser->getFavorites(), true));
            /* assert that the recipe's likes count has increased by 1 */
            $this->assertEquals(++$saveLikes, $recipe->getLikes());
        } else {
            /* assert that the recipe is not in the user's favorites */
            $this->assertTrue(!in_array($recipeId, $testUser->getFavorites(), true));
            /* assert that the recipe's likes count has decreased by 1 */
            $this->assertEquals(--$saveLikes, $recipe->getLikes());
        }
    }

    public function userIdProvider()
    {
        return [
            [1], [2], [3]
        ];
    }
}
