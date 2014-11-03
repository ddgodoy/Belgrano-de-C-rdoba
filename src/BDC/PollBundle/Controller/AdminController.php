<?php

namespace BDC\PollBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class AdminController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('BDCPollBundle:Admin:index.html.twig', array('name' => $name));
    }
}
