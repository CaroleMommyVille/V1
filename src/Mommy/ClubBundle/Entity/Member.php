<?php

namespace Mommy\ClubBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;
use Doctrine\ORM\Mapping as ORM;

use Mommy\SecurityBundle\Entity\User;
use Mommy\ClubBundle\Entity\Club;

/**
 * Mommy\ClubBundle\Entity\Member
 *
 * @ORM\Table(name="mv_club_member", indexes={@ORM\Index(name="search_idx", columns={"id", "uid", "cid"})})
 * @ORM\Entity()
 */
class Member
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
    private $member;

    /**
     * @ORM\ManyToOne(targetEntity="\Mommy\ClubBundle\Entity\Club")
     * @ORM\JoinColumn(name="cid", referencedColumnName="id")
     */
    private $club;

    public function getId() {
        return $this->id;
    }    

    public function getMember() {
        return $this->member;
    }

    public function setMember(User $user) {
        $this->member = $user;
    }

    public function getClub() {
        return $this->club;
    }

    public function setClub(Club $club) {
        $this->club = $club;
    }
}