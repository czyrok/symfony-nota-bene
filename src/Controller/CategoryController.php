<?php

namespace App\Controller;

use App\Entity\Category;
use App\Service\CategoryService;
use App\Type\CategoryCreationType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CategoryController extends AbstractController
{
    #[Route(path: '/category/add', name: 'app_category_add')]
    public function addCategory(Request $request, CategoryService $categoryService): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $currentUser = $this->getUser();

        $category = new Category();

        $categoryCreationForm = $this->createForm(CategoryCreationType::class, $category);
        $categoryCreationForm->handleRequest($request);

        if ($categoryCreationForm->isSubmitted() && $categoryCreationForm->isValid()) {
            $categoryService->addCategoryToUser($category, $currentUser);

            return $this->redirectToRoute('app_profile');
        }

        return $this->render('category/edit-form.html.twig', [
            'categoryCreationForm' => $categoryCreationForm
        ]);
    }
}
