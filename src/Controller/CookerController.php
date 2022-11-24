<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;

class CookerController extends AbstractController
{
    #[Route('/cooker/{id}', name: 'app_cooker_show', methods: ['GET'], requirements: ['id' => '[1-9]\d*'])]
    public function show(User $cooker): Response
    {
        if (empty($cooker->getRecipes())) {
            return $this->redirectToRoute(
                'app_recipe_index'
            );
        }
        return $this->render('cooker/show.html.twig', [
            'cooker' => $cooker
        ]);
    }
}
