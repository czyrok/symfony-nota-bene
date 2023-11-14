<?php

namespace App\Controller;

use App\Service\CategoryService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TagController extends AbstractController
{
    #[Route(path: '/tag/add', name: 'app_tag_add')]
    public function addTag(Request $request, CategoryService $categoryService): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $currentUser = $this->getUser();

        /* $category = new Category();

        $categoryCreationForm = $this->createForm(CategoryCreationType::class, $category);
        $categoryCreationForm->handleRequest($request);

        if ($categoryCreationForm->isSubmitted() && $categoryCreationForm->isValid()) {
            $categoryService->addCategoryToUser($category, $currentUser);

            return $this->redirectToRoute('app_profile');
        }

        return $this->render('category/edit-form.html.twig', [
            'categoryCreationForm' => $categoryCreationForm
        ]); */
    }
}
