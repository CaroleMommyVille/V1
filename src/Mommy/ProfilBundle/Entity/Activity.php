<?php

namespace Mommy\ProfilBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;
use Doctrine\ORM\Mapping as ORM;

/**
 * Mommy\ProfilBundle\Entity\Activity
 *
 * @ORM\Table(name="mv_activity", indexes={@ORM\Index(name="search_idx", columns={"id", "name"})})
 * @ORM\Entity()
 * @DoctrineAssert\UniqueEntity(fields={"name"}, message="name.already.exist" )
 */
class Activity
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
     * @ORM\Column(type="string")
     */
    private $identical;

    /**
     * @ORM\Column(type="string")
     */
    private $desc_fr;

    public function getId() {
        return $this->id;
    }    

    public function getName() {
        return $this->name;
    }

    public function setName($name) {
        if (!is_string($name)) return false;
        $this->name = $name;
    }

    public function getIdentical() {
        return $this->identical;
    }

    public function setIdentical($identical) {
        if (!is_string($identical)) return false;
        $this->identical = $identical;
    }

    public function getDescFR() {
    	return html_entity_decode($this->desc_fr);
    }

    public function setDescFR($desc) {
    	if (!is_string($desc)) $this->desc_fr = '';
		$this->desc_fr = $desc;
    }
}