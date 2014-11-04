<?php

namespace Mommy\PageBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;
use Doctrine\ORM\Mapping as ORM;

use Mommy\SecurityBundle\Entity\User;

/**
 * Mommy\PageBundle\Entity\LikeMessage
 *
 * @ORM\Table(name="mv_pro_message_like", indexes={@ORM\Index(name="search_idx", columns={"id", "uid", "mid"})})
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
     * @ORM\ManyToOne(targetEntity="\Mommy\SecurityBundle\Entity\User")
     * @ORM\JoinColumn(name="uid", referencedColumnName="id")
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="Message")
     * @ORM\JoinColumn(name="mid", referencedColumnName="id")
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