<?php

namespace BDC\PollBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use BDC\PollBundle\Entity\Poll;
use BDC\PollBundle\Entity\Answer;

use BDC\PollBundle\Service\BDCUtils;

/**
 * Poll controller.
 *
 */
class StatsController extends Controller {

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
        $entities = $em->getRepository('BDCPollBundle:Poll')->getGeneralStats();
        
        return $this->render('BDCPollBundle:Stats:index.html.twig', array(
                    'entities' => $entities,
        ));
    }

    /**
     * Finds and displays a Poll entity.
     *
     */
    public function showAction($id) {

      
        $error = '';
        $em = $this->getDoctrine()->getManager();
        
        $entity = $em->getRepository('BDCPollBundle:Poll')->find($id);
        $questions = $em->getRepository('BDCPollBundle:Question')->findBy(array('id_poll' =>$id));
        if($questions){
            $js = array('js/stats/show.js', 
                'js/plugins/flot/excanvas.min.js',
                'js/plugins/flot/jquery.flot.js',
                'js/plugins/flot/jquery.flot.pie.js',
                'js/plugins/flot/jquery.flot.resize.js',
                'js/plugins/flot/jquery.flot.tooltip.min.js',
                'js/plugins/morris/raphael.min.js',
                'js/plugins/morris/morris.min.js');     

            return $this->render('BDCPollBundle:Stats:show.html.twig', array('entity' => $entity, 'questions' => $questions, 'js' => $js  ));
        }else{
            return $this->render('BDCPollBundle:Stats:errors.html.twig');
        }
    }
}
