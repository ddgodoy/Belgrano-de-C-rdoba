<?php

namespace BDC\PollBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class AdminController extends Controller
{
    public function indexAction()
    {
        return $this->render('BDCPollBundle:Admin:index.html.twig');
    }
}
