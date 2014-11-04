<?php

namespace Mommy\PageBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;
use Doctrine\ORM\Mapping as ORM;

use Mommy\SecurityBundle\Entity\User;
use Mommy\ProfilBundle\Entity\Pro;

/**
 * Mommy\PageBundle\Entity\Like
 *
 * @ORM\Table(name="mv_page_like", indexes={@ORM\Index(name="search_idx", columns={"id", "uid", "pid"})})
 * @ORM\Entity()
  */
class Like
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
     * @ORM\ManyToOne(targetEntity="\Mommy\ProfilBundle\Entity\Pro")
     * @ORM\JoinColumn(name="pid", referencedColumnName="id")
     */
    private $page;

    public function getId() {
        return $this->id;
    }    

    public function getUser() {
        return $this->source;
    }

    public function setUser(User $user) {
        $this->source = $user;
    }

    public function getPage() {
        return $this->page;
    }

    public function setPage(Pro $page) {
        $this->page = $page;
    }

}