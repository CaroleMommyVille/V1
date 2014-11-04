<?php

namespace Mommy\UIBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;
use Doctrine\ORM\Mapping as ORM;

use Mommy\SecurityBundle\Entity\User;
use Mommy\ProfilBundle\Entity\Pro;
use Mommy\ClubBundle\Entity\Club;

/**
 * Mommy\UIBundle\Entity\Gallery
 *
 * @ORM\Table(name="mv_gallery", indexes={@ORM\Index(name="search_idx", columns={"id", "gid", "uid", "pid", "cid"})}))
 * @ORM\Entity()
 */
class Gallery
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="GalleryType")
     * @ORM\JoinColumn(name="gid", referencedColumnName="id", nullable=true)
     */
    private $type=null;

    /**
     * @ORM\ManyToOne(targetEntity="\Mommy\SecurityBundle\Entity\User")
     * @ORM\JoinColumn(name="uid", referencedColumnName="id", nullable=true)
     */
    private $owner=null;

    /**
     * @ORM\ManyToOne(targetEntity="\Mommy\ProfilBundle\Entity\Pro")
     * @ORM\JoinColumn(name="pid", referencedColumnName="id", nullable=true)
     */
    private $page=null;

    /**
     * @ORM\ManyToOne(targetEntity="\Mommy\ClubBundle\Entity\Club")
     * @ORM\JoinColumn(name="cid", referencedColumnName="id", nullable=true)
     */
    private $club=null;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $name=null;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $cover=null;

    /**
     * @ORM\Column(type="integer")
     */
    private $published;

    /**
     * @ORM\Column(type="integer")
     */
    private $edited;

    /**
     * @ORM\Column(type="text")
     */
    private $description;

    public function getId() {
        return $this->id;
    }

    public function getOwner() {
    	return $this->owner;
    }

    public function setOwner(User $owner) {
		$this->owner = $owner;
    }

    public function getPage() {
        return $this->page;
    }

    public function setPage(Pro $page) {
        $this->page = $page;
    }

    public function getClub() {
        return $this->club;
    }

    public function setClub(Club $club) {
        $this->club = $club;
    }

    public function getName() {
    	return $this->name;
    }

    public function setName($name) {
    	$this->name = filter_var($name, FILTER_SANITIZE_STRING);
    }

    public function getPublished() {
        return $this->published;
    }

    public function setPublished() {
        $this->published = time();
    }

    public function getEdited() {
        return $this->edited;
    }

    public function setEdited() {
        $this->edited = time();
    }

    public function getType() {
        return $this->type;
    }

    public function setType(GalleryType $type) {
        $this->type = $type;
    }

    public function getCover() {
        return $this->cover;
    }

    public function setCover($cover) {
        $this->cover = filter_var($cover, FILTER_SANITIZE_URL);
    }
}