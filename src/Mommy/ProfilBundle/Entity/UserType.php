<?php

namespace Mommy\ProfilBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;
use Doctrine\ORM\Mapping as ORM;

use Mommy\SecurityBundle\Entity\User;

/**
 * Mommy\ProfilBundle\Entity\UserType
 *
 * @ORM\Table(name="mv_user_type", indexes={@ORM\Index(name="search_idx", columns={"id", "uid", "tid"})})
 * @ORM\Entity()
 */
class UserType
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="\Mommy\SecurityBundle\Entity\User", cascade={"remove"})
     * @ORM\JoinColumn(name="uid", referencedColumnName="id", onDelete="CASCADE")
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="Type")
     * @ORM\JoinColumn(name="tid", referencedColumnName="id")
     */
    private $type;

    public function getId() {
        return $this->id;
    }    

    public function getUser() {
    	return $this->user;
    }

    public function setUser(User $user) {
		$this->user = $user;
    }

    public function getType() {
        return $this->type;
    }

    public function setType(Type $type) {
        $this->type = $type;
    }

}