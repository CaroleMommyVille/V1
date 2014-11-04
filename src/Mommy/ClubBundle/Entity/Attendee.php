<?php

namespace Mommy\ClubBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\EntityRepository;

use Mommy\SecurityBundle\Entity\User;

/**
 * Mommy\ClubBundle\Entity\Member
 *
 * @ORM\Table(name="mv_club_event_attendee", indexes={@ORM\Index(name="search_idx", columns={"id", "uid", "eid"})})
 * @ORM\Entity()
 */
class Attendee extends EntityRepository
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="\Mommy\SecurityBundle\Entity\User")
     * @ORM\JoinColumn(name="uid", referencedColumnName="id")
     */
    private $member;

    /**
     * @ORM\ManyToOne(targetEntity="Event")
     * @ORM\JoinColumn(name="eid", referencedColumnName="id")
     */
    private $event;

    public function getId() {
        return $this->id;
    }    

    public function getMember() {
        return $this->member;
    }

    public function setMember(User $user) {
        $this->member = $user;
    }

    public function getEvent() {
        return $this->event;
    }

    public function setEvent(Event $event) {
        $this->event = $event;
    }
}