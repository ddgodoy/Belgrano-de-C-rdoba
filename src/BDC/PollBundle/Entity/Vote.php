<?php

namespace BDC\PollBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Vote
 */
class Vote
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
    public $id_answer;
    
     /**
     * @var integer
     */
    public $id_question;
    
    /**
     * @var string
     */
    public $id_user;

    /**
     * @var string
     */
    public $name;
    
    /**
     * @var \DateTime
     */
    private $created;

     
   
}