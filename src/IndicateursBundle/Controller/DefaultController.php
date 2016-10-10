<?php

namespace IndicateursBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use IndicateursBundle\Repository\Indic_itemsRepository;

class DefaultController extends Controller
{
    public function indexAction()
    {

        return $this->render('IndicateursBundle:Default:index.html.twig',array('activeMenu' => 'homepage'));
    }

}
