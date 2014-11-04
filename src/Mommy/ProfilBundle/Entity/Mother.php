<?php

namespace Mommy\ProfilBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;
use Doctrine\ORM\Mapping as ORM;

use Mommy\SecurityBundle\Entity\User;

/**
 * Mommy\ProfilBundle\Entity\Mother
 *
 * @ORM\Table(name="mv_mother", indexes={@ORM\Index(name="search_idx", columns={"id", "uid"})})
 * @ORM\Entity()
 */
class Mother
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
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $baby_blues;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $weight_ok;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $weight_time;

    /**
     * @ORM\ManyToOne(targetEntity="AfterDelivery")
     * @ORM\JoinColumn(name="after_id", referencedColumnName="id", nullable=true)
     */
    private $after;

    /**
     * @ORM\ManyToOne(targetEntity="ChildBreastfed")
     * @ORM\JoinColumn(name="breastfed_id", referencedColumnName="id", nullable=true)
     */
    private $breastfed;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $breastfed_during;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $between_time;

    public function getId() {
        return $this->id;
    }    

    public function getUser() {
        return $this->user;
    }

    public function setUser(User $user) {
        $this->user = $user;
    }

    public function getBabyBlues() {
        return $this->baby_blues;
    }

    public function setBabyBlues($baby_blues) {
        $this->baby_blues = ($baby_blues == true);
    }

    public function getWeightOk() {
        return $this->baby_blues;
    }

    public function setWeightOk($weight_ok) {
        $this->weight_ok = ($weight_ok == true);
    }

    public function getWeightTime() {
        return $this->weight_time;
    }

    public function setWeightTime($weight_time) {
        $this->weight_time = $weight_time;
    }

    public function getAfter() {
        return $this->after;
    }

    public function setAfter(AfterDelivery $after) {
        $this->after = $after;
    }

    public function getBreastfed() {
        return $this->breastfed;
    }

    public function setBreastfed(ChildBreastfed $breastfed) {
        $this->breastfed = $breastfed;
    }

    public function getBetween() {
        return $this->between_time;
    }

    public function setBetween($time) {
        $this->between_time = $time;
    }

    public function getDuring() {
        return $this->breastfed_during;
    }

    public function setDuring($breastfed_during) {
        $this->breastfed_during = $breastfed_during;
    }

}