<?php

namespace Mommy\ProfilBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;
use Doctrine\ORM\Mapping as ORM;

use Mommy\SecurityBundle\Entity\User;

/**
 * Mommy\ProfilBundle\Entity\LikeMessage
 *
 * @ORM\Table(name="mv_message_like", indexes={@ORM\Index(name="search_idx", columns={"id", "uid", "mid"})})
 * @ORM\Entity()
 */
class LikeMessage
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
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="Message", cascade={"remove"})
     * @ORM\JoinColumn(name="mid", referencedColumnName="id", onDelete="CASCADE")
     */
    private $message;

    public function getId() {
        return $this->id;
    }    

    public function getUser() {
        return $this->user;
    }

    public function setUser(User $user) {
        $this->user = $user;
    }

    public function getMessage() {
        return $this->message;
    }

    public function setMessage(Message $message) {
        $this->message = $message;
    }

}