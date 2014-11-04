<?php

namespace Mommy\ProfilBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;
use Doctrine\ORM\Mapping as ORM;

use Mommy\SecurityBundle\Entity\User;

/**
 * Mommy\ProfilBundle\Entity\Family
 *
 * @ORM\Table(name="mv_family", indexes={@ORM\Index(name="search_idx", columns={"id", "uid"})})
 * @ORM\Entity()
 */
class Family
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
     * @ORM\ManyToOne(targetEntity="FamilySize")
     * @ORM\JoinColumn(name="size_id", referencedColumnName="id", nullable=true)
     */
    private $size;

    /**
     * @ORM\ManyToOne(targetEntity="ChildNew")
     * @ORM\JoinColumn(name="new_id", referencedColumnName="id", nullable=true)
     */
    private $when;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $new;


    public function getId() {
        return $this->id;
    }    

    public function getUser() {
        return $this->user;
    }

    public function setUser(User $user) {
        $this->user = $user;
    }

    public function getSize() {
        return $this->size;
    }

    public function setSize(FamilySize $size) {
        $this->size = $size;
    }

    public function getWhen() {
        return $this->when;
    }

    public function setWhen(ChildNew $when) {
        $this->when = $when;
    }

    public function getNew() {
        return $this->new;
    }

    public function setNew($bool) {
        $this->new = ($bool == true);
    }
}