<?php

namespace BDC\PollBundle\Repository;

use Doctrine\ORM\EntityRepository;


class VoteRepository extends EntityRepository {

    /**
     * get Count Partners Vote
     * @param int $id_poll
     * 
     */
    function getCountPartnersVote($id_poll){
        
        $em = $this->getEntityManager();
        $query = $em->createQueryBuilder()
                ->select('COUNT(v.id_user)') 
                ->from('BDCPollBundle:Vote', 'v')
                ->where('v.id_poll = :id_poll')
                ->groupBy('v.id_poll')
                ->setParameter('id_poll', $id_poll);

        $count_partner = $query->getQuery()->getOneOrNullResult();
        
        return $count_partner==NULL?0:$count_partner;
    }
    
    /**
     * get Partners Vote
     * @param int $id_poll
     * @return array
     */
    function getPartnersVote($id_poll){
        
         $stmt = $this->getEntityManager()
                 ->getConnection()
                 ->prepare('SELECT u.id, u.name, u.last_name, u.email, u.dni
                            FROM Vote v 
                            LEFT JOIN User u ON v.id_user = u.id
                            WHERE v.id_poll = '.$id_poll.'
                            GROUP BY v.id_user');
         
         $stmt->execute();
        
         return $stmt->fetchAll();
    }

    /**
     * @param $id_poll
     * @param $id_user
     * @return array
     * @throws \Doctrine\DBAL\DBALException
     */
    function getVoteForUser($id_poll, $id_user){

        $stmt = $this->getEntityManager()
            ->getConnection()
            ->prepare("SELECT q.question AS question, a.answer AS answer   
                       FROM Vote v 
                       LEFT JOIN Answer a ON v.id_answer = a.id 
                       LEFT JOIN Question q ON v.id_question = q.id 
                       WHERE v.id_user = $id_user  AND v.id_poll = $id_poll");

        $stmt->execute();

        return $stmt->fetchAll();

    }
}
