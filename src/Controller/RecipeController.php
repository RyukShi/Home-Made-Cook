<?php

namespace App\Controller;

use App\Entity\Recipe;
use App\Form\RecipeType;
use App\Repository\RecipeRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

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

    #[Route('/{id}', name: 'app_recipe_show', methods: ['GET', 'POST'], requirements: ['id' => '[1-9]\d*'])]
    public function show(
        Recipe $recipe,
        UserRepository $userRepository,
        Request $request
    ): Response {
        $user = $this->getUser();
        // If the user is logged
        if ($user) {
            // If the user is the cooker of the recipe
            $isCooker = $user->getId() === $recipe->getCooker()->getId();

            // If the recipe is in the user's favorites
            $isFavorite = $user->getFavorites() !== null && in_array($recipe->getId(), $user->getFavorites(), true);

            // create form to add/remove the recipe to the user's favorites
            $favoriteForm = $this->createFormBuilder()
                ->add('submit', SubmitType::class, ['label' => ($isFavorite) ? 'Remove from favorites' : 'Add to favorites'])
                ->setMethod('POST')
                ->getForm();

            $favoriteForm->handleRequest($request);

            if ($favoriteForm->isSubmitted() && $favoriteForm->isValid()) {
                if ($isFavorite) {
                    // remove the recipe from the user's favorites
                    $user->removeRecipeFromFavorites($recipe->getId());
                } else {
                    // add the recipe to the user's favorites
                    $user->addRecipeToFavorites($recipe->getId());
                }
                // persist the user and flush it
                $userRepository->add($user, true);
                // redirect to the recipe show page
                return $this->redirectToRoute(
                    'app_recipe_show',
                    ['id' => $recipe->getId()],
                    Response::HTTP_SEE_OTHER
                );
            }
        }

        return $this->render('recipe/show.html.twig', [
            'recipe' => $recipe,
            'is_cooker' => $isCooker ?? false,
            'is_favorite' => $isFavorite ?? false,
            'favorite_form' => isset($favoriteForm) ? $favoriteForm->createView() : null,
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
