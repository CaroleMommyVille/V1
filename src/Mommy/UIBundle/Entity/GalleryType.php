<?php

namespace Mommy\UIBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;
use Doctrine\ORM\Mapping as ORM;

/**
 * Mommy\UIBundle\Entity\GalleryType
 *
 * @ORM\Table(name="mv_gallery_type", indexes={@ORM\Index(name="search_idx", columns={"id", "name"})}))
 * @ORM\Entity()
 */
class GalleryType
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     */
    private $name;

    /**
     * @ORM\Column(type="text")
     */
    private $desc_fr;

    /**
     * @ORM\Column(type="string")
     */
    private $cover;

    public function getId() {
        return $this->id;
    }

    public function getName() {
    	return $this->name;
    }

    public function setName($name) {
    	$this->name = filter_var($name, FILTER_SANITIZE_STRING);
    }

    public function getDescFR() {
        return $this->desc_fr;
    }

    public function setDescFR($desc) {
        $this->desc_fr = filter_var($desc, FILTER_SANITIZE_STRING);
    }

    public function getCover() {
        return $this->cover;
    }

    public function setCover($cover) {
        $this->cover = filter_var($cover, FILTER_SANITIZE_URL);
    }
}