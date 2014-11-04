<?php

namespace Mommy\ProfilBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;
use Doctrine\ORM\Mapping as ORM;

/**
 * Mommy\ProfilBundle\Entity\PathologyBaby
 *
 * @ORM\Table(name="mv_pathology_baby", indexes={@ORM\Index(name="search_idx", columns={"id", "name"})})
 * @ORM\Entity()
 * @DoctrineAssert\UniqueEntity(fields={"name"}, message="name.already.exist" )
 */
class PathologyBaby
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
    private $desc_fr;

    /**
     * @ORM\Column(type="string")
     */
    private $has_detail;

    /**
     * @ORM\Column(type="boolean")
     */
    private $enabled;

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

    public function getDescFR() {
    	return html_entity_decode($this->desc_fr);
    }

    public function setDescFR($desc) {
		$this->desc_fr = $desc;
    }

    public function getHasDetail() {
        return $this->has_detail;
    }

    public function setHasDetail($detail) {
        $this->has_detail = $detail;
    }

    public function getEnabled() {
        return $this->enabled;
    }

    public function setEnabled($bool) {
        $this->enabled = ($bool == true);
    }
}