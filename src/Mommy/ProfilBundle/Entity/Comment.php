<?php

namespace Mommy\ProfilBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;
use Doctrine\ORM\Mapping as ORM;

use Mommy\SecurityBundle\Entity\User;

/**
 * Mommy\ProfilBundle\Entity\Comment
 *
 * @ORM\Table(name="mv_comment", indexes={@ORM\Index(name="search_idx", columns={"id", "aid", "mid", "date"})})
 * @ORM\Entity()
  */
class Comment
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="\Mommy\SecurityBundle\Entity\User", cascade={"remove"})
     * @ORM\JoinColumn(name="aid", referencedColumnName="id", onDelete="CASCADE")
     */
    private $author;

    /**
     * @ORM\ManyToOne(targetEntity="Message", cascade={"remove"})
     * @ORM\JoinColumn(name="mid", referencedColumnName="id", onDelete="CASCADE")
     */
    private $message;

    /**
     * @ORM\Column(type="integer")
     */
    private $date;

    /**
     * @ORM\Column(type="text")
     */
    private $content;

    public function getId() {
        return $this->id;
    }    

    public function getAuthor() {
        return $this->author;
    }

    public function setAuthor(User $user) {
        $this->author = $user;
    }

    public function getMessage() {
        return $this->message;
    }

    public function setMessage(Message $msg) {
        $this->message = $msg;
    }

    public function setDate() {
        $this->date = time();
    }

    public function getDate($as_int = false) {
        if ($as_int) return $this->date;
        setlocale(LC_TIME, 'fr_FR.utf8','fra'); 
        return strftime('%d %B', $this->date);
    }

    public function getContent() {
        return $this->content;
    }

    public function setContent($content) {
        $this->content = filter_var($content, FILTER_SANITIZE_STRING);
    }
}