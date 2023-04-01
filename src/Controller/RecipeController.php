<?php

namespace App\Controller;

use App\Data\SearchRecipe;
use App\Entity\Recipe;
use App\Entity\Comment;
use App\Form\CommentType;
use App\Form\RecipeType;
use App\Form\SearchRecipeType;
use App\Repository\RecipeRepository;
use App\Repository\CommentRepository;
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
    public function index(RecipeRepository $recipeRepository, Request $request): Response
    {
        $searchRecipe = new SearchRecipe();
        $searchForm = $this->createForm(SearchRecipeType::class, $searchRecipe);
        $searchForm->handleRequest($request);

        if ($searchForm->isSubmitted() && $searchForm->isValid()) {
            $filteredRecipes = $recipeRepository->findSearch($searchRecipe);
        }
        return $this->renderForm('recipe/index.html.twig', [
            'recipes' => $filteredRecipes ?? $recipeRepository->findAll(),
            'search_recipe_form' => $searchForm,
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
        RecipeRepository $recipeRepository,
        UserRepository $userRepository,
        Request $request,
        CommentRepository $commentRepository
    ): Response {
        $user = $this->getUser();
        // If the user is logged
        if ($user) {
            // Comment form
            $comment = new Comment();
            $commentForm = $this->createForm(CommentType::class, $comment);
            $commentForm->handleRequest($request);

            if ($commentForm->isSubmitted() && $commentForm->isValid()) {
                $comment
                    ->setCooker($user)
                    ->setRecipe($recipe);
                $commentRepository->add($comment, true);
                return $this->redirectToRoute(
                    'app_recipe_show',
                    ['id' => $recipe->getId()],
                    Response::HTTP_SEE_OTHER
                );
            }

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
                    $this->addFlash('success', 'Recipe removed from favorites');
                    $recipe->decreaseLikes();
                } else {
                    // add the recipe to the user's favorites
                    $user->addRecipeToFavorites($recipe->getId());
                    $this->addFlash('success', 'Recipe added to favorites');
                    $recipe->increaseLikes();
                }
                // persist the user and flush it
                $userRepository->add($user, true);
                // persist the recipe and flush it
                $recipeRepository->add($recipe, true);
                $this->addFlash('info', 'You can see your favorites in your profile');
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
            'comment_form' => isset($commentForm) ? $commentForm->createView() : null,
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
        if ($recipe->getCooker()->getId() !== $this->getUser()->getId()) {
            return new Response('You must be the owner of the recipe to delete it.', 403);
        }

        if ($this->isCsrfTokenValid('delete' . $recipe->getId(), $request->request->get('_token'))) {
            $recipeRepository->remove($recipe, true);
        }

        return $this->redirectToRoute('app_recipe_index', [], Response::HTTP_SEE_OTHER);
    }
}
