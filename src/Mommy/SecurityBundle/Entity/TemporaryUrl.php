<?php

namespace Mommy\SecurityBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;
use Doctrine\ORM\Mapping as ORM;

use Mommy\SecurityBundle\Entity\User;

/**
 * Mommy\SecurityBundle\Entity\TemporaryUrl
 *
 * @ORM\Table(name="mv_tempurl")
 * @ORM\Entity()
 * @DoctrineAssert\UniqueEntity(fields={"url"}, message="url.already.exist" )
 */
class TemporaryUrl
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=254, unique=true)
     */
    private $url;

    /**
     * @ORM\Column(type="integer")
     */
    private $expires;

    /**
     * @ORM\Column(type="integer")
     */
    private $user;

    /**
     * @inheritDoc
     */
    public function getId() {
        return $this->id;
    }    

    public function getUrl() {
        return $this->url;
    }

    /**
     * @inheritDoc
     */
    public function setUrl($url) {
        $this->url = filter_var($url, FILTER_SANITIZE_URL);
    }

    /**
     * @inheritDoc
     */
    public function getExpires() {
        return $this->expires;
    }

    /**
     * @inheritDoc
     */
    public function setExpires($expires) {
        if (is_int($expires))
            $this->expires = trim($expires);
        else
            $this->expires = '0';
    }

    /**
     * @inheritDoc
     */
    public function getUser() {
        return $this->user;
    }

    /**
     * @inheritDoc
     */
    public function setUser($user) {
        $this->user = $user;
    }
}