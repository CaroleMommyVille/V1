<?php

namespace Mommy\ProfilBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;
use Doctrine\ORM\Mapping as ORM;

use Mommy\UIBundle\Controller\DefaultController as MommyUIBundle;

/**
 * Mommy\ProfilBundle\Entity\VIP
 *
 * @ORM\Table(name="mv_vip", indexes={@ORM\Index(name="search_idx", columns={"id", "name"})})
 * @ORM\Entity()
 * @DoctrineAssert\UniqueEntity(fields={"name"}, message="name.already.exist" )
 */
class VIP
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

    public function getId() {
        return $this->id;
    }    

    static public function formatName($name) {
        $name = trim($name);
        $name = strtolower($name);
        $name = preg_replace('/(\s*)/', ' ', $name);
        $name = MommyUIBundle::stripAccents($name);
        $name = sha1($name);
        return $name;
    }

    public function getName() {
    	return $this->name;
    }

    public function setName($name) {
		$this->name = $this->formatName($name);
    }

    public function getDescFR() {
    	return html_entity_decode($this->desc_fr);
    }

    public function setDescFR($desc) {
    	if (!is_string($desc)) $this->desc_fr = '';
		$this->desc_fr = $desc;
    }
}