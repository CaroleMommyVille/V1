<?php

namespace Mommy\UIBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;
use Doctrine\ORM\Mapping as ORM;

/**
 * Mommy\UIBundle\Entity\Statistics
 *
 * @ORM\Table(name="mv_statistics", indexes={@ORM\Index(name="search_idx", columns={"id", "time", "uri"})})
 * @ORM\Entity()
 */
class Statistics
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=10)
     */
    private $time;

    /**
     * @ORM\Column(type="string")
     */
    private $uri;

    /**
     * @ORM\Column(type="integer")
     */
    private $count;

    /**
     * @ORM\Column(type="string", length=4)
     */
    private $method;     

    public function getTime() {
    	return $this->time;
    }

    public function setTime($time) {
        $this->time = $time;
    }

    public function getURI() {
        return $this->uri;
    }

    public function setURI($uri) {
    	$this->uri = $uri;
    }

    public function getCount() {
        return $this->count;
    }

    public function setCount($count) {
        $this->count = $count;
    }

    public function getCall() {
        return $this->method;
    }

    public function setCall($request) {
        $this->method = $request;
    }

}