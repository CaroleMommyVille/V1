<?php

namespace Mommy\BlogBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;
use Doctrine\ORM\Mapping as ORM;

use Mommy\SecurityBundle\Entity\User;

/**
 * Mommy\BlogBundle\Entity\Article
 *
 * @ORM\Table(name="mv_article", indexes={@ORM\Index(name="search_idx", columns={"id", "title"})}))
 * @ORM\Entity()
* @DoctrineAssert\UniqueEntity(fields={"title"}, message="name.already.exist" )
  */
class Article
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
    private $title;

    /**
     * @ORM\Column(type="string")
     */
    private $photo;

    /**
     * @ORM\Column(type="string")
     */
    private $referer;

    /**
     * @ORM\Column(type="text")
     */
    private $content;

    /**
     * @ORM\Column(type="text")
     */
    private $keywords;

    /**
     * @ORM\Column(type="boolean")
     */
    private $published = false;

    /**
     * @ORM\ManyToOne(targetEntity="\Mommy\SecurityBundle\Entity\User")
     * @ORM\JoinColumn(name="uid", referencedColumnName="id")
     */
    private $author;

    /**
     * @ORM\Column(type="integer")
     */
    private $date_published;

    /**
     * @ORM\Column(type="integer")
     */
    private $date_edited;
    
    public function getId() {
        return $this->id;
    }    

    public function getTitle() {
        return $this->title;
    }

    public function setTitle($title) {
        $this->title = filter_var($title, FILTER_SANITIZE_STRING);
    }

    public function getPhoto() {
        return $this->photo;
    }

    public function setPhoto($url) {
        $this->photo = filter_var($url, FILTER_SANITIZE_URL);
    }

    public function getReferer() {
        return $this->referer;
    }

    public function setReferer($url) {
        $this->referer = filter_var($url, FILTER_SANITIZE_URL);
    }

    public function getContent() {
    	return html_entity_decode($this->desc_fr);
    }

    public function setContent($content) {
		$this->content = $content;
    }

    public function getAuthor() {
        return $this->author;
    }

    public function setAuthor(User $user) {
        $this->author = $user;
    }

    public function getPublished() {
        return $this->published;
    }

    public function setPublished($bool = false) {
        $this->published = ($bool === true);
    }

    public function getKeys() {
        return $this->keywords;
    }

    public function setKeys($keywords) {
        $this->keywords = filter_var($keywords, FILTER_SANITIZE_STRING);
    }

    public function getDatePublished() {
        return date('d-m-Y', $this->date_published);
    }

    public function setDatePublished($date = 'now') {
        if ($date == 'now') $this->date_published = time();
        else {
            $date = strtotime($date);
            $this->date_published = $date;
        }
    }

    public function getDateEdited() {
        return date('d-m-Y', $this->date_edited);
    }

    public function setDateEdited($date = 'now') {
        if ($date == 'now') $this->date_edited = time();
        else {
            $date = strtotime($date);
            $this->date_edited = $date;
        }
    }

}