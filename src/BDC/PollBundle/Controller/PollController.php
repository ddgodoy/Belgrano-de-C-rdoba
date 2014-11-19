<?php

namespace BDC\PollBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use BDC\PollBundle\Entity\Poll;
use BDC\PollBundle\Entity\Question;
use BDC\PollBundle\Form\PollType;
use BDC\PollBundle\Service\BDCUtils;

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
        
        $utils = new BDCUtils;      
        if ($utils->checkSession() === null) {
            return $this->redirect($this->generateUrl('user_login'));
        }
        
        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('BDCPollBundle:Poll')->findAll();

        return $this->render('BDCPollBundle:Poll:index.html.twig', array(
                    'entities' => $entities, 'js' => array('js/poll/index.js'),
        ));
    }

    public function formAction(Request $request, $id = null) {
        
        $utils = new BDCUtils;      
        if ($utils->checkSession() === null) {
            return $this->redirect($this->generateUrl('user_login'));
        }

       


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
        $form = $this->createForm(new PollType($em), $poll);
        $form->handleRequest($request);

        $params = array(
            'entity' => $poll,
            'form' => $form->createView(),
            'str_action' => $str_action,
            'url' => $url,
            'id' => $id,
            'js' => $js);



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
    public function showAction($id) {
        
        $utils = new BDCUtils;      
        if ($utils->checkSession() === null) {
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
        
        

        return $this->render('BDCPollBundle:Poll:show.html.twig', array(
                    'entity' => $entity, 'questions' => $questions, 'votes' => $votes, 'js' => $js)
        );
    }

    /**
     * Deletes a Poll entity.
     *
     */
    public function deleteAction(Request $request, $id) {
        
        $utils = new BDCUtils;      
        if ($utils->checkSession() === null) {
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

}
