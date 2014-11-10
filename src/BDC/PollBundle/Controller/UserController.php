<?php


namespace BDC\PollBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use BDC\PollBundle\Entity\User;
use BDC\PollBundle\Entity\Associate;


use BDC\PollBundle\Form\UserType;

/**
 * User controller.
 *
 */
class UserController extends Controller
{

    /**
     * Lists all User entities.
     *
     */
    public function indexAction()
    {       
       $em = $this->getDoctrine()->getManager();
       $entities =  $em->getRepository('BDCPollBundle:User')->findAll();
       
       return $this->render('BDCPollBundle:User:index.html.twig', array(
            'entities' => $entities,'js' => array('js/user/index.js'),
        ));
    }
    
    public function formAction(Request $request, $id=Null)
    {
        $str_action  = $id?'Editar':'Nuevo';
        $rute        = $id?'user_form_edit':'user_form';
        $parameter   = $id?['id'=>$id]:[];
        $url         = $this->generateUrl($rute,$parameter);
        $em          = $this->getDoctrine()->getManager();
        if($id)
        {
            $user = $em->getRepository('BDCPollBundle:User')->findOneById($id);
        }
        else
        {
            $user = new User();
        }    
        
        $form = $this->createForm(new UserType($em), $user);
        $form->handleRequest($request);
        
        if ($form->isValid()) {
            $associate = $em->getRepository('BDCPollBundle:Associate')->findOneById($user->getAssociateId());
            $user->setAssociate($associate);
            if(!$id){
            $user->setCreated(new \DateTime());
            }
            $user->setModified(new \DateTime());
            $em->persist($user);
            $em->flush();

            return $this->redirect($this->generateUrl('user_form_edit',['id'=>$user->getId()]));
        }
        
        return $this->render('BDCPollBundle:User:form.html.twig', array(
            'entity' => $user,
            'form'   => $form->createView(),
            'str_action'=>$str_action,
            'url'=>$url,
            'id'=>$id
        ));
        
    }        

    /**
     * Finds and displays a User entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BDCPollBundle:User')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find User entity.');
        }

        return $this->render('BDCPollBundle:User:show.html.twig', array(
            'entity'      => $entity,
        ));
    }

    /**
     * Deletes a User entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('BDCPollBundle:User')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find User entity.');
        }

        $em->remove($entity);
        $em->flush();
        
        return $this->redirect($this->generateUrl('user'));
    }
}
