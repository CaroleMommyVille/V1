<?php

namespace Mommy\ClubBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\EntityRepository;

use Mommy\SecurityBundle\Entity\User;
use Mommy\ProfilBundle\Entity\Address;

/**
 * Mommy\ClubBundle\Entity\Club
 *
 * @ORM\Table(name="mv_club", indexes={@ORM\Index(name="search_idx", columns={"id", "name"})})
 * @ORM\Entity()
 * @DoctrineAssert\UniqueEntity(fields={"name"}, message="name.already.exist" )
  */
class Club extends EntityRepository
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
    private $photo;

    /**
     * @ORM\Column(type="text")
     */
    private $desc_fr;

    /**
     * @ORM\Column(type="boolean")
     */
    private $enabled = false;

    /**
     * @ORM\ManyToOne(targetEntity="\Mommy\ProfilBundle\Entity\Address")
     * @ORM\JoinColumn(name="aid", referencedColumnName="id", nullable=true)
     */
    private $address = null;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $keywords = null;

    /**
     * @ORM\ManyToOne(targetEntity="\Mommy\SecurityBundle\Entity\User")
     * @ORM\JoinColumn(name="uid", referencedColumnName="id", nullable=true)
     */
    private $founder = null;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $admins = null;

    public function __construct() {
        $this->admins = json_encode(array());
    }

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

    public function getPhoto() {
        return $this->photo;
    }

    public function setPhoto($url) {
        $this->photo = filter_var($url, FILTER_SANITIZE_URL);
    }

    public function getDescFR() {
    	return html_entity_decode($this->desc_fr);
    }

    public function setDescFR($desc) {
    	if (!is_string($desc)) $this->desc_fr = '';
		$this->desc_fr = $desc;
    }

    public function getAddress() {
        return $this->address;
    }

    public function setAddress(Address $address) {
        $this->address = $address;
    }

    public function getFounder() {
        return $this->founder;
    }

    public function setFounder(User $user) {
        $this->founder = $user;
    }

    public function getAdmins() {
        return json_decode($this->admins, true);
    }

    public function setAdmins($uids) {
        $uids = trim($uids);
        if (empty($uids)) return ;
        $this->admins = json_encode(explode(',', $uids));
    }

    public function getEnabled() {
        return $this->enabled;
    }

    public function setEnabled($bool = false) {
        $this->enabled = ($bool === true);
    }

    public function getKeys() {
        return $this->keywords;
    }

    public function setKeys($keywords) {
        $this->keywords = filter_var($keywords, FILTER_SANITIZE_STRING);
        $this->keywords = str_replace(',', ' ', $this->keywords);
    }

    public function search($term) {
        return $this->createQueryBuilder('l')
            ->select('*')
            ->where("lower(keys) like '%:term%' OR lower(desc_fr) like '%:term%' OR lower(name) like '%:term%'")
            ->setParameter('term', strtolower($term))
            ->getQuery()
            ->getResult();
    }
}