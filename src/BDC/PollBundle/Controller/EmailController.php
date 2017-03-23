<?php

namespace BDC\PollBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints\Email as EmailConstraint;
use Symfony\Component\HttpFoundation\Session\Session;
use BDC\PollBundle\Service\BDCUtils;

class EmailController extends Controller
{
    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $utils = new BDCUtils;
        
        if ($utils->check_session() === null) {
            return $this->redirect($this->generateUrl('user_login'));
        }
        
        $session = new Session();
        $user    = $session->get('user');
        
        $em = $this->getDoctrine()->getManager();
        $polls = $em->getRepository('BDCPollBundle:Poll')->findBy(['id_user'=>$user->getId()]);
        
        
        if($request->isMethod('POST'))
        {
            $email_send = $request->get('email-send');
            $subject    = $request->get('asunto');
            $poll_id    = $request->get('id_poll');
            
            
            $poll = $em->getRepository('BDCPollBundle:Poll')->find($poll_id);
            $questions = $em->getRepository('BDCPollBundle:Question')->findBy(array('id_poll' => $poll_id));
            $answers = $em->getRepository('BDCPollBundle:Answer')->findBy(array('id_poll' => $poll_id));

            $action =  $url = $this->generateUrl('front_vote',array(), true);
            
            $email_send = substr($email_send, 0, -1);
            
            $array_email = explode(';', $email_send);
            
            foreach ($array_email as $k=>$email_to){
               $user = $em->getRepository('BDCPollBundle:User')->findOneByEmail($email_to);
               
               $form_code = $utils->generate_form_code($poll, $questions, $answers, $action, $user);
              
               echo $form_code;
               exit();
               
            }
            
            
            
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
        }
        
        return $this->render('BDCPollBundle:Email:index.html.twig', ['polls'=>$polls]);
    }


}