<?php

namespace Mommy\ProfilBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;
use Doctrine\ORM\Mapping as ORM;

/**
 * Mommy\ProfilBundle\Entity\Maternity
 *
 * @ORM\Table(name="mv_maternity", indexes={@ORM\Index(name="search_idx", columns={"id", "name"})})
 * @ORM\Entity()
 * @DoctrineAssert\UniqueEntity(fields={"name"}, message="name.already.exist" )
 */
class Maternity
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", unique=true)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=32)
     */
    private $type;

    /**
     * @ORM\Column(type="integer")
     */
    private $level;

    /**
     * @ORM\ManyToOne(targetEntity="Address",cascade={"merge"})
     * @ORM\JoinColumn(name="aid", referencedColumnName="id")
     */
    private $address;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $url =null;

    /**
     * @ORM\Column(type="string")
     */
    private $phone;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $cesarienne = null;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $peridurale = null;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $duree = null;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $lit = null;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $accouhement = null;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $chambre = null;

    public function getId() {
        return $this->id;
    }    

    public function getName() {
    	return $this->name;
    }

    public function setName($name) {
		$this->name = $name;
    }

    public function getAddress() {
    	return $this->address;
    }

    public function setAddress(Address $address) {
		$this->address = $address;
    }

    public function getType() {
        return $this->type;
    }

    public function setType($type) {
        $this->type = $type;
    }

    public function getURL() {
        return $this->url;
    }

    public function setURL($url) {
        $this->url = $url;
    }

    public function getLevel() {
        return $this->level;
    }

    public function setLevel($level) {
        $this->level = $level;
    }

    public function getPhone() {
        return $this->phone;
    }

    public function setPhone($phone) {
        $this->phone = $phone;
    }

    public function getCesarienne() {
        return $this->cesarienne;
    }

    public function setCesarienne($cesarienne) {
        $this->cesarienne = $cesarienne;
    }

    public function getPeridurale() {
        return $this->peridurale;
    }

    public function setPeridurale($peridurale) {
        $this->peridurale = $peridurale;
    }

    public function getDuree() {
        return $this->duree;
    }

    public function setDuree($duree) {
        $this->duree = $duree;
    }

    public function getLit() {
        return $this->lit;
    }

    public function setLit($lit) {
        $this->lit = $lit;
    }

    public function getAccouchement() {
        return $this->accouhement;
    }

    public function setAccouchement($accouhement) {
        $this->accouhement = $accouhement;
    }

    public function getChambre() {
        return $this->chambre;
    }

    public function setChambre($chambre) {
        $this->chambre = $chambre;
    }
}