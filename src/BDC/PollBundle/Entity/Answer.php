<?php

namespace BDC\PollBundle\Entity;



use Doctrine\ORM\Mapping as ORM;

/**
 * Answer
 */
class Answer
{
    /**
     * @var integer
     */
    public $id;

    
    
    /**
     * @var integer
     */
    public $id_poll;
    
    /**
     * @var integer
     */
    public $id_question;

    /**
     * @var string
     */
    public $answer;
     
   
}
