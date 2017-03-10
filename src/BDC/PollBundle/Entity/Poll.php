<?php

namespace BDC\PollBundle\Entity;



use Doctrine\ORM\Mapping as ORM;

/**
 * User
 */
class Poll
{
    /**
     * @var integer
     */
    public $id;

    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $slug;

    /**
     * @var string
     */
    public $status;

    /**
     * @var string
     */
    public $id_user;
    
    /**
     * @var \DateTime
     */
    public $created;

    /**
     * @var \DateTime
     */
    public $modified;
   
}
