<?php

namespace Mommy\ProfilBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;
use Doctrine\ORM\Mapping as ORM;

use Mommy\SecurityBundle\Entity\User;

/**
 * Mommy\ProfilBundle\Entity\Pregnancy
 *
 * @ORM\Table(name="mv_pregnancy", indexes={@ORM\Index(name="search_idx", columns={"id", "uid"})})
 * @ORM\Entity()
 */
class Pregnancy
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
     * @ORM\Column(type="integer", nullable=true)
     */
    private $date;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $amenorrhee;

    /**
     * @ORM\ManyToOne(targetEntity="PregnancySpeed")
     * @ORM\JoinColumn(name="sid", referencedColumnName="id", nullable=true)
     */
    private $speed;

    /**
     * @ORM\ManyToOne(targetEntity="Reaction")
     * @ORM\JoinColumn(name="rid", referencedColumnName="id", nullable=true)
     */
    private $reaction;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $prems;

    public function getId() {
        return $this->id;
    }    

    public function getUser() {
        return $this->user;
    }

    public function setUser(User $user) {
        $this->user = $user;
    }

    public function getDate() {
        return date('d-m-Y', $this->date);
    }

    public function setDate($date) {
        $date = strtotime($date);
        $this->date = $date;
    }

    public function setAmenorrhee($weeks) {
        if (is_integer($weeks))
            $this->amenorrhee = $weeks;
        else
            $this->amenorrhee = 0;
    }

    public function getAmenorrhee() {
        return $this->amenorrhee;
    }

    public function setSpeed(PregnancySpeed $speed) {
        $this->speed = $speed;
    }
 
    public function getSpeed() {
        return $this->speed;
    }

    public function setReaction(Reaction $reaction) {
        $this->reaction = $reaction;
    }

    public function getReaction() {
        return $this->reaction;
    }

    public function getPrems() {
        return $this->prems;
    }

    public function setPrems($prems) {
        $this->prems = ($prems === true);
    }
}