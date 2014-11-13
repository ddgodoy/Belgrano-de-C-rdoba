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
     * @var string
     */
    public $id_poll;

    /**
     * @var string
     */
    public $name;
     
   
}
