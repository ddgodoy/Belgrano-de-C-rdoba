<?php

namespace BDC\PollBundle\Entity;



use Doctrine\ORM\Mapping as ORM;

/**
 * User
 */
class User
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $email;
    
     /**
     * @var string
     */
    private $gender;

    /**
     * @var string
     */
    private $password;

    /**
     * @var \DateTime
     */
    private $created;

    /**
     * @var \DateTime
     */
    private $modified;
    
        /**
     * @var string
     */
    private $last_name;

    /**
     * @var string
     */
    private $dni;

    /**
     * @var integer
     */
    private $associate_id;

    /**
     * @var string
     */
    private $role;

    private $salt;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return User
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }
    
      /**
     * Set gender
     *
     * @param string $gender
     * @return User
     */
    public function setGender($gender)
    {
        $this->gender = $gender;

        return $this;
    }

    /**
     * Get gender
     *
     * @return string 
     */
    public function getGender()
    {
        return $this->gender;
    }

    /**
     * Set password
     *
     * @param string $password
     * @return User
     */
    public function setPassword($password)
    {   
        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string 
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return User
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime 
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set modified
     *
     * @param \DateTime $modified
     * @return User
     */
    public function setModified($modified)
    {
        $this->modified = $modified;

        return $this;
    }

    /**
     * Get modified
     *
     * @return \DateTime 
     */
    public function getModified()
    {
        return $this->modified;
    }


    /**
     * Set last_name
     *
     * @param string $lastName
     * @return User
     */
    public function setLastName($lastName)
    {
        $this->last_name = $lastName;

        return $this;
    }

    /**
     * Get last_name
     *
     * @return string 
     */
    public function getLastName()
    {
        return $this->last_name;
    }

    /**
     * Set dni
     *
     * @param string $dni
     * @return User
     */
    public function setDni($dni)
    {
        $this->dni = $dni;

        return $this;
    }

    /**
     * Get dni
     *
     * @return string 
     */
    public function getDni()
    {
        return $this->dni;
    }

    /**
     * Set associate_id
     *
     * @param integer $associateId
     * @return User
     */
    public function setAssociateId($associateId)
    {
        $this->associate_id = intval($associateId);

        return $this;
    }

    /**
     * Get associate_id
     *
     * @return integer 
     */
    public function getAssociateId()
    {
        return $this->associate_id;
    }

    /**
     * Set role
     *
     * @param string $role
     * @return User
     */
    public function setRole($role)
    {
        $this->role = $role;

        return $this;
    }

    /**
     * Get role
     *
     * @return string 
     */
    public function getRole()
    {
        return $this->role;
    }
    /**
     * @var \BDC\PollBundle\Entity\Associate
     */
    private $associate;


    /**
     * Set associate
     *
     * @param \BDC\PollBundle\Entity\Associate $associate
     * @return User
     */
    public function setAssociate(\BDC\PollBundle\Entity\Associate $associate = null)
    {
        $this->associate = $associate;

        return $this;
    }

    /**
     * Get associate
     *
     * @return \BDC\PollBundle\Entity\Associate 
     */
    public function getAssociate()
    {
        return $this->associate;
    }
    
    public function getSalt() {
        return $this->salt;

    }
    
    public function setSalt($salt) {
        $this->salt = $salt;
        return $this;
    }
}
