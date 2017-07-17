<?php

namespace BDC\PollBundle\Controller;


use BDC\PollBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints\Email as EmailConstraint;
use Symfony\Component\HttpFoundation\Session\Session;
use BDC\PollBundle\Service\BDCUtils;
use BDC\PollBundle\Service\PasswordEncrypt;

class EmailController extends Controller
{
    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $error = Null;
        $utils = new BDCUtils;
        
        if ($utils->check_session() === null) {
            return $this->redirect($this->generateUrl('user_login'));
        }
        
        $session = new Session();
        $user    = $session->get('user');
        $sent    = NULL;
        
        $em = $this->getDoctrine()->getManager();
        $polls = $em->getRepository('BDCPollBundle:Poll')->findBy(['id_user'=>$user->getId()]);
        
        
        if($request->isMethod('POST'))
        {
            $email_send = $request->get('email-send', '');
            $subject    = $request->get('asunto');
            $poll_id    = $request->get('id_poll');
            
            if($email_send != ''){
            
                $token = md5($poll_id);
                $poll = $em->getRepository('BDCPollBundle:Poll')->find($poll_id);
                $questions = $em->getRepository('BDCPollBundle:Question')->findBy(array('id_poll' => $poll_id));
                $answers = $em->getRepository('BDCPollBundle:Answer')->findBy(array('id_poll' => $poll_id));

                $action =  $url = $this->generateUrl('front_vote',array(), true);

                $email_send_t = substr($email_send, 0, -1);

                $array_email = explode(';', $email_send_t);


                foreach ($array_email as $k=>$email_to){

                  $user = $em->getRepository('BDCPollBundle:User')->findOneByEmail($email_to);


                   $link = $request->getSchemeAndHttpHost().'/web/vote/show/'.$token.'/'.$email_to;

                   $link_code = '<div>Si no puede visualizar o responder la Encuesta <a href="'.$link.'" target="_blanck">Click Aqui</a></div><br/><br/><br/>';

                   $form_code = $utils->generate_form_code($poll, $questions, $answers, $action, $user, $link_code);

                   $replacements[$email_to] = array (
                        "{user}" => $email_to,
                        "{body}" => $form_code
                    ); 
                }


                // Create the mail transport configuration
                $transport = \Swift_MailTransport::newInstance();

                $plugin = new \Swift_Plugins_DecoratorPlugin($replacements);

                $mailer = \Swift_Mailer::newInstance($transport);

                $mailer->registerPlugin($plugin);

                $message = \Swift_Message::newInstance()
                          ->setSubject('Club Belgrano | '.$subject)
                          ->setFrom("info@belgrano.com", 'Club Belgrano')
                          ->setBody('{body}','text/html')  ;

                // Send the email
                foreach($array_email as $k=>$email_to) {
                  $message->setTo($email_to);
                  $mailer->send($message);
                }

                $sent = TRUE;
            }else{
                $error = 1;
            }
            
        }
        
        return $this->render('BDCPollBundle:Email:index.html.twig', ['polls'=>$polls, 'sent'=>$sent, 'error' => $error]);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function xmlAction(Request $request)
    {
        $utils = new BDCUtils;

        if ($utils->check_session() === null) {
            return $this->redirect($this->generateUrl('user_login'));
        }

        $session = new Session();
        $user    = $session->get('user');
        $sent    = NULL;

        $em = $this->getDoctrine()->getManager();
        $polls = $em->getRepository('BDCPollBundle:Poll')->findBy(['id_user'=>$user->getId()]);


        if($request->isMethod('POST'))
        {
            $email_send = $request->get('email-send');
            $subject    = $request->get('asunto');
            $poll_id    = $request->get('id_poll');

            $token = md5($poll_id);
            $poll = $em->getRepository('BDCPollBundle:Poll')->find($poll_id);
            $questions = $em->getRepository('BDCPollBundle:Question')->findBy(array('id_poll' => $poll_id));
            $answers = $em->getRepository('BDCPollBundle:Answer')->findBy(array('id_poll' => $poll_id));

            $action =  $url = $this->generateUrl('front_vote',array(), true);

            $file = isset($_FILES['xml_file']) ? $_FILES['xml_file'] : null;


            try{
                if($file)
                {
                    if($file['type'] == 'text/xml')
                    {
                        $xml = new \SimpleXMLElement(file_get_contents($file['tmp_name']));
                        foreach ($xml as $entry){
                            $data = $this->xml2array($entry);

                            $email = $data['mail'];
                            $user = $em->getRepository('BDCPollBundle:User')->findOneByEmail($email);


                            $link = $request->getSchemeAndHttpHost().'/web/vote/show/'.$token.'/'.$email;

                            $link_code = '<div>Si no puede visualizar o reponder la Encuesta <a href="'.$link.'" target="_blanck">Click Aqui</a></div><br/><br/><br/>';

                            $form_code = $utils->generate_form_code($poll, $questions, $answers, $action, $user, $link_code);


                            $replacements[$email] = array (
                                "{user}" => $email,
                                "{body}" => $form_code
                            );
                        }

                        // Create the mail transport configuration
                        $transport = \Swift_MailTransport::newInstance();

                        $plugin = new \Swift_Plugins_DecoratorPlugin($replacements);

                        $mailer = \Swift_Mailer::newInstance($transport);

                        $mailer->registerPlugin($plugin);

                        $message = \Swift_Message::newInstance()
                            ->setSubject('Club Belgrano | '.$subject)
                            ->setFrom("info@belgrano.com", 'Club Belgrano')
                            ->setBody('{body}','text/html')  ;


                        // Send the email
                        foreach($xml as $entry) {
                            $data = $this->xml2array($entry);
                            $email = $data['mail'];
                            $message->setTo($email);
                            $mailer->send($message);
                        }

                        $sent = TRUE;
                    }else{
                        return $this->render('BDCPollBundle:Email:xml.html.twig', ['polls'=>$polls, 'sent'=>$sent, 'error' => true]);
                    }

                }
            }catch(\Exception $e)
            {
                return $this->render('BDCPollBundle:Email:xml.html.twig', ['polls'=>$polls, 'sent'=>$sent, 'error' => true]);
            }

        }

        return $this->render('BDCPollBundle:Email:xml.html.twig', ['polls'=>$polls, 'sent'=>$sent]);
    }

