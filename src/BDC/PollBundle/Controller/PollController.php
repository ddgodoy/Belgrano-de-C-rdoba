<?php

namespace BDC\PollBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use BDC\PollBundle\Entity\Poll;
use BDC\PollBundle\Entity\Question;
use BDC\PollBundle\Entity\Vote;
use BDC\PollBundle\Form\PollType;
use BDC\PollBundle\Service\BDCUtils;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Poll controller.
 *
 */
class PollController extends Controller {

    /**
     * Lists al Poll entities.
     *
     */
    public function indexAction() {
        
        $session = new Session();
        $user    = $session->get('user');
        
        $utils = new BDCUtils;      
        if ($utils->check_session() === null) {
            return $this->redirect($this->generateUrl('user_login'));
        }
        
        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('BDCPollBundle:Poll')->findBy(['id_user'=>$user->getId()]);

        return $this->render('BDCPollBundle:Poll:index.html.twig', array(
                    'entities' => $entities, 'js' => array('js/poll/index.js'),
        ));
    }

    public function formAction(Request $request, $id = null) {

        $utils = new BDCUtils;      
        if ($utils->check_session() === null) {
            return $this->redirect($this->generateUrl('user_login'));
        }

        $session = new Session();
        $user    = $session->get('user');

        $str_action = $id ? 'Editar' : 'Nueva';
        $rute = $id ? 'poll_form_edit' : 'poll_form';
        $parameter = $id ? ['id' => $id] : [];
        $url = $this->generateUrl($rute, $parameter);
        $em = $this->getDoctrine()->getManager();
        $poll_repo = $em->getRepository('BDCPollBundle:Poll');



        $js = array('js/plugins/jquery-validation/js/jquery.validate.min.js', 'js/plugins/jquery-validation/js/localization/messages_es_AR.js', 'js/poll/edit.js');

        if ($id) {
            $ok_message = 'La encuesta se ha editado correctamente.';
            $error_message = 'Ha ocurrido un problema y la encuesta no se pudo editar, por favor inténtelo nuevamente más tarde.';

            $poll = $poll_repo->findOneById($id);
        } else {
            $ok_message = 'El usuario se ha creado correctamente.';
            $error_message = 'Ha ocurrido un problema y el usuario no se pudo crear, por favor inténtelo nuevamente más tarde.';

            $poll = new Poll();
        }


        $form = $this->createForm(new PollType(), $poll);
        $form->handleRequest($request);

        $params = array(
            'entity' => $poll,
            'form' => $form->createView(),
            'str_action' => $str_action,
            'url' => $url,
            'id' => $id,
            'js' => $js,
            'id_user'=>$user->getId());



        if ($form->isSubmitted() === true) {

            $request_params = $this->get('request')->request->all();
            //por el momento nada extra por validar
            $validate = true;


            if ($validate === true) {
                if (!$id) {
                    $poll->created = new \DateTime();
                }
                $poll->modified = new \DateTime();

                $poll->slug = $utils->slugify($request_params['bdc_pollbundle_poll']['name']);

                $file = $poll->image_header;

                if(is_file($file))
                {
                    // Generate a unique name for the file before saving it
                    $fileName = md5(uniqid()).'.'.$file->guessExtension();

                    // Move the file to the directory where brochures are stored
                    $file->move(
                        $this->container->getParameter('images_directory'),
                        $fileName
                    );
                    $poll->image_header = $fileName;
                }

                $file = $poll->image_footer;

                if(is_file($file))
                {
                    // Generate a unique name for the file before saving it
                    $fileName = md5(uniqid()).'.'.$file->guessExtension();

                    // Move the file to the directory where brochures are stored
                    $file->move(
                        $this->container->getParameter('images_directory'),
                        $fileName
                    );
                    $poll->image_footer = $fileName;
                }


                $em->persist($poll);
                $em->flush();
                $params['message'] = array('status' => 'success', 'text' => $ok_message);

                return $this->redirect($this->generateUrl('poll_show',array('id' => $poll->id)));
                
            } else {
                $params['message'] = array('status' => 'danger', 'text' => $error_message);
            }
        }

        return $this->render('BDCPollBundle:Poll:form.html.twig', $params);
    }

