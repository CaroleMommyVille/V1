<?php

namespace Mommy\StatsBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;
use Doctrine\ORM\Mapping as ORM;

/**
 * Mommy\StatsBundle\Entity\ProStats
 *
 * @ORM\Table(name="mv_stats_pro", indexes={@ORM\Index(name="search_idx", columns={"id", "time"})})
 * @ORM\Entity()
 */
class ProStats
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
     * @ORM\Column(type="integer")
     */
    private $count;

    public function getTime() {
    	return $this->time;
    }

    public function setTime($time) {
        $this->time = $time;
    }

    public function getCount() {
        return $this->count;
    }

    public function setCount($count) {
        $this->count = $count;
    }

}