<?php

namespace Mommy\ProfilBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;
use Doctrine\ORM\Mapping as ORM;

use Mommy\SecurityBundle\Entity\User;

/**
 * Mommy\ProfilBundle\Entity\Adoption
 *
 * @ORM\Table(name="mv_adoption", indexes={@ORM\Index(name="search_idx", columns={"id", "uid"})})
 * @ORM\Entity()
 */
class Adoption
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
    private $arrived;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $started;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $lasted;

    /**
     * @ORM\ManyToOne(targetEntity="AdoptApproval")
     * @ORM\JoinColumn(name="approval_id", referencedColumnName="id", nullable=true)
     */
    private $approval;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $resort;

    /**
     * @ORM\ManyToOne(targetEntity="AdoptAnotherWay")
     * @ORM\JoinColumn(name="anotherway_id", referencedColumnName="id", nullable=true)
     */
    private $anotherway;

    /**
     * @ORM\ManyToOne(targetEntity="AdoptApprovalEase")
     * @ORM\JoinColumn(name="ease_id", referencedColumnName="id", nullable=true)
     */
    private $ease;

    /**
     * @ORM\ManyToOne(targetEntity="AdoptApprovalDenial")
     * @ORM\JoinColumn(name="denial_id", referencedColumnName="id", nullable=true)
     */
    private $denial;

    /**
     * @ORM\ManyToOne(targetEntity="AdoptType")
     * @ORM\JoinColumn(name="type_id", referencedColumnName="id", nullable=true)
     */
    private $type;

    /**
     * @ORM\ManyToOne(targetEntity="AdoptAge")
     * @ORM\JoinColumn(name="age_id", referencedColumnName="id", nullable=true)
     */
    private $age;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $french;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $countries;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $children;

    public function __construct() {
        $this->countries = json_encode(array());
    }

    public function getId() {
        return $this->id;
    }    

    public function getArrived() {
        return $this->arrived;
    }

    public function setArrived($arrived) {
        $this->arrived = $arrived;
    }

    public function getStarted() {
        return $this->started;
    }

    public function setStarted($started) {
        $this->started = $started;
    }

    public function getLasted() {
        return $this->lasted;
    }

    public function setLasted($lasted) {
        $this->lasted = $lasted;
    }

    public function getApproval() {
        return $this->approval;
    }

    public function setApproval(AdoptApproval $approval) {
        $this->approval = $approval;
    }

    public function getResort() {
        return $this->resort;
    }

    public function setResort($resort) {
        $this->resort = $resort;
    }

    public function getAnotherWay() {
        return $this->anotherway;
    }

    public function setAnotherWay(AdoptAnotherWay $anotherway) {
        $this->anotherway = $anotherway;
    }

    public function getEase() {
        return $this->ease;
    }

    public function setEase(AdoptApprovalEase $ease) {
        $this->ease = $ease;
    }

    public function getDenial() {
        return $this->denial;
    }

    public function setDenial(AdoptApprovalDenial $denial) {
        $this->denial = $denial;
    }

    public function getType() {
        return $this->type;
    }

    public function setType(AdoptType $type) {
        $this->type = $type;
    }

    public function getAge() {
        return $this->age;
    }

    public function setAge(AdoptAge $age) {
        $this->age = $age;
    }

    public function getChildren() {
        return $this->children;
    }

    public function setChildren($children) {
        $this->children = $children;
    }

    public function getUser() {
        return $this->user;
    }

    public function setUser(User $user) {
        $this->user = $user;
    }

    public function getFrench() {
        return $this->french;
    }

    public function setFrench($bool) {
        $this->french = ($bool & true);
    }

    public function getCountries() {
        $countries = json_decode($this->countries, true);
        if (is_null($countries))
            return array();
        return $countries;
    }

    public function setCountries($countries) {
        if (is_string($countries)) {
            $countries = trim($countries);
            if (empty($countries)) return ;
            $countries = explode(',', $countries);
        }
        $this->countries = json_encode($countries);
    }

}