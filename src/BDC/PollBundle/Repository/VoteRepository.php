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
                        'SELECT COUNT(`id_user`) AS count_s
                         FROM BDCPollBundle:Vote v
                         WHERE id_poll = :id_poll
                         GROUP BY id_poll'
                )->setParameter('id_poll', $id_poll);

        $count_partner = $query->getResult();
        
        return $count_partner;
    }
}
