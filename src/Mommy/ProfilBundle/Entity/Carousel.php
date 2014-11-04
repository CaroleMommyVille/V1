<?php

namespace Mommy\ProfilBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;
use Doctrine\ORM\Mapping as ORM;

use Mommy\SecurityBundle\Entity\User;

/**
 * Mommy\ProfilBundle\Entity\Carousel
 *
 * @ORM\Table(name="mv_carousel", indexes={@ORM\Index(name="search_idx", columns={"id", "uid", "pid"})})
 * @ORM\Entity()
  */
class Carousel
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="\Mommy\SecurityBundle\Entity\User", cascade={"remove"})
     * @ORM\JoinColumn(name="uid", referencedColumnName="id", nullable = true, onDelete="CASCADE")
     */
    private $user = null;

    /**
     * @ORM\ManyToOne(targetEntity="Pro")
     * @ORM\JoinColumn(name="pid", referencedColumnName="id", nullable = true)
     */
    private $pro = null;

    public function getId() {
        return $this->id;
    }    

    public function getUser() {
        return $this->user;
    }

    public function setUser(User $user) {
        $this->user = $user;
    }

    public function getPro() {
        return $this->pro;
    }

    public function setPro(Pro $pro) {
        $this->pro = $pro;
    }
}