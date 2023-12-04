<?php

namespace App\Controller;

use App\Entity\Tag;
use App\Service\TagService;
use App\Type\TagEditType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Annotation\Route;

class TagController extends AbstractController
{
    #[Route(path: '/tag/add', name: 'app_tag_add')]
    public function addTag(#[MapQueryParameter] ?int $noteId, #[MapQueryParameter] ?int $categoryId, Request $request, TagService $tagService): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $currentUser = $this->getUser();

        $tag = new Tag();

        $tagEditForm = $this->createForm(TagEditType::class, $tag);
        $tagEditForm->handleRequest($request);

        if ($tagEditForm->isSubmitted() && $tagEditForm->isValid()) {
            $tagService->saveTag($tag, $currentUser);

            if ($noteId === null && $categoryId !== null) {
                return $this->redirectToRoute('app_note_add', ['categoryId' => $categoryId]);
            } elseif ($noteId === null) {
                return $this->redirectToRoute('app_profile');
            }

            return $this->redirectToRoute('app_note_one_edit', ['noteId' => $noteId]);
        }

        return $this->render('tag/add-form.html.twig', [
            'tagEditForm' => $tagEditForm
        ]);
    }
}
