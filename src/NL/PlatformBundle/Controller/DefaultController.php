<?php

namespace NL\PlatformBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('NLPlatformBundle:Default:index.html.twig', array('name' => $name));
    }
}
