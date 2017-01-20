<?php

namespace IndicateursBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class HelpController extends Controller
{
    public function indexAction()
    {
        return $this->render('IndicateursBundle:Help:help.html.twig',array('activeMenu' => 'help'));
    }
}