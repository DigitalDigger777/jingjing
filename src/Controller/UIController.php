<?php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class UIController extends Controller
{
    /**
     * @Route("/", name="jingjing_ui")
     */
    public function index()
    {
        // replace this line with your own code!
        return $this->render('ui.html.twig');
    }
}
