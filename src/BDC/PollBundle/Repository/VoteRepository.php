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
                ->where('v.id_poll = 5')
                ->groupBy('v.id_poll');

        $count_partner = $query->getQuery()->getSingleScalarResult();
        
        return $count_partner;
    }
}
