<?php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @IsGranted("ROLE_USER")
 */
class IndexController extends AbstractController
{
    /**
     * @Route("/", name="home_page")
     */
    public function home(): Response
    {
        return $this->render('home.html.twig');
    }
}