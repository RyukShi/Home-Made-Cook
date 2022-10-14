<?php

namespace App\Controller;

use App\Entity\Comment;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use App\Form\CommentType;
use App\Repository\CommentRepository;

#[Route('/comment')]
#[IsGranted('ROLE_USER')]
class CommentController extends AbstractController
{
    #[Route('/{id}/edit', name: 'app_comment_edit', methods: ['GET', 'POST'], requirements: ['id' => '[1-9]\d*'])]
    public function edit(
        Comment $comment,
        Request $request,
        CommentRepository $commentRepository
    ): Response {
        if ($comment->getCooker()->getId() !== $this->getUser()->getId()) {
            throw $this->createAccessDeniedException('You can only edit your own comments.');
        }
        $commentForm = $this->createForm(CommentType::class, $comment);
        $commentForm->handleRequest($request);

        if ($commentForm->isSubmitted() && $commentForm->isValid()) {
            // persist the comment and flush it
            $commentRepository->add($comment, true);
            // redirect to the recipe show page
            return $this->redirectToRoute(
                'app_recipe_show',
                ['id' => $comment->getRecipe()->getId()],
                Response::HTTP_SEE_OTHER
            );
        }

        return $this->renderForm('comment/edit.html.twig', [
            'comment_form' => $commentForm,
            'comment' => $comment,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_comment_delete', methods: ['POST'], requirements: ['id' => '[1-9]\d*'])]
    public function delete(
        Comment $comment,
        CommentRepository $commentRepository,
        Request $request
    ): Response {
        if ($comment->getCooker()->getId() !== $this->getUser()->getId()) {
            throw $this->createAccessDeniedException('You can only delete your own comments.');
        }
        if ($this->isCsrfTokenValid('delete' . $comment->getId(), $request->request->get('_token'))) {
            $commentRepository->remove($comment, true);
        }
        // redirect to the recipe show page
        return $this->redirectToRoute(
            'app_recipe_show',
            ['id' => $comment->getRecipe()->getId()],
            Response::HTTP_SEE_OTHER
        );
    }
}
