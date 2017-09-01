<?php

namespace ProjectBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class HPGlobaleController extends Controller
{
    public function indexAction()
    {
        return $this->render('ProjectBundle:Homepage:index.html.twig');
    }
}
