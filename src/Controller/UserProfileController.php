<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use App\Form\UserModificationType;
use App\Repository\RecipeRepository;
use App\Repository\UserRepository;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[IsGranted('ROLE_USER')]
#[Route('/profile')]
class UserProfileController extends AbstractController
{
    #[Route('/', name: 'app_user_profile_index')]
    public function index(): Response
    {
        return $this->render('user_profile/index.html.twig', [
            'user' => $this->getUser(),
        ]);
    }

    #[Route('/{id}/edit', name: 'app_user_profile_edit', requirements: ['id' => '[1-9]\d*'])]
    public function edit(
        Request $request,
        User $user,
        UserRepository $userRepository,
        UserPasswordHasherInterface $passwordHasher
    ): Response {
        // If the user is not the owner of the profile, he can't edit it
        if ($this->getUser()->getId() !== $user->getId()) {
            // redirect to the homepage for security reasons
            return $this->redirectToRoute('app_home');
        }

        $userModificationForm = $this->createForm(UserModificationType::class, $user);

        $userModificationForm->handleRequest($request);

        if ($userModificationForm->isSubmitted() && $userModificationForm->isValid()) {

            if (
                ($userModificationForm->get('plainPassword') !== null) &&
                ($userModificationForm->get('plainPassword')->getData() === $userModificationForm->get('confirmPassword')->getData())
            ) {
                $user->setPassword(
                    $passwordHasher->hashPassword(
                        $user,
                        $userModificationForm->get('plainPassword')->getData()
                    )
                );
            }

            $userRepository->add($user, true);

            return $this->redirectToRoute('app_user_profile_index');
        }
        return $this->renderForm('user_profile/edit.html.twig', [
            'userModificationForm' => $userModificationForm,
            'user' => $user,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_user_profile_delete', requirements: ['id' => '[1-9]\d*'])]
    public function deleteAccount(
        User $user,
        UserRepository $userRepository,
        Request $request
    ): Response {

        // If the user is not the owner of the profile, he can't remove it
        if ($this->getUser()->getId() !== $user->getId()) {
            // redirect to the homepage for security reasons
            return $this->redirectToRoute('app_home');
        }

        $userRepository->remove($user, true);

        // Clears all session attributes and flashes and regenerates the session and deletes the old session from persistence.
        $request->getSession()->invalidate();
        // Set security token to null
        $this->container->get('security.token_storage')->setToken(null);

        return $this->redirectToRoute('app_home');
    }

    #[Route('/{id}/my-favorites', name: 'app_user_profile_favorites', methods: ['GET'], requirements: ['id' => '[1-9]\d*'])]
    public function getFavorites(
        User $user,
        RecipeRepository $recipeRepository,
        UserRepository $userRepository
    ): Response {
        // if the user is not the owner of the profile, he can't see the favorites
        if ($this->getUser()->getId() !== $user->getId()) {
            // redirect to the user profile for security reasons
            return $this->redirectToRoute('app_user_profile_index');
        }

        $recipesIdFavorites = $user->getFavorites();

        // if the user has no favorites
        if ($recipesIdFavorites === null || count($recipesIdFavorites) === 0) {
            return $this->redirectToRoute('app_user_profile_index');
        }

        $favoritesRecipe = [];
        $count = count($recipesIdFavorites);

        foreach ($recipesIdFavorites as $recipeId) {
            // find the recipe by id
            $recipe = $recipeRepository->find($recipeId);
            // if the recipe exists
            if ($recipe) {
                // add the recipe to the array of favorites
                $favoritesRecipe[] = $recipe;
            } else {
                // remove the recipe id from user's favorites
                $user->removeRecipeFromFavorites($recipeId);
            }
        }
        // if the old favorites array is different from the new one
        if ($count !== count($user->getFavorites())) {
            // persist the user and flush it
            $userRepository->add($user, true);
        }

        // finally, if the user has no favorites recipes after processing
        if (count($favoritesRecipe) === 0) {
            return $this->redirectToRoute('app_user_profile_index');
        }

        return $this->render('user_profile/favorites.html.twig', [
            'favorites_recipe' => $favoritesRecipe,
        ]);
    }
}
