<?php

namespace BDC\PollBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use BDC\PollBundle\Entity\Poll;
use BDC\PollBundle\Entity\Answer;
use BDC\PollBundle\Service\BDCUtils;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Request;

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

        if ($utils->check_session() === null) {
            return $this->redirect($this->generateUrl('user_login'));
        }

        $session = new Session();
        $user    = $session->get('user');
                
        
        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('BDCPollBundle:Poll')->getGeneralStats($user->getId());

        return $this->render('BDCPollBundle:Stats:index.html.twig', array(
                    'entities' => $entities,
        ));
    }

    /**
     * Finds and displays a Poll entity.
     *
     */
    public function showAction($id) {

        $utils = new BDCUtils;
        if ($utils->check_session() === null) {
            return $this->redirect($this->generateUrl('user_login'));
        }

        $error = '';
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BDCPollBundle:Poll')->find($id);
        $count_partner = $em->getRepository('BDCPollBundle:Vote')->getCountPartnersVote($id);
        $partner_vote  = $em->getRepository('BDCPollBundle:Vote')->getPartnersVote($id);
        $questions = $em->getRepository('BDCPollBundle:Question')->findBy(array('id_poll' => $id));
        if ($questions) {
            $js = array('js/stats/show.js',
                'js/plugins/flot/excanvas.min.js',
                'js/plugins/flot/jquery.flot.js',
                'js/plugins/flot/jquery.flot.pie.js',
                'js/plugins/flot/jquery.flot.resize.js',
                'js/plugins/flot/jquery.flot.tooltip.min.js',
                'js/plugins/morris/raphael.min.js',
                'js/plugins/morris/morris.min.js');

            return $this->render('BDCPollBundle:Stats:show.html.twig', 
                    array(  'entity' => $entity, 
                            'questions' => $questions,
                            'js' => $js,
                            'count_partner'=>$count_partner,
                            'partner_vote'=>$partner_vote,
                            'count_partner'=> count($count_partner)));
        } else {
            return $this->render('BDCPollBundle:Stats:errors.html.twig');
        }
    }

    /**
     * @param Request $request
     */
    public function userShowAction(Request $request){
        $id_poll = $request->get('id_poll');
        $id_user = $request->get('id_user');

        $em = $this->getDoctrine()->getManager();

        $user_repo = $em->getRepository('BDCPollBundle:User')->findOneById($id_user);

        $entity = $em->getRepository('BDCPollBundle:Vote')->getVoteForUser($id_poll, $id_user);
        
        return $this->render('BDCPollBundle:Stats:usershow.html.twig',
            array('entity' => $entity,
                  'user'   => $user_repo));

    }
}
