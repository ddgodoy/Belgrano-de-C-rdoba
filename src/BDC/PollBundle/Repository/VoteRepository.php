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
        $query = $em->createQueryBuilder(
                        'SELECT COUNT(v.id_user) AS count_s
                         FROM BDCPollBundle:Vote v
                         WHERE v.id_poll = 5
                         GROUP BY v.id_poll'
                );

        $count_partner = $query->getQuery()->getSingleScalarResult();
        
        return $count_partner;
    }
}