    /**
     * Finds and displays a Poll entity.
     *
     */
    public function showAction(Request $request, $id) {
        
        $utils = new BDCUtils;      
        if ($utils->check_session() === null) {
            return $this->redirect($this->generateUrl('user_login'));
        }

        $em = $this->getDoctrine()->getManager();
        $votes = $em->getRepository('BDCPollBundle:Vote')->findBy(array('id_question' => $id));
        $request_params = $this->get('request')->request->all();

        if (count($request_params) > 0) {
            $question_repo = $em->getRepository('BDCPollBundle:Question');
            
            if (isset($request_params['delete'])) {
                $question = $question_repo->findOneById($request_params['id_question']);
                $em->remove($question);
                $em->flush();
                echo json_encode(array('status' => 'success'));
                exit;
            }
            
            $question_name = $request_params['question'];
            //die(print_r($request_params));
            
            if ($question_name != '') {
                    
                if (isset($request_params['id_question'])) {
                    $question = $question_repo->findOneById($request_params['id_question']);
                } else {
                    $question = new Question();
                }
                $question->question = $question_name;
                $question->id_poll = $request_params['id_poll'];
                $em->persist($question);
                $em->flush();
                echo json_encode(array('status' => 'success'));
                exit;
            }
        }

        $entity = $em->getRepository('BDCPollBundle:Poll')->find($id);
        $questions = $em->getRepository('BDCPollBundle:Question')->findBy(array('id_poll' => $id));
        

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Poll entity.');
        }
        
        $js = array('js/plugins/jquery-validation/js/jquery.validate.min.js', 'js/plugins/jquery-validation/js/localization/messages_es_AR.js', 'js/poll/show.js');
        
        $token = md5($id);

