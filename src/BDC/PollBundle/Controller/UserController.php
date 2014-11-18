<?php

namespace BDC\PollBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use BDC\PollBundle\Entity\User;
//use BDC\PollBundle\Entity\Associate;
use BDC\PollBundle\Service\PasswordEncrypt;
use BDC\PollBundle\Form\UserType;
use Symfony\Component\HttpFoundation\Session\Session;
use BDC\PollBundle\Service\BDCUtils;
/**
 * User controller.
 *
 */
class UserController extends Controller {

    
     
    public function indexAction() {
        
        //no encontré manera mas simple que chequear la sesión en cada action!!
        $utils = new BDCUtils;      
        if ($utils->checkSession() === null) {
            return $this->redirect($this->generateUrl('user_login'));
        }
        
        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('BDCPollBundle:User')->findAll();

        return $this->render('BDCPollBundle:User:index.html.twig', array(
                    'entities' => $entities, 'js' => array('js/user/index.js'),
        ));
    }

    public function formAction(Request $request, $id = null) {

        
        $utils = new BDCUtils;      
        if ($utils->checkSession() === null) {
            return $this->redirect($this->generateUrl('user_login'));
        }

        $str_action = $id ? 'Editar' : 'Nuevo';
        $rute = $id ? 'user_form_edit' : 'user_form';
        $parameter = $id ? ['id' => $id] : [];
        $url = $this->generateUrl($rute, $parameter);
        $em = $this->getDoctrine()->getManager();
        $user_repo = $em->getRepository('BDCPollBundle:User');



        $js = array('js/plugins/jquery-validation/js/jquery.validate.min.js', 'js/plugins/jquery-validation/js/localization/messages_es_AR.js');
        if ($id) {
            $ok_message = 'El usuario se ha editado correctamente.';
            $error_message = 'Ha ocurrido un problema y el usuario no se pudo editar, por favor inténtelo nuevamente más tarde.';
            $js[] = 'js/user/edit.js';
            $user = $user_repo->findOneById($id);
        } else {
            $ok_message = 'El usuario se ha creado correctamente.';
            $error_message = 'Ha ocurrido un problema y el usuario no se pudo crear, por favor inténtelo nuevamente más tarde.';
            $js[] = 'js/user/new.js';
            $user = new User();
        }



        $form = $this->createForm(new UserType($em), $user);
        $form->handleRequest($request);

        $params = array(
            'entity' => $user,
            'form' => $form->createView(),
            'str_action' => $str_action,
            'url' => $url,
            'id' => $id,
            'js' => $js);

        $associate = $em->getRepository('BDCPollBundle:Associate')->findOneById($user->getAssociateId());
        $user->setAssociate($associate);

        if ($form->isSubmitted() === true) {

            $request_params = $this->get('request')->request->all();
            $email = $request_params['bdc_pollbundle_user']['email'];
            $dni = $request_params['bdc_pollbundle_user']['dni'];

            $duplicate_email = $user_repo->duplicateEmail($email, $id);

            $validate = true;
            $change_pass = false;

            if ($duplicate_email === true) {
                $validate = false;
                $error_message = 'Ya existe un usuario con el e-mail "' . $email . '". ';
            }

            $duplicate_dni = $user_repo->duplicateDni($dni, $id);

            if ($duplicate_dni === true) {
                $validate = false;
                $error_message = 'Ya existe un usuario con el DNI "' . $dni . '". ';
            }

            if (($request->get('pass') !== '') && ($validate === true)) {
                $change_pass = true;
                if ($request->get('pass2') !== $request->get('pass')) {
                    $error_message = 'Las contraseñas ingresadas no coinciden. Por favor, ingrese nuevamente la misma contraseña en ambos campos de texto.';
                    $validate = false;
                }

                if (strlen($request->get('pass')) < 4) {
                    $error_message = 'La longitud de la contraseña debe ser mayor a 3 caracteres. Por favor, ingrese una contraseña de mayor longitud.';
                    $validate = false;
                }
            }


            if ($validate === true) {
                if (!$id) {
                    $user->setCreated(new \DateTime());
                }
                $user->setModified(new \DateTime());

                if ($change_pass === true) {
                    $enc = new PasswordEncrypt();
                    $salt = uniqid(mt_rand());
                    $user->setSalt($salt);
                    $encoded = $enc->encodePassword($request->get('pass'), $salt);
                 
                    $user->setPassword($encoded);
                }
                $em->persist($user);
                $em->flush();
                $params['message'] = array('status' => 'success', 'text' => $ok_message);
                if (!$id) {
                    return $this->redirect($this->generateUrl('user'));
                }
            } else {
                $params['message'] = array('status' => 'danger', 'text' => $error_message);
            }
        }

        return $this->render('BDCPollBundle:User:form.html.twig', $params);
    }

    /**
     * Finds and displays a User entity.
     *
     */
    public function showAction($id) {
        
        $utils = new BDCUtils;      
        if ($utils->checkSession() === null) {
            return $this->redirect($this->generateUrl('user_login'));
        }
        
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BDCPollBundle:User')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find User entity.');
        }

        return $this->render('BDCPollBundle:User:show.html.twig', array(
                    'entity' => $entity,
        ));
    }

    /**
     * Deletes a User entity.
     *
     */
    public function deleteAction(Request $request, $id) {
        
        $utils = new BDCUtils;      
        if ($utils->checkSession() === null) {
            return $this->redirect($this->generateUrl('user_login'));
        }
        
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('BDCPollBundle:User')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find User entity.');
        }

        $em->remove($entity);
        $em->flush();
        echo json_encode(array('status' => 'success'));
        exit;
        return $this->redirect($this->generateUrl('user'));
    }

    function loginAction(Request $request) {
        
                
        $em = $this->getDoctrine()->getManager();
        $params = array('action' => $this->generateUrl('user_login'));
        if ($request->get('email')) {
            $user = $em->getRepository('BDCPollBundle:User')->authenticate($request->get('email'), $request->get('password'));
            if ($user !== false) {
                $session = new Session();
                $session->set('user', $user);
                return $this->redirect($this->generateUrl('home'));
            } else {
                $params['error'] = true;
            }
        }

        return $this->render('BDCPollBundle:User:login.html.twig', $params);
    }
    
    function logoutAction() {
        $session = new Session();
        $session->remove('user');
        $this->redirect($this->generateUrl('user_login'));
    }
    
   

}
