<?php

namespace Mommy\ProfilBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;
use Doctrine\ORM\Mapping as ORM;

use Mommy\SecurityBundle\Entity\User;

/**
 * Mommy\ProfilBundle\Entity\Friend
 *
 * @ORM\Table(name="mv_club_friend", indexes={@ORM\Index(name="search_idx", columns={"id", "sid", "did"})})
 * @ORM\Entity()
  */
class Friend
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="\Mommy\SecurityBundle\Entity\User", cascade={"remove"})
     * @ORM\JoinColumn(name="sid", referencedColumnName="id", onDelete="CASCADE")
     */
    private $source;

    /**
     * @ORM\ManyToOne(targetEntity="\Mommy\SecurityBundle\Entity\User", cascade={"remove"})
     * @ORM\JoinColumn(name="did", referencedColumnName="id", onDelete="CASCADE")
     */
    private $dest;

    /**
     * @ORM\Column(type="boolean")
     */
    private $enabled;

    public function getId() {
        return $this->id;
    }    

    public function getSource() {
        return $this->source;
    }

    public function setSource(User $user) {
        $this->source = $user;
    }

    public function getDest() {
        return $this->dest;
    }

    public function setDest(User $user) {
        $this->dest = $user;
    }

    public function getEnabled() {
        return $this->enabled;
    }

    public function setEnabled($bool) {
        $this->enabled = (bool) $bool;
    }

}