        return $this->render('BDCPollBundle:Poll:show.html.twig', array(
                    'entity' => $entity,
                    'questions' => $questions,
                    'votes' => $votes,
                    'js' => $js,
                    'token'=> $token,
                    'link'=>$request->getSchemeAndHttpHost().'/web/vote/show/'.$token.'/*|EMAIL|*')
        );
    }

    /**
     * Deletes a Poll entity.
     *
     */
    public function deleteAction(Request $request, $id) {
        
        $utils = new BDCUtils;      
        if ($utils->check_session() === null) {
            return $this->redirect($this->generateUrl('user_login'));
        }
        
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('BDCPollBundle:Poll')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Poll entity.');
        }

        $em->remove($entity);
        $em->flush();
        echo json_encode(array('status' => 'success'));
        exit;
        return $this->redirect($this->generateUrl('poll'));
    }
    
    public function generateAction($id) {
        
        $utils = new BDCUtils();
        
        if ($utils->check_session() === null) {
            return $this->redirect($this->generateUrl('user_login'));
        }
        
        $em = $this->getDoctrine()->getManager();
        $js = array('js/plugins/prism/prism.js','js/poll/generate.js');
        $css = array('js/plugins/prism/prism.css'); 
        $entity = $em->getRepository('BDCPollBundle:Poll')->find($id);
        $questions = $em->getRepository('BDCPollBundle:Question')->findBy(array('id_poll' => $id));
        $answers = $em->getRepository('BDCPollBundle:Answer')->findBy(array('id_poll' => $id));
        
        $action =  $url = $this->generateUrl('front_vote',array(), true);
        $form_code = $utils->generate_form_code($entity, $questions, $answers, $action);
        
        return $this->render('BDCPollBundle:Poll:generate.html.twig', array(
                    'entity' => $entity,'form_code' => $form_code ,'js' => $js, 'css' => $css)
        );
    } 
    
    public function voteAction(Request $request) {
        
        
        $request_params = $this->get('request')->request->all();
        $id_poll = !empty($request_params['id_poll'])?$request_params['id_poll']:'';
        $email = !empty($request_params['email'])?$request_params['email']:'';
        $answers = !empty($request_params['answers'])?$request_params['answers']:'';
        
        if ($id_poll=='') {
             return $this->render('BDCPollBundle:Front:vote.html.twig', array('text' =>  '<strong>La Encuesta no puede encontrarse.</strong><br/>Si considera que esto es un error por favor comuníquese con el administador del sitio.', 'vote_result' => 'danger'));
        }
        
        $em = $this->getDoctrine()->getManager();
        $poll = $em->getRepository('BDCPollBundle:Poll')->find($id_poll);
        if (count($poll) === 0) {
             return $this->render('BDCPollBundle:Front:vote.html.twig', array('<strong>La Encuesta no puede encontrarse.</strong><br/>Si considera que esto es un error por favor comuníquese con el administador del sitio.', 'vote_result' => 'danger'));
        }
        $associate = $em->getRepository('BDCPollBundle:User')->findOneBy(array('email' => $email ));
        if (count($associate) === 0) {
             return $this->render('BDCPollBundle:Front:vote.html.twig', array('text' =>  '<strong>El e-mail no pertenece a ningún socio.</strong><br/> Si considera que esto es un error por favor comuníquese con el administador del sitio.', 'vote_result' => 'danger'));
        }
        
     
        $questions = $em->getRepository('BDCPollBundle:Question')->findBy(array('id_poll' => $id_poll));
        //$answers = $em->getRepository('BDCPollBundle:Answer')->findBy(array('id_poll' => $id_poll));
         $voted = false;
        foreach ($answers as $id_question => $id_answer) {
            $already_voted = $em->getRepository('BDCPollBundle:Vote')->findBy(array('id_poll' => $id_poll, 'id_user' => $associate->getId(), 'id_answer' => $id_answer));
           
            if (count($already_voted) === 0) {
                $vote = new Vote();
                $voted = true;
                $vote->id_poll = $id_poll;
                $vote->id_question = $id_question;
                $vote->id_answer = $id_answer;
                $vote->id_user = $associate->getId();
                $vote->created = new \DateTime('now');
               
                $em->persist($vote);
                $em->flush();
            }
        }
        
        $vote_result = 'success';
        if ($voted === false) {
            $vote_result = 'already_voted';
        }
        return $this->render('BDCPollBundle:Front:vote.html.twig', array('vote_result' => $vote_result)); 
    }
    
    public function front_showAction(Request $request) {
        $user_email = $request->get('email');
        $id_poll_token = $request->get('token');
            
        $em = $this->getDoctrine()->getManager();
        $poll = $em->getRepository('BDCPollBundle:Poll')->getByMD5Id($id_poll_token);
        $user = $em->getRepository('BDCPollBundle:User')->findOneByEmail($user_email);

        if ( ($poll !== false) && ($user !== false) ) {
            /*$questions = $em->getRepository('BDCPollBundle:Question')->findBY(array('id_poll' => $poll['id']));
            $answers = $em->getRepository('BDCPollBundle:Answer')->findBY(array('id_poll' => $poll['id']));*/
            $utils = new BDCUtils;
            
            //$form = $utils->generate_form_code_web($questions, $answers);
            
            $js = array('js/plugins/prism/prism.js','js/poll/generate.js');
            $css = array('js/plugins/prism/prism.css'); 
            $entity = $em->getRepository('BDCPollBundle:Poll')->find($poll['id']);
            $questions = $em->getRepository('BDCPollBundle:Question')->findBy(array('id_poll' => $poll['id']));
            $answers = $em->getRepository('BDCPollBundle:Answer')->findBy(array('id_poll' => $poll['id']));

            $action =  $url = $this->generateUrl('front_vote',array(), true);
            $form_code = $utils->generate_form_code($entity, $questions, $answers, $action, $user);
            
            return $this->render('BDCPollBundle:Front:show.html.twig', 
                    array('form_code' => $form_code ,'js' => $js, 'css' => $css));         
        }
             
    }
        
}
