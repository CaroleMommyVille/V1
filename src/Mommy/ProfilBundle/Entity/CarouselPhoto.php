<?php

namespace Mommy\ProfilBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;
use Doctrine\ORM\Mapping as ORM;

/**
 * Mommy\ProfilBundle\Entity\CarouselPhoto
 *
 * @ORM\Table(name="mv_carousel_photo", indexes={@ORM\Index(name="search_idx", columns={"id", "cid", "url"})})
 * @ORM\Entity()
  */
class CarouselPhoto
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Carousel", cascade={"remove"})
     * @ORM\JoinColumn(name="cid", referencedColumnName="id", onDelete="CASCADE")
     */
    private $carousel;

    /**
     * @ORM\Column(type="string")
     */
    private $url;

    public function getId() {
        return $this->id;
    }    

    public function getCarousel() {
        return $this->carousel;
    }

    public function setCarousel(Carousel $carousel) {
        $this->carousel = $carousel;
    }

    public function getUrl() {
        return $this->url;
    }

    public function setUrl($url) {
        $this->url = filter_var($url, FILTER_SANITIZE_URL);
    }

}