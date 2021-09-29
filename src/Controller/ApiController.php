<?php

namespace App\Controller;

use App\Entity\Article;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class ApiController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/api/v1/get/articles", methods="GET", name="api_articles")
     */
    public function list(): JsonResponse
    {
       return new JsonResponse($this->entityManager->getRepository(Article::class)->getArticles());
    }

    /**
     * @Route("/api/authenticate", methods="POST", name="api_authenticate")
     */
    public function authenticate(): JsonResponse
    {
        $user = $this->getUser();

        return new JsonResponse([
            'username' => $user->getUsername(),
            'token' => $user->getApiToken(),
        ]);
    }
}