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
use BDC\PollBundle\Entity\Associate;

/**
 * User controller.
 *
 */
class UserController extends Controller {

    public function indexAction(Request $request) {

        $utils = new BDCUtils;


        //no encontré manera mas simple que chequear la sesión en cada action!!

        if ($utils->check_session() === null) {
            return $this->redirect($this->generateUrl('user_login'));
        }

        $js = array('js/plugins/jasny-bootstrap/js/jasny-bootstrap.min.js', 'js/pagination.js', 'js/user/index.js');
        $css = array('js/plugins/jasny-bootstrap/css/jasny-bootstrap.min.css');

        $variables = array('js' => $js, 'css' => $css, 'search' => $request->get('search'));
        $em = $this->getDoctrine()->getManager();

        if ($request->get('import') !== null) {
            $file = $request->files->get('csv_file');
            $import_result = $utils->import_users($file->getPathName(), $em);
            if ($import_result !== false) {
                $variables['message'] = array('status' => 'success', 'text' => 'Importación finalizada. Se han agregado los socios a la base.');
            } else {
                $variables['message'] = array('status' => 'danger', 'text' => 'Ha ocurrido un error. No se han agregado los socios a la base, por favor verifíque que el formato del archivo sea un .CSV válido.');
            }
        }

        $result = $em->getRepository('BDCPollBundle:User')->getPartners($request->get('page'), $request->get('amount'), $request->get('search'),  $this->get('knp_paginator'));
        $variables['entities'] = $result['entities'];
        $variables['page'] = $result['page'];
        $variables['amount'] = $result['amount'];
        $variables['total_records'] = $result['total_records'];
        $variables['pagination_nav'] = $utils->build_pagination_nav($result['total_pages'], $result['page'], 'paginate-form');
        
        
        return $this->render('BDCPollBundle:User:index.html.twig', $variables);
    }

    public function formAction(Request $request, $id = null, $profile = null) {


        $utils = new BDCUtils;
        if ($utils->check_session() === null) {
            return $this->redirect($this->generateUrl('user_login'));
        }

        $str_action = $id ? 'Editar' : 'Nuevo';
        $rute = $id ? 'user_form_edit' : 'user_form';
        $parameter = $id ? ['id' => $id] : [];
        $url = $this->generateUrl($rute, $parameter);
        $em = $this->getDoctrine()->getManager();
        $user_repo = $em->getRepository('BDCPollBundle:User');
        $user_pass = $profile ? $request->get('pass') : uniqid();
        $url = $profile ? $this->generateUrl('user_profile_edit', ['id' => 7, 'profile' => 'profile']) : $url;


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
            'js' => $js,
            'profile' => $profile);

        $associate = $em->getRepository('BDCPollBundle:Associate')->findOneById($user->getAssociateId());
        $user->setAssociate($associate);

        if ($form->isSubmitted() === true || $request->isMethod('POST')) {

            $request_params = $this->get('request')->request->all();
            $email = $request_params['bdc_pollbundle_user']['email'];
            $dni = $request_params['bdc_pollbundle_user']['dni'];

            $duplicate_email = $user_repo->duplicateEmail($email, $id);

            $validate = true;
            $change_pass = $profile ? false : true;

            if ($duplicate_email === true) {
                $validate = false;
                $error_message = 'Ya existe un usuario con el e-mail "' . $email . '". ';
            }

            $duplicate_dni = $user_repo->duplicateDni($dni, $id);

            if ($duplicate_dni === true) {
                $validate = false;
                $error_message = 'Ya existe un usuario con el DNI "' . $dni . '". ';
            }

            if ($profile) {
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
                    $encoded = $enc->encodePassword($user_pass, $salt);

                    $user->setPassword($encoded);
                }
                $role = $profile ? 'admin' : 'partners';
                $user->setRole($role);
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

    public function ajaxNewUserAction(Request $request){
        
        $utils = new BDCUtils;
        if ($utils->check_session() === null) {
            return $this->redirect($this->generateUrl('user_login'));
        }
        
        $url = $this->generateUrl('user_new_ajax');
        $em = $this->getDoctrine()->getManager();
        $user_repo = $em->getRepository('BDCPollBundle:User');
        $error_message = 'Ha ocurrido un problema y el usuario no se pudo crear, por favor inténtelo nuevamente más tarde.';
        $user = new User();
        
        $form = $this->createForm(new UserType($em), $user);
        $form->handleRequest($request);
        
        $params = array(
            'entity' => $user,
            'form' => $form->createView(),
            'url' => $url);

        $associate = $em->getRepository('BDCPollBundle:Associate')->findOneById($user->getAssociateId());
        $user->setAssociate($associate);
        
        if ($request->isMethod('POST')) {
            $request_params = $this->get('request')->request->all();
            $email = $request_params['bdc_pollbundle_user']['email'];
            $dni = $request_params['bdc_pollbundle_user']['dni'];
            

            $duplicate_email = $user_repo->duplicateEmail($email);

            $validate = true;

            if ($duplicate_email === true) {
                $validate = false;
                $error_message = 'Ya existe un usuario con el e-mail "' . $email . '". ';
            }

            $duplicate_dni = $user_repo->duplicateDni($dni);

            if ($duplicate_dni === true) {
                $validate = false;
                $error_message = 'Ya existe un usuario con el DNI "' . $dni . '". ';
            }
            
            if ($validate === true) {
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
                echo '1';
                exit();
            }else{
                echo $error_message;
                exit();
            }
        }
        return $this->render('BDCPollBundle:User:formajax.html.twig', $params);
    }
    
    /**
     * Finds and displays a User entity.
     *
     */
    public function showAction($id) {

        $utils = new BDCUtils;
        if ($utils->check_session() === null) {
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
        if ($utils->check_session() === null) {
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

    /**
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     */
    public function ajaxUserAction(Request $request){
        
        $email_search = $request->get('email');
        $email_all    = $request->get('email_all');
        
        $em = $this->getDoctrine()->getManager();
        
        $entity = $em->getRepository('BDCPollBundle:User')->getUserByEmail($email_search, $email_all);
        
        return $this->render('BDCPollBundle:User:ajaxuser.html.twig',['users'=>$entity]);
    }
    
    public function loginAction(Request $request) {

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

    public function logoutAction(Request $request) {
        $session = new Session();
        $session->remove('user');
        return $this->redirect($this->generateUrl('user_login'));
    }

}
