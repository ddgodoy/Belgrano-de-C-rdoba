<?php
//app/console sent:emailpoll

namespace BDC\PollBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use BDC\PollBundle\Service\BDCUtils;

class sendEmailCommand extends ContainerAwareCommand
{
    protected function configure()
    {
      parent::configure();

      $this->setName('sent:emailpoll')
           ->setDescription('Envío de encuestas a usuarios socios');
    }
    
    //
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $utils = new BDCUtils;
        // leeo el xml con curl desde el link
        // este link es de prueba
        // remplazar por el link de ellos 
        $sXML = $this->download_page('file:///home/mauro/webdav_belgrano/MAIL_ENCUESTAS_15-05-2017_170000.xml');
        $oXML = new \SimpleXMLElement($sXML);
        
        /*$d = $this->getContainer()->get('doctrine');
        $em = $d->getManager(); 
        
        // dejo el id de la encuesta fijo por que siempre es la misma encuesta 
        // cambiar el id cuando cree la encuesta 
        $poll_id = 15;
        $token = md5($poll_id);
        $poll = $em->getRepository('BDCPollBundle:Poll')->find($poll_id);
        $questions = $em->getRepository('BDCPollBundle:Question')->findBy(array('id_poll' => $poll_id));
        $answers = $em->getRepository('BDCPollBundle:Answer')->findBy(array('id_poll' => $poll_id));

        $action =  $url = $this->generateUrl('front_vote',array(), true);*/
        
        // recorro el xml 
        foreach($oXML as $oEntry){
            $email = $oEntry->mail."\n";
            echo $email;
            /*if($email){
                $user = $em->getRepository('BDCPollBundle:User')->findOneByEmail($email);
                // si no existe el usuario lo creo
                if(!$user){
                    $user->setName($oEntry->nombre);
                    $user->setEmail($email);
                    $user->setLastName($oEntry->apellido);
                    $user->setDni($oEntry->codigo);
                    $user->setAssociateId(1);
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
                $link = 'http://belgrano.icox.mobi/vote/show/'.$token.'/'.$email;
                
                //$link = 'http://encuestas.belgranocordoba.com/vote/show/'.$token.'/'.$email;
               
                $link_code = '<div>Si no puede visualizar la Encuesta <a href="'.$link.'" target="_blanck">Click Aqui</a></div><br/><br/><br/>';
               
                // creo el codigo de la encuesta por cada usuario
                $form_code = $utils->generate_form_code($poll, $questions, $answers, $action, $user, $link_code);
                
                // creo el array de envio 
                $replacements[$email] = array (
                    "{user}" => $email,
                    "{body}" => $form_code
                ); 
            }*/
            
        }
        exit();
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