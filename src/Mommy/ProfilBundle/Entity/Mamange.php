<?php

namespace Mommy\ProfilBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;
use Doctrine\ORM\Mapping as ORM;

use Mommy\SecurityBundle\Entity\User;

/**
 * Mommy\ProfilBundle\Entity\Mamange
 *
 * @ORM\Table(name="mv_mamange", indexes={@ORM\Index(name="search_idx", columns={"id", "uid"})})
 * @ORM\Entity()
 */
class Mamange
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
    private $since;

    /**
     * @ORM\ManyToOne(targetEntity="MamangeAge")
     * @ORM\JoinColumn(name="age_id", referencedColumnName="id", nullable=true)
     */
    private $age;

    /**
     * @ORM\ManyToOne(targetEntity="MamangeCase")
     * @ORM\JoinColumn(name="case_id", referencedColumnName="id", nullable=true)
     */
    private $case;

    /**
     * @ORM\ManyToOne(targetEntity="MamangeIVG")
     * @ORM\JoinColumn(name="ivg_id", referencedColumnName="id", nullable=true)
     */
    private $ivg;

    /**
     * @ORM\ManyToOne(targetEntity="MamangeIMG")
     * @ORM\JoinColumn(name="img_id", referencedColumnName="id", nullable=true)
     */
    private $img;

    /**
     * @ORM\ManyToOne(targetEntity="MamangeDisease")
     * @ORM\JoinColumn(name="disease_id", referencedColumnName="id", nullable=true)
     */
    private $disease;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $FollowUp;

    /**
     * @ORM\ManyToOne(targetEntity="MamangeCouple")
     * @ORM\JoinColumn(name="couple_id", referencedColumnName="id", nullable=true)
     */
    private $couple;

    /**
     * @ORM\ManyToOne(targetEntity="MamangeLife")
     * @ORM\JoinColumn(name="life_id", referencedColumnName="id", nullable=true)
     */
    private $life;

    /**
     * @ORM\ManyToOne(targetEntity="MamangeBaby")
     * @ORM\JoinColumn(name="baby_id", referencedColumnName="id", nullable=true)
     */
    private $baby;

    public function __construct() {
        $this->FollowUp = json_encode(array());
    }
    
    public function getId() {
        return $this->id;
    }    

    public function getUser() {
        return $this->user;
    }

    public function setUser(User $user) {
        $this->user = $user;
    }

    public function getSince() {
        return date('m-Y', $this->since);
    }

    public function setSince($since) {
        $since = '01-'.$since;
        $since = strtotime($since);
        $this->since = $since;
    }

    public function getAge() {
        return $this->age;
    }

    public function setAge(MamangeAge $age) {
        $this->age = $age;
    }

    public function getCase() {
        return $this->case;
    }

    public function setCase(MamangeCase $case) {
        $this->case = $case;
    }

    public function getIVG() {
        return $this->ivg;
    }

    public function setIVG(MamangeIVG $ivg) {
        $this->ivg = $ivg;
    }

    public function getIMG() {
        return $this->img;
    }

    public function setIMG(MamangeIMG $img) {
        $this->img = $img;
    }

    public function getDisease() {
        return $this->disease;
    }

    public function setDisease(MamangeDisease $disease) {
        $this->disease = $disease;
    }

    public function getFollowUp() {
        $FollowUp = json_decode($this->FollowUp, true);
        if (is_null($FollowUp))
            return array();
        return $FollowUp;
    }

    public function setFollowUp($FollowUp) {
        $FollowUp = trim($FollowUp);
        if (empty($FollowUp)) return ;
        $this->FollowUp = json_encode(explode(',', $FollowUp));
    }

    public function getCouple() {
        return $this->couple;
    }

    public function setCouple(MamangeCouple $couple) {
        $this->couple = $couple;
    }

    public function getLife() {
        return $this->life;
    }

    public function setLife(MamangeLife $life) {
        $this->life = $life;
    }

    public function getBaby() {
        return $this->baby;
    }

    public function setBaby(MamangeBaby $baby) {
        $this->baby = $baby;
    }

}