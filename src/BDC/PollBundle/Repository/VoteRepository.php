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
        $query = $em->createQuery(
                        'SELECT COUNT(v.id_user) AS count_s
                         FROM BDCPollBundle:Vote v
                         WHERE v.id_poll = :id_poll
                         GROUP BY v.id_poll'
                )->setParameter('id_poll', $id_poll);

        $count_partner = $query->execute();
        
        return $count_partner->fetch();
    }
}
