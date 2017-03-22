<?php

namespace BDC\PollBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints\Email as EmailConstraint;

class EmailController extends Controller
{
    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        if($request->getMethod() == "POST")
        {
            $to = 'sanchez91nestor@gmail.com';

            $message = \Swift_Message::newInstance()
                ->setSubject('Club Belgrano | ')
                ->setFrom("info@belgrano.com")
                ->setTo($to)
                ->setReplyTo("$to")
                ->setBody(
                    $this->renderView(
                        'Emails/index.html.twig',
                        array(
                            'message' => $request->request->get('message'))
                    ),
                    'text/html'
                );
            $sent = $this->get('mailer')->send($message);

            return $this->render('BDCPollBundle:Email:index.html.twig', array('sent' => $sent));
        }else{
            return $this->render('BDCPollBundle:Email:index.html.twig');
        }
    }


}