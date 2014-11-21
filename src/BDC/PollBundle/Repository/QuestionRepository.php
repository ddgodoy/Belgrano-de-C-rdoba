<?php

namespace BDC\PollBundle\Repository;

use Doctrine\ORM\EntityRepository;

class QuestionRepository extends EntityRepository {

    function getVotes($id) {
        
       

        $stmt = $this->getEntityManager()
                ->getConnection()
                ->prepare('SELECT count(*) as total_votes,a.answer FROM Vote v '
                . 'INNER JOIN Answer a on v.id_answer = a.id '
                . 'INNER JOIN Question q on q.id = v.id_question '
                . 'WHERE v.id_question = :id group by(a.answer)');
        $stmt->bindValue('id', intval($id));
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Retorna los votos agrupados por tipo de socio
    function getVotesGrouped($id) {
       
        $stmt = $this->getEntityManager()->getConnection()->prepare('SELECT count(*) as total_votes, a.name, an.answer from Vote v
        INNER JOIN Answer an on v.id_answer = an.id
        INNER JOIN Question q on v.id_question = q.id
        INNER JOIN User u ON v.id_user = u.id
        INNER JOIN Associate a ON u.associate_id = a.id
        WHERE v.id_question = :id
        group by a.name, an.answer');
        
        $stmt->bindValue('id', intval($id));
        $stmt->execute();
        
        return $stmt->fetchAll();
        
    }
}
