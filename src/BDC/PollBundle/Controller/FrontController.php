<?php

namespace BDC\PollBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class FrontController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('BDCPollBundle:Front:index.html.twig', array('name' => $name));
    }
}

