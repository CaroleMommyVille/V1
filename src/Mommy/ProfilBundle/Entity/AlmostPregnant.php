<?php

namespace Mommy\ProfilBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;
use Doctrine\ORM\Mapping as ORM;

use Mommy\SecurityBundle\Entity\User;

/**
 * Mommy\ProfilBundle\Entity\AlmostPregnant
 *
 * @ORM\Table(name="mv_almost_pregnant", indexes={@ORM\Index(name="search_idx", columns={"id", "uid"})})
 * @ORM\Entity()
 */
class AlmostPregnant
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
     * @ORM\ManyToOne(targetEntity="PregnancyWayTo")
     * @ORM\JoinColumn(name="way_id", referencedColumnName="id", nullable=true)
     */
    private $way;

    /**
     * @ORM\ManyToOne(targetEntity="PregnancySo")
     * @ORM\JoinColumn(name="so_id", referencedColumnName="id", nullable=true)
     */
    private $so;

    /**
     * @ORM\ManyToOne(targetEntity="PregnancyDaddy")
     * @ORM\JoinColumn(name="dad_id", referencedColumnName="id", nullable=true)
     */
    private $dad;

    /**
     * @ORM\ManyToOne(targetEntity="PregnancyMood")
     * @ORM\JoinColumn(name="mood_id", referencedColumnName="id", nullable=true)
     */
    private $mood;

    /**
     * @ORM\ManyToOne(targetEntity="PregnancyHope")
     * @ORM\JoinColumn(name="hope_id", referencedColumnName="id", nullable=true)
     */
    private $hope;

    /**
     * @ORM\Column(type="boolean")
     */
    private $prems;

    public function getId() {
        return $this->id;
    }    

    public function getSince($as_int = false) {
        if ($as_int) return $this->since;
        return date('d-m-Y', $this->since);
    }

    public function setSince($since) {
        $since = '01-'.$since;
        $since = strtotime($since);
        $this->since = $since;
    }

    public function getWay() {
        return $this->way;
    }

    public function setWay(PregnancyWayTo $way) {
        $this->way = $way;
    }

    public function getSo() {
        return $this->so;
    }

    public function setSo(PregnancySo $so) {
        $this->so = $so;
    }

    public function getDad() {
        return $this->dad;
    }

    public function setDad(PregnancyDaddy $dad) {
        $this->dad = $dad;
    }

    public function getMood() {
        return $this->mood;
    }

    public function setMood(PregnancyMood $mood) {
        $this->mood = $mood;
    }

    public function getHope() {
        return $this->hope;
    }

    public function setHope(PregnancyHope $hope) {
        $this->hope = $hope;
    }

    public function getUser() {
        return $this->user;
    }

    public function setUser(User $user) {
        $this->user = $user;
    }

    public function getPrems() {
        return $this->prems;
    }

    public function setPrems($prems) {
        $this->prems = ($prems === true);
    }

}