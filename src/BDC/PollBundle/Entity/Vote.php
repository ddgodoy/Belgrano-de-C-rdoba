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
     * @var string
     */
    public $id_poll;

    /**
     * @var string
     */
    public $id_answer;
    
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