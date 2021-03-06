<?php

namespace BDC\PollBundle\Repository;

use Doctrine\ORM\EntityRepository;
use BDC\PollBundle\Service\PasswordEncrypt;
use Knp\Bundle\PaginatorBundle\KnpPaginatorBundle;

/**
 * UserRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class UserRepository extends EntityRepository {

    //se fija si existe otro usuario con ese email y con un id distinto al pasado, se necesita validar para el alta o modificacion de un usuario
    function duplicateEmail($email, $id = null) {

        $id = intval($id);
        $em = $this->getEntityManager();
        $query = $em->createQuery(
                        'SELECT u
               FROM BDCPollBundle:User u
              WHERE u.email = :email
              AND u.id != :id'
                )->setParameter('email', $email)->setParameter('id', $id);

        $users = $query->getResult();
        return count($users) > 0;
    }

    function duplicateDni($dni, $id) {
        $id = intval($id);

        $em = $this->getEntityManager();
        $query = $em->createQuery(
                        'SELECT u
               FROM BDCPollBundle:User u
              WHERE u.dni = :dni
              AND u.id != :id'
                )->setParameter('dni', $dni)->setParameter('id', $id);

        $users = $query->getResult();
        return count($users) > 0;
    }

    function authenticate($email, $password) {

        $em = $this->getEntityManager();
        $query = $em->createQuery(
                        'SELECT u
               FROM BDCPollBundle:User u
              WHERE u.email = :email'
                )->setParameter('email', $email);

        $user = $query->getResult();
        if (count($user)) {
            $user = $user[0];
            $encodedPassword = $user->getPassword();

            $salt = $user->getSalt();

            $enc = new PasswordEncrypt();

            if ($enc->isPasswordValid($encodedPassword, $password, $salt)) {
                return $user;
            }
            return false;
        }
    }

    /**
     * get partners
     */
    function getPartners($page = null, $amount = null, $search = null, $paginator) {


        if ($page === null) {
            $page = 1;
        }

        if ($amount === null) {
            $amount = 35;
        }

        $start = ($page - 1) * $amount;

        $where = 'WHERE u.role != :rol';

        if ($search !== null) {
            $where.= " AND ( u.dni LIKE '$search%' OR concat(u.name,' ',u.last_name) LIKE '$search%' OR u.email LIKE '$search%') ";
        }

        $em = $this->getEntityManager();

        $count_query = 'SELECT count(u) FROM BDCPollBundle:User u ' . $where;

        $total_records_query = $em->createQuery($count_query)->setParameter('rol', 'admin');

        $total_records = $total_records_query->getResult();
        $total_records = intval($total_records[0][1]);
        $total_pages = ceil($total_records / $amount);

        $final_query = 'SELECT u FROM BDCPollBundle:User u ' . $where . ' ORDER BY u.name ASC, u.last_name ASC';
        
        $query = $em->createQuery($final_query)->setParameter('rol', 'admin')->setMaxResults($amount)->setFirstResult($start);


        $pagination = $paginator->paginate(
                $query, $page, $amount);

        return array('entities' => $pagination, 'total_records' => $total_records, 'total_pages' => $total_pages, 'amount' => $amount, 'page' => $page);
    }
    
    function getByMD5Id($md5_id) {
        $stmt = $this->getEntityManager()->getConnection()->prepare(
                "SELECT * from User WHERE MD5(id) = '$md5_id'");
       
        $stmt->execute();
        
        return $stmt->fetch();
    }
    
    /**
     * 
     * @param string $email
     * @return object
     */
    function getUserByEmail($email){
        
        $txt_query = "SELECT u
               FROM BDCPollBundle:User u
              WHERE u.email LIKE '%$email%' ";
        
        $em = $this->getEntityManager();
        $query = $em->createQuery($txt_query);

        $users = $query->getResult();
        
        return count($users)>0?$users:'';
    }

}
