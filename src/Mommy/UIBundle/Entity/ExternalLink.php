<?php

namespace Mommy\UIBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Doctrine\ORM\Mapping as ORM;

use Mommy\UIBundle\Controller\DefaultController as MommyUIBundle;

/**
 * Mommy\UIBundle\Entity\ExternalLink
 *
 * @ORM\Table(name="mv_elink", indexes={@ORM\Index(name="search_idx", columns={"id", "url"})})
 * @ORM\Entity()
 */
class ExternalLink
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $published;

    /**
     * @ORM\Column(type="string")
     */
    private $url;

    /**
     * @ORM\Column(type="string")
     */
    private $title;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $video;

    /**
     * @ORM\Column(type="text")
     */
    private $text;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $image;

    public function getId() {
        return $this->id;
    }

    public function getPublished() {
    	return $this->published;
    }

    public function setPublished() {
        $this->published = time();
    }

    public function getURL() {
        return $this->url;
    }

    public function setURL($url) {
    	$this->url = filter_var($url, FILTER_SANITIZE_URL);
    }

    public function getVideo() {
        return $this->video;
    }

    public function setVideo($video) {
        $this->video = filter_var($video, FILTER_SANITIZE_URL);
    }

    public function getText() {
        return $this->text;
    }

    public function setText($text) {
        $text = filter_var($text, FILTER_SANITIZE_STRING);
        $this->text = strip_tags($text);
    }

    public function getImage() {
        return $this->image;
    }

    public function setImage($image) {
        $src = filter_var($image, FILTER_SANITIZE_URL);
        $thumb = new \Imagick();
        $thumb->readImage($src);
        $thumb->scaleImage(240,135,true);
        $type = 'image/'.strtolower($thumb->getImageFormat());
        if (!in_array($type, MommyUIBundle::$allowedMimeTypes))
            return false;

        $this->image = 'data:'.$type.';base64,'.base64_encode($thumb);
    }


    public function getTitle() {
        return $this->title;
    }

    public function setTitle($title) {
        $title = filter_var($title, FILTER_SANITIZE_STRING);
        $this->title = strip_tags($title);
    }
}