<?php

namespace Mommy\ProfilBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;
use Doctrine\ORM\Mapping as ORM;

/**
 * Mommy\ProfilBundle\Entity\Picture
 *
 * @ORM\Table(name="mv_picture", indexes={@ORM\Index(name="search_idx", columns={"owner", "name"})}))
 * @ORM\Entity()
 */
class Picture
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
    private $url;

    /**
     * @ORM\Column(type="string")
     */
    private $name;

    public function getOwner() {
    	return $this->owner;
    }

    public function setOwner($owner) {
    	if (!is_integer($owner)) return;
		$user = $this->getDoctrine()->getRepository('User')->find($owner);
		if ($user->getId())
			$this->owner = $owner;
    }

    public function getUrl() {
    	return $this->url;
    }

    public function setUrl($url) {
    	if (!is_string($url)) return;
    	if (@get_headers($url) === false) return;
    	$this->url = $url;
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
}