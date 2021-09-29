<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Category;
use App\Form\ArticleType;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use DateTime;
use DateTimeZone;

/**
 * @IsGranted("ROLE_USER")
 */
class ArticleController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/articles", methods="GET", name="list_articles")
     */
    public function list(Request $request): Response
    {
        $categoryFilter = $request->get('category');
        if (isset($categoryFilter) && !ctype_digit($categoryFilter)) {
            throw $this->createNotFoundException('');
        }

        $filter = isset($categoryFilter) ? ['category' => $categoryFilter] : [];

        return $this->render('article/list.html.twig', [
            'articles' => $this->entityManager->getRepository(Article::class)->findBy($filter, ['date' => 'DESC']),
            'categories' => $this->entityManager->getRepository(Category::class)->findAll()
        ]);
    }

    /**
     * @Route("/article/{article}/view", methods="GET", name="view_article")
     */
    public function view(Article $article): Response
    {
        return $this->render('article/open.html.twig', [
            'article' => $article,
        ]);
    }

    /**
     * @Route("/article/create", methods="GET|POST", name="create_article")
     */
    public function new(Request $request): Response
    {
        $form = $this->createForm(ArticleType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $article = $form->getData();

            $date = new DateTime('now', new DateTimeZone('UTC'));
            $article->setDate($date);

            $this->entityManager->persist($article);
            $this->entityManager->flush();

            return $this->redirectToRoute('list_articles');
        }

        return $this->render('article/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/article/{article}/update", methods="GET|POST", name="update_article", requirements={"article"="\d+"})
     */
    public function update(Article $article, Request $request): Response
    {
        $form = $this->createForm(ArticleType::class, $article, ['oldImage' => $article->getImage()]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();

            return $this->redirectToRoute('list_articles');
        }

        return $this->render('article/update.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/article/{article}/delete", methods="GET", name="delete_article", requirements={"article"="\d+"})
     */
    public function delete(Article $article): Response
    {
        $this->entityManager->remove($article);
        $this->entityManager->flush();

        return $this->redirectToRoute('list_articles');
    }

}