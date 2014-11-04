<?php

namespace Mommy\ProfilBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;
use Doctrine\ORM\Mapping as ORM;

/**
 * Mommy\ProfilBundle\Entity\Ad
 *
 * @ORM\Table(name="mv_ads", indexes={@ORM\Index(name="search_idx", columns={"owner", "name"})}))
 * @ORM\Entity()
 */
class Ad
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $owner;

    /**
     * @ORM\Column(type="string")
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=10)
     */
    private $published;

    /**
     * @ORM\Column(type="string", length=10)
     */
    private $expire;

    /**
     * @ORM\Column(type="text")
     */
    private $code;

    /**
     * @ORM\Column(type="boolean")
     */
    private $active;

    /**
     * @ORM\Column(type="string")
     */
    private $url;

    /**
     * @ORM\Column(type="string")
     */
    private $picture;

    public function getOwner() {
        return $this->owner;
    }

    public function setOwner($owner) {
        if (!is_integer($owner)) return;
        $user = $this->getDoctrine()->getRepository('User')->find($owner);
        if ($user->getId())
            $this->owner = $owner;
    }

    public function getName() {
        return $this->name;
    }

    public function setName($name) {
        if (!is_string($name)) return;
        $name = trim($name);
        $name = ucfirst($name);
        $this->name = $name;
    }

    public function getPublished() {
        return $this->published;
    }

    public function setPublished($published) {
        if (is_null($published)) $published = date('YmdHis');
        if (!is_string($published)) return;
        $this->published = $published;
    }

    public function getExpire() {
        return $this->expire;
    }

    public function setExpire($expire) {
        if (is_null($expire)) $expire = date('YmdHis', time() + 365*24*3600); // 1 year max
        if (!is_string($expire)) return;
        $this->expire = $expire;
    }

    public function getCode() {
        return $this->code;
    }

    public function setCode($code) {
        if (!is_string($code)) return;
        $this->code = $code;
    }

    public function getActive() {
        return $this->active;
    }

    public function setActive($active) {
        if (!is_bool($active)) return;
        $this->active = $active;
    }

    public function getUrl() {
        return $this->url;
    }

    public function setUrl($url) {
        if (!is_string($url)) return;
        if (@get_headers($url) === false) return;
        $this->url = $url;
    }

    public function getPicture() {
        return $this->picture;
    }

    public function setPicture($picture) {
        if (!is_string($picture)) return;
        if (@get_headers($picture) === false) return;
        $this->picture = $picture;
    }
}