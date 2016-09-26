<?php

namespace IndicateursBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('IndicateursBundle:Default:index.html.twig');
    }
}
