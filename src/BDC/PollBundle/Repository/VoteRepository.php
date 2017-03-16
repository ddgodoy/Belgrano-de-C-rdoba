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
}
