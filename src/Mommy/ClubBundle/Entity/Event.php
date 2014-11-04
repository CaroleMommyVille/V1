<?php

namespace Mommy\ClubBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;
use Doctrine\ORM\Mapping as ORM;

use Mommy\SecurityBundle\Entity\User;

/**
 * Mommy\ClubBundle\Entity\Event
 *
 * @ORM\Table(name="mv_club_event", indexes={@ORM\Index(name="search_idx", columns={"id", "aid", "cid", "date"})})
 * @ORM\Entity()
  */
class Event
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Club")
     * @ORM\JoinColumn(name="cid", referencedColumnName="id")
     */
    private $club;

    /**
     * @ORM\Column(type="integer")
     */
    private $date;

    /**
     * @ORM\Column(type="string")
     */
    private $name;

    /**
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @ORM\ManyToOne(targetEntity="\Mommy\ProfilBundle\Entity\Address")
     * @ORM\JoinColumn(name="aid", referencedColumnName="id", nullable=true)
     */
    private $address = null;

    public function getId() {
        return $this->id;
    }    

    public function getClub() {
        return $this->club;
    }

    public function setClub(Club $club) {
        $this->club = $club;
    }

    public function setDate() {
        $this->date = strtotime($date);
    }

    public function getName() {
        return $this->name;
    }

    public function setName($name) {
        $this->name = filter_var($name, FILTER_SANITIZE_STRING);
    }

    public function getDate($as_int = false) {
        if ($as_int) return $this->date;
        setlocale(LC_TIME, 'fr_FR.utf8','fra'); 
        return strftime('%A %d %B %Y - %Hh%M', $this->date);
    }

    public function getDescription() {
        return $this->description;
    }

    public function setDescription($description) {
        $this->description = filter_var($description, FILTER_SANITIZE_STRING);
    }
}