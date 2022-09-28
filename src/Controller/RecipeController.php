<?php

namespace App\Controller;

use App\Entity\Recipe;
use App\Form\RecipeType;
use App\Repository\RecipeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

#[Route('/recipe')]
class RecipeController extends AbstractController
{
    #[Route('/', name: 'app_recipe_index', methods: ['GET'])]
    public function index(RecipeRepository $recipeRepository): Response
    {
        return $this->render('recipe/index.html.twig', [
            'recipes' => $recipeRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_recipe_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function new(Request $request, RecipeRepository $recipeRepository): Response
    {
        $recipe = new Recipe();
        $recipe->setCooker($this->getUser());
        // Creates and returns a Form instance from the type of the form
        $recipeForm = $this->createForm(RecipeType::class, $recipe);
        // Determines whether to submit the form or not
        $recipeForm->handleRequest($request);

        if ($recipeForm->isSubmitted() && $recipeForm->isValid()) {
            // persist the recipe and flush it
            $recipeRepository->add($recipe, true);
            // redirect to the recipe show page
            return $this->redirectToRoute(
                'app_recipe_show',
                ['id' => $recipe->getId()],
                Response::HTTP_SEE_OTHER
            );
        }

        return $this->renderForm('recipe/new.html.twig', [
            'recipe_form' => $recipeForm,
        ]);
    }

    #[Route('/{id}', name: 'app_recipe_show', methods: ['GET'], requirements: ['id' => '[1-9]\d*'])]
    public function show(Recipe $recipe): Response
    {
        return $this->render('recipe/show.html.twig', [
            'recipe' => $recipe,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_recipe_edit', methods: ['GET', 'POST'], requirements: ['id' => '[1-9]\d*'])]
    #[IsGranted('ROLE_USER')]
    public function edit(Request $request, Recipe $recipe, RecipeRepository $recipeRepository): Response
    {
        $recipeForm = $this->createForm(RecipeType::class, $recipe);
        $recipeForm->handleRequest($request);

        if ($recipeForm->isSubmitted() && $recipeForm->isValid()) {
            $recipeRepository->add($recipe, true);

            return $this->redirectToRoute(
                'app_recipe_show',
                ['id' => $recipe->getId()],
                Response::HTTP_SEE_OTHER
            );
        }

        return $this->renderForm('recipe/edit.html.twig', [
            'recipe' => $recipe,
            'recipe_form' => $recipeForm,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_recipe_delete', methods: ['POST'], requirements: ['id' => '[1-9]\d*'])]
    #[IsGranted('ROLE_USER')]
    public function delete(Request $request, Recipe $recipe, RecipeRepository $recipeRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $recipe->getId(), $request->request->get('_token'))) {
            $recipeRepository->remove($recipe, true);
        }

        return $this->redirectToRoute('app_recipe_index', [], Response::HTTP_SEE_OTHER);
    }
}
