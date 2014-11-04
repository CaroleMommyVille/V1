<?php

namespace Mommy\PageBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;
use Doctrine\ORM\Mapping as ORM;

use Mommy\SecurityBundle\Entity\User;
use Mommy\ProfilBundle\Entity\Pro;

/**
 * Mommy\PageBundle\Entity\Message
 *
 * @ORM\Table(name="mv_pro_message", indexes={@ORM\Index(name="search_idx", columns={"id", "aid", "wid", "date"})})
 * @ORM\Entity()
  */
class Message
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="\Mommy\SecurityBundle\Entity\User")
     * @ORM\JoinColumn(name="aid", referencedColumnName="id")
     */
    private $author;

    /**
     * @ORM\ManyToOne(targetEntity="\Mommy\ProfilBundle\Entity\Pro")
     * @ORM\JoinColumn(name="wid", referencedColumnName="id")
     */
    private $wall;

    /**
     * @ORM\Column(type="integer")
     */
    private $date;

    /**
     * @ORM\Column(type="text")
     */
    private $content;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $photo;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $link;

    public function getId() {
        return $this->id;
    }    

    public function getAuthor() {
        return $this->author;
    }

    public function setAuthor(User $user) {
        $this->author = $user;
    }


    public function getWall() {
        return $this->wall;
    }

    public function setWall(Pro $pro) {
        $this->wall = $pro;
    }

    public function setDate() {
        $this->date = time();
    }

    public function getDate($as_int = false) {
        if ($as_int) return $this->date;
        setlocale(LC_TIME, 'fr_FR.utf8','fra'); 
        return strftime('%A %d %B %Y - %Hh%M', $this->date);
    }

    public function getContent() {
        return $this->content;
    }

    public function setContent($content) {
        $this->content = filter_var($content, FILTER_SANITIZE_STRING);
    }

    public function getPhoto() {
        return $this->photo;
    }

    public function setPhoto($url) {
        $this->photo = filter_var($url, FILTER_SANITIZE_URL);
    }

    public function getLink() {
        return $this->link;
    }

    public function setLink($link) {
        $this->link = $link;
    }
}