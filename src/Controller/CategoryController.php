<?php

namespace App\Controller;

use App\Entity\Category;
use App\Service\CategoryService;
use App\Type\CategoryEditType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class CategoryController extends AbstractController
{
    #[Route(path: '/category/add', name: 'app_category_add')]
    public function addCategory(Request $request, CategoryService $categoryService): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $currentUser = $this->getUser();

        $category = new Category();

        $categoryEditForm = $this->createForm(CategoryEditType::class, $category);
        $categoryEditForm->handleRequest($request);

        if ($categoryEditForm->isSubmitted() && $categoryEditForm->isValid()) {
            $categoryService->addCategoryToUser($category, $currentUser);

            return $this->redirectToRoute('app_profile');
        }

        return $this->render('category/edit-form.html.twig', [
            'categoryEditForm' => $categoryEditForm
        ]);
    }

    #[Route(path: '/category/{categoryId}/edit', name: 'app_category_one_edit')]
    public function editCategory(int $categoryId, Request $request, CategoryService $categoryService): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $currentUser = $this->getUser();

        $category = $categoryService->getCategory($categoryId);

        if (!$category) {
            throw new NotFoundHttpException("Category not found");
        }

        if ($category->getUser()->getId() !== $currentUser->getId()) {
            throw new AccessDeniedHttpException("You can't edit a category of a different user");
        }

        $categoryEditForm = $this->createForm(CategoryEditType::class, $category);
        $categoryEditForm->handleRequest($request);

        if ($categoryEditForm->isSubmitted() && $categoryEditForm->isValid()) {
            $categoryService->saveCategory($category);

            return $this->redirectToRoute('app_profile');
        }

        return $this->render('category/edit-form.html.twig', [
            'categoryEditForm' => $categoryEditForm,
            'category' => $category
        ]);
    }

    #[Route(path: '/category/{categoryId}/delete', name: 'app_category_one_delete')]
    public function deleteCategory(int $categoryId, CategoryService $categoryService): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $currentUser = $this->getUser();

        $categoryService->deleteCategory($categoryId, $currentUser);

        return $this->redirectToRoute('app_profile');
    }

    #[Route(path: '/category/{categoryId}/delete/confirmation', name: 'app_category_one_delete_confirmation')]
    public function deleteCategoryConfirmation(int $categoryId, CategoryService $categoryService): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $currentUser = $this->getUser();

        $category = $categoryService->getCategory($categoryId);

        if (!$category) {
            throw new NotFoundHttpException("Category not found");
        }

        if ($category->getUser()->getId() !== $currentUser->getId()) {
            throw new AccessDeniedHttpException("You can't delete a category of a different user");
        }

        return $this->render('category/delete-confirmation.html.twig', [
            'category' => $category
        ]);
    }
}
