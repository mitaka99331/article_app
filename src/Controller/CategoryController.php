<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @IsGranted("ROLE_USER")
 */
class CategoryController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/categories", methods="GET", name="list_categories")
     */
    public function list(): Response
    {
        return $this->render('category/list.html.twig', [
            'categories' => $this->entityManager->getRepository(Category::class)->findAll(),
        ]);
    }

    /**
     * @Route("/category/create", methods="GET|POST", name="create_category")
     */
    public function new(Request $request): Response
    {
        $form = $this->createForm(CategoryType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($form->getData());
            $this->entityManager->flush();

            return $this->redirectToRoute('list_categories');
        }

        return $this->render('category/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/category/{category}/delete", methods="GET", name="delete_category", requirements={"category"="\d+"})
     */
    public function delete(Category $category): Response
    {
        $this->entityManager->remove($category);
        $this->entityManager->flush();

        return $this->redirectToRoute('list_categories');
    }

    /**
     * @Route("/category/{category}/update", methods="GET|POST", name="update_category", requirements={"category"="\d+"})
     */
    public function update(Category $category, Request $request): Response
    {
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();

            return $this->redirectToRoute('list_categories');
        }

        return $this->render('category/update.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}