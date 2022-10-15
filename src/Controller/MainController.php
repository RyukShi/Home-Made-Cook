<?php

namespace App\Controller;

use App\Repository\RecipeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(RecipeRepository $recipeRepository): Response
    {
        // get the 10 most popular recipes
        $popularRecipes = $recipeRepository->findMostPopularRecipes();

        return $this->render('main/index.html.twig', [
            'popular_recipes' => $popularRecipes,
        ]);
    }
}
