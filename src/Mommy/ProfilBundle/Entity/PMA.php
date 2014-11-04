<?php

namespace Mommy\ProfilBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;
use Doctrine\ORM\Mapping as ORM;

use Mommy\SecurityBundle\Entity\User;

/**
 * Mommy\ProfilBundle\Entity\PMA
 *
 * @ORM\Table(name="mv_pma", indexes={@ORM\Index(name="search_idx", columns={"id", "uid"})})
 * @ORM\Entity()
 */
class PMA
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
    private $pregnant;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $amenorrhee;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $date;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $since;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $stimulation_length;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $gyneco;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $risk;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $stimulator;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $pump;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $pump_side_effects;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $pump_side_effects_type;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $soft_method;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $tech;

    /**
     * @ORM\ManyToOne(targetEntity="TryPMA")
     * @ORM\JoinColumn(name="try_id", referencedColumnName="id", nullable=true)
     */
    private $tries;

    public function __construct() {
        $this->gyneco = json_encode(array());
        $this->risk = json_encode(array());
        $this->pump_side_effects_type = json_encode(array());
        $this->tech = json_encode(array());
        $this->stimulator = json_encode(array());
        $this->soft_method = json_encode(array());
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

    public function getPregnant() {
        return $this->pregnant;
    }

    public function setPregnant($bool) {
        $this->pregnant = $bool;
    }

    public function getDate() {
        return date('d-m-Y', $this->date);
    }

    public function setDate($date) {
        $date = strtotime($date);
        $this->date = $date;
    }

    public function getSince() {
        return $this->since;
    }

    public function setSince($since) {
        $this->since = $since;
    }

    public function getStimulationLength() {
        return $this->stimulation_length;
    }

    public function setStimulationLength($duree) {
        $this->stimulation_length = $duree;
    }

    public function getPathologyGyneco() {
        $gyneco = json_decode($this->gyneco, true);
        if (is_null($gyneco))
            return array();
        return $gyneco;
    }

    public function setPathologyGyneco($gyneco) {
        $gyneco = trim($gyneco);
        if (empty($gyneco)) return ;
        $this->gyneco = json_encode(explode(',', $gyneco));
    }

    public function getRiskFactor() {
        $risk = json_decode($this->risk, true);
        if (is_null($risk))
            return array();
        return $risk;
    }

    public function setRiskFactor($risk) {
        $risk = trim($risk);
        if (empty($risk)) return ;
        $this->risk = json_encode(explode(',', $risk));
    }

    public function getOvulationStimulator() {
        $stimulator = json_decode($this->stimulator, true);
        if (is_null($stimulator))
            return array();
        return $stimulator;
    }

    public function setOvulationStimulator($stimulator) {
        $stimulator = trim($stimulator);
        if (empty($stimulator)) return ;
        $this->stimulator = json_encode(explode(',', $stimulator));
    }
    public function getPump() {
        return $this->pump;
    }

    public function setPump($bool) {
        $this->pump = $bool;
    }

    public function getPumpSideEffects() {
        return $this->pump_side_effects;
    }

    public function setPumpSideEffects($bool) {
        $this->pump_side_effects = $bool;
    }

    public function getPumpSideEffect() {
        $pump_side_effects_type = json_decode($this->pump_side_effects_type, true);
        if (is_null($pump_side_effects_type))
            return array();
        return $pump_side_effects_type;
    }

    public function setPumpSideEffect($pump_side_effects_type) {
        $pump_side_effects_type = trim($pump_side_effects_type);
        if (empty($pump_side_effects_type)) return ;
        $this->pump_side_effects_type = json_encode(explode(',', $pump_side_effects_type));
    }

    public function getSoftMethod() {
        $soft_method = json_decode($this->soft_method, true);
        if (is_null($soft_method))
            return array();
        return $soft_method;
    }

    public function setSoftMethod($soft_method) {
        $soft_method = trim($soft_method);
        if (empty($soft_method)) return ;
        $this->soft_method = json_encode(explode(',', $soft_method));
    }

    public function getTechPMA() {
        $tech = json_decode($this->tech, true);
        if (is_null($tech))
            return array();
        return $tech;
    }

    public function setTechPMA($tech) {
        $tech = trim($tech);
        if (empty($tech)) return ;
        $this->tech = json_encode(explode(',', $tech));
    }

    public function addTech(TechPMA $tech) {
        if (!in_arra($tech, $this->tech))
            $this->tech[] = $tech;
    }

    public function resetTech() {
        $this->tech = json_encode(array());
    }

    public function getTech() {
        return $this->tech;
    }

    public function getTries() {
        return $this->tries;
    }

    public function setTries(TryPMA $try) {
        $this->tries = $try;
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

}