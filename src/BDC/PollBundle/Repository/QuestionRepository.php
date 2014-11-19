<?php

namespace BDC\PollBundle\Repository;

use Doctrine\ORM\EntityRepository;

class QuestionRepository extends EntityRepository {

    function getVotes($id) {
        $id = intval($id);
        $em = $this->getEntityManager();

        $stmt = $this->getEntityManager()
                ->getConnection()
                ->prepare( 'SELECT count(*) as total_votes,a.answer FROM Vote v '
                        . 'INNER JOIN Answer a on v.id_answer = a.id '
                        . 'INNER JOIN Question q on q.id = v.id_question '
                        . 'WHERE v.id_question = :id group by(a.answer)');
        $stmt->bindValue('id', $id);
        $stmt->execute();
        return $stmt->fetchAll();

      
    }

}