    private function xml2array( $xmlObject, $out = array () )
    {
        foreach ( (array) $xmlObject as $index => $node )
            $out[$index] = ( is_object ( $node ) ) ? xml2array ( $node ) : $node;

        return $out;
    }

    /**
     * crontab Action
     * @param Request $request
     */
    public function crontabAction(Request $request){
        libxml_use_internal_errors(true);
        $utils = new BDCUtils;
        // leeo el xml con curl desde el link
        // este link es de prueba
        // remplazar por el link de ellos
        $date = date("d-m-Y H0000");
        //$date = '14-07-2017 130000';
        $date = str_ireplace(' ', '_', $date);

        //echo $date;
        
        $sXML = $this->download_page('http://belgranosocios.com/xmlupload/MAIL_ENCUESTAS_'.$date.'.xml');

        $oXML = simplexml_load_string($sXML);

        /*echo '<pre>';
        print_r($oXML);
        echo '</pre>';
        exit();*/

        if ($oXML) {
            $d  = $this->getDoctrine();
            $em = $d->getManager();

            // dejo el id de la encuesta fijo por que siempre es la misma encuesta
            // cambiar el id cuando cree la encuesta
            $poll_id = 14;
            $token = md5($poll_id);
            $poll = $em->getRepository('BDCPollBundle:Poll')->find($poll_id);
            $questions = $em->getRepository('BDCPollBundle:Question')->findBy(array('id_poll' => $poll_id));
            $answers = $em->getRepository('BDCPollBundle:Answer')->findBy(array('id_poll' => $poll_id));

            $action =  $url = $this->generateUrl('front_vote',array(), true);

            // recorro el xml
            foreach($oXML as $oEntry){
                $email = $oEntry->mail;
                if($email){
                    $user = $em->getRepository('BDCPollBundle:User')->findOneByEmail($email);
                    // si no existe el usuario lo creo
                    if(!$user){
                        $associate = $em->getRepository('BDCPollBundle:Associate')->findOneById(1);

                        $user = NEW User();
                        $user->setName($oEntry->nombre);
                        $user->setEmail($email);
                        $user->setLastName($oEntry->apellido);
                        $user->setDni($oEntry->codigo);
                        $user->setAssociate($associate);
                        $user->setGender('');
                        $user->setCreated(new \DateTime());
                        $user->setModified(new \DateTime());
                        $role = 'associate';
                        $enc = new PasswordEncrypt();
                        $salt = uniqid(mt_rand());
                        $user->setSalt($salt);
                        $encoded = $enc->encodePassword(uniqid(), $salt);

                        $user->setPassword($encoded);
                        $user->setRole($role);
                        $em->persist($user);
                        $em->flush();
                    }

                    // cambiar el link del servidor segun si estaen prod o dev
                    // esto por ahora es para la prueba
                    $link = 'http://encuestas.belgranocordoba.com/web/vote/show/'.$token.'/'.$email;

                    //$link = 'http://encuestas.belgranocordoba.com/vote/show/'.$token.'/'.$email;

                    $link_code = '<div>Si no puede visualizar la Encuesta <a href="'.$link.'" target="_blanck">Click Aqui</a></div><br/><br/><br/>';

                    // creo el codigo de la encuesta por cada usuario
                    $form_code = $utils->generate_form_code($poll, $questions, $answers, $action, $user, $link_code);

                    $replacements[$email] = array (
                        "{user}" => $email,
                        "{body}" => $form_code
                    );

                }

            }

            // envio multiples email
            // Create the mail transport configuration
            $transport = \Swift_MailTransport::newInstance();

            $plugin = new \Swift_Plugins_DecoratorPlugin($replacements);

            $mailer = \Swift_Mailer::newInstance($transport);

            $mailer->registerPlugin($plugin);

            $message = \Swift_Message::newInstance()
                ->setSubject('Club Belgrano | Encuesta socios')
                ->setFrom("info@belgrano.com", 'Club Belgrano')
                ->setBody('{body}','text/html')  ;

            // Send the email
            foreach($oXML as $oEntry) {
                $message->setTo($oEntry->mail);
                $mailer->send($message);
            }

            echo 'email enviados';
        } else {
            echo 'error de docuento';
        }
        exit();
    }

    /**
     * download_page
     * @param string $path
     * @return object xml
     */
    protected function download_page($path){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$path);
        curl_setopt($ch, CURLOPT_FAILONERROR,1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION,1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        $retValue = curl_exec($ch);
        curl_close($ch);
        return $retValue;
    }

}