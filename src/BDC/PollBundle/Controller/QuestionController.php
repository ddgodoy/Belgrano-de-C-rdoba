<?php

namespace BDC\PollBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use BDC\PollBundle\Entity\Poll;
use BDC\PollBundle\Entity\Answer;
use BDC\PollBundle\Form\PollType;
use BDC\PollBundle\Service\BDCUtils;

/**
 * Poll controller.
 *
 */
class QuestionController extends Controller {


    public function formAction(Request $request, $id = null) {
        
        $utils = new BDCUtils;      
        if ($utils->checkSession() === null) {
            return $this->redirect($this->generateUrl('user_login'));
        }
        
        $utils = new BDCUtils;


        $str_action = $id ? 'Editar' : 'Nueva';
        $rute = $id ? 'poll_form_edit' : 'poll_form';
        $parameter = $id ? ['id' => $id] : [];
        $url = $this->generateUrl($rute, $parameter);
        $em = $this->getDoctrine()->getManager();
        $poll_repo = $em->getRepository('BDCPollBundle:Poll');



        $js = array('js/plugins/jquery-validation/js/jquery.validate.min.js', 'js/plugins/jquery-validation/js/localization/messages_es_AR.js', 'js/question/edit.js');

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
        
        $group_votes = $em->getRepository('BDCPollBundle:Question')->getVotesGrouped($id);
        $associates = $em->getRepository('BDCPollBundle:Associate')->findAll();
        
             
        //echo '<pre>';
        //print_r($group_votes);
        //echo '</pre>';
        
        //echo '<pre>';
        //print_r($associates);
        //echo '</pre>'; 
        
        $request_params = $this->get('request')->request->all();
        $entity = $em->getRepository('BDCPollBundle:Question')->find($id);
        $answers = $em->getRepository('BDCPollBundle:Answer')->findBy(array('id_question' => $id));
        
        $votes = $em->getRepository('BDCPollBundle:Question')->getVotes($id);
        
        $total_votes = $em->getRepository('BDCPollBundle:Vote')->findBy(array('id_question' => $id));
        
        if (isset($request_params['pie_chart_data'])) {

            echo $utils->pie_chart_data($votes, $answers);
            exit;
        }
        
        if (isset($request_params['bar_chart_data'])) {
            $group_votes = $em->getRepository('BDCPollBundle:Question')->getVotesGrouped($id);
            echo $utils->bar_chart_data($group_votes, $answers, $associates);   
            exit;
        }
       
        if (count($request_params) > 0) {
            $answer_repo = $em->getRepository('BDCPollBundle:Answer');
            
            if (isset($request_params['delete'])) {
                $answer = $answer_repo->findOneById($request_params['id_answer']);
                $em->remove($answer);
                $em->flush();
                echo json_encode(array('status' => 'success'));
                exit;
            }
            
            $answer_name = $request_params['answer'];
            
            if ($answer_name != '') {
                    
                if (isset($request_params['id_answer'])) {
                    $answer = $answer_repo->findOneById($request_params['id_answer']);
                } else {
                    $answer = new Answer();
                }
                $answer->answer = $answer_name;
                $answer->id_poll = $request_params['id_poll'];
                $answer->id_question = $request_params['id_question'];
                $em->persist($answer);
                $em->flush();
                echo json_encode(array('status' => 'success'));
                exit;
            }
        }

       
   
        $poll = $em->getRepository('BDCPollBundle:Poll')->find($entity->id_poll);
        
        


        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Poll entity.');
        }
        
        $js = array('js/plugins/jquery-validation/js/jquery.validate.min.js', 
            'js/plugins/jquery-validation/js/localization/messages_es_AR.js',
            'js/plugins/flot/excanvas.min.js',
            'js/plugins/flot/jquery.flot.js',
            'js/plugins/flot/jquery.flot.pie.js',
            'js/plugins/flot/jquery.flot.resize.js',
            'js/plugins/flot/jquery.flot.tooltip.min.js',
            'js/plugins/morris/raphael.min.js',
            'js/plugins/morris/morris.min.js',
            'js/question/show.js',
      
               );
                 
        return $this->render('BDCPollBundle:Question:show.html.twig', array('entity' => $entity, 'votes' => $votes, 'total_votes' => $total_votes, 'poll' => $poll, 'answers' => $answers, 'js' => $js  ));
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
