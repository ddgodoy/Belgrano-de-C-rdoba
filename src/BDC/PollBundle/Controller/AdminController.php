<?php

namespace BDC\PollBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use BDC\PollBundle\Service\BDCUtils;

class AdminController extends Controller
{
    public function indexAction()
    {
        $utils = new BDCUtils;      
        if ($utils->check_session() === null) {
            return $this->redirect($this->generateUrl('user_login'));
        }
        return $this->render('BDCPollBundle:Admin:index.html.twig');
    }
}
