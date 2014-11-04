<?php

namespace Mommy\ProfilBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;
use Doctrine\ORM\Mapping as ORM;

use Mommy\SecurityBundle\Entity\User;
use Mommy\MapBundle\Entity\Station;

/**
 * Mommy\ProfilBundle\Entity\Woman
 *
 * @ORM\Table(name="mv_woman", indexes={@ORM\Index(name="search_idx", columns={"id", "uid"})})
 * @ORM\Entity()
 */
class Woman
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="\Mommy\SecurityBundle\Entity\User", cascade={"remove"})
     * @ORM\JoinColumn(name="uid", referencedColumnName="id", onDelete="CASCADE")
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="Marriage")
     * @ORM\JoinColumn(name="marriage_id", referencedColumnName="id", nullable=true)
     */
    private $marriage;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $since;

    /**
     * @ORM\ManyToOne(targetEntity="Address")
     * @ORM\JoinColumn(name="aid", referencedColumnName="id", nullable=true)
     */
    private $address;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $stations;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $jobstations;

    /**
     * @ORM\ManyToOne(targetEntity="Address")
     * @ORM\JoinColumn(name="jobaddress", referencedColumnName="id", nullable = true)
     */
    private $jobaddress;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $jobtitle;

    /**
     * @ORM\ManyToOne(targetEntity="NoWork")
     * @ORM\JoinColumn(name="nowork", referencedColumnName="id", nullable = true)
     */
    private $nowork;

    /**
     * @ORM\ManyToOne(targetEntity="Diploma")
     * @ORM\JoinColumn(name="diploma", referencedColumnName="id", nullable = true)
     */
    private $diploma;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $languages;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $spheres;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $style;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $sports;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $activities;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $holidays;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $tvshows;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $movies;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $musiques;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $vip;

    public function __construct() {
        $this->stations = json_encode(array());
        $this->jobstations = json_encode(array());
        $this->languages = json_encode(array());
        $this->spheres = json_encode(array());
        $this->activities = json_encode(array());
        $this->holidays = json_encode(array());
        $this->style = json_encode(array());
        $this->sports = json_encode(array());
        $this->tvshows = json_encode(array());
        $this->movies = json_encode(array());
        $this->musiques = json_encode(array());
        $this->vip = json_encode(array());
    }

    public function getId() {
        return $this->id;
    }    

    public function getUser() {
        return $this->user;
    }

    public function setUser(User $user) {
        $this->user = $user;
    }

    public function getMarriage() {
        return $this->marriage;
    }

    public function setMarriage(Marriage $marriage) {
        $this->marriage = $marriage;
    }

    public function getSince() {
        return date('m-Y', $this->since);
    }

    public function setSince($since) {
        $since = '01-'.$since;
        $since = strtotime($since);
        if ($since > time()) return false;
        $this->since = $since;
    }

    public function getStations() {
        $stations = json_decode($this->stations, true);
        if (is_null($stations))
            return array();
        return $stations;
    }

    public function setStations($stations) {
        $stations = trim($stations);
        if (empty($stations)) return ;
        $this->stations = json_encode(explode(',', $stations));
    }

    public function getAddress() {
        return $this->address;
    }

    public function setAddress(Address $address) {
        $this->address = $address;
    }

    public function getJobAddress() {
        return $this->jobaddress;
    }

    public function setJobAddress(Address $address) {
        $this->jobaddress = $address;
    }

    public function getJobStations() {
        $stations = json_decode($this->jobstations, true);
        if (is_null($stations))
            return array();
        return $stations;
    }

    public function setJobStations($stations) {
        $stations = trim($stations);
        if (empty($stations)) return ;
        $this->jobstations = json_encode(explode(',', $stations));
    }

    public function getJobTitle() {
        return $this->jobtitle;
    }

    public function setJobTitle($title) {
        $this->jobtitle = (string) $title;
    }

    public function getNoWork() {
        return $this->nowork;
    }

    public function setNoWork(NoWork $nowork) {
        $this->nowork = $nowork;
    }

    public function getLanguages() {
        $languages = json_decode($this->languages, true);
        if (is_null($languages))
            return array();
        return $languages;
    }

    public function setLanguages($languages) {
        $languages = trim($languages);
        if (empty($languages)) return ;
        $languages = array_slice(explode(',', $languages), 2);
        $this->languages = json_encode($languages);
    }

    public function getSpheres() {
        $spheres = json_decode($this->spheres, true);
        if (is_null($spheres))
            return array();
        return $spheres;
    }

    public function setSpheres($spheres) {
        $spheres = trim($spheres);
        if (empty($spheres)) return ;
        $spheres =  array_slice(explode(',', $spheres), sizeof(json_decode($this->languages, true)));
        $this->spheres = json_encode($spheres);
    }

    public function getStyle() {
        $style = json_decode($this->style, true);
        if (is_null($style))
            return array();
        return $style;
    }

    public function setStyle($style) {
        $style = trim($style);
        if (empty($style)) return ;
        $this->style = json_encode(explode(',', $style));
    }

    public function getSports() {
        $sports = json_decode($this->sports, true);
        if (is_null($sports))
            return array();
        return $sports;
    }

    public function setSports($sports) {
        $sports = trim($sports);
        if (empty($sports)) return ;
        $this->sports = json_encode(explode(',', $sports));
    }

    public function getActivities() {
        $activities = json_decode($this->activities, true);
        if (is_null($activities))
            return array();
        return $activities;
    }

    public function setActivities($activities) {
        $activities = trim($activities);
        if (empty($activities)) return ;
        $this->activities = json_encode(explode(',', $activities));
    }

    public function getHolidays() {
        $holidays = json_decode($this->holidays, true);
        if (is_null($holidays))
            return array();
        return $holidays;
    }

    public function setHolidays($holidays) {
        $holidays = trim($holidays);
        if (empty($holidays)) return ;
        $this->holidays = json_encode(explode(',', $holidays));
    }

    public function getTVShows() {
        $tvshows = json_decode($this->tvshows, true);
        if (is_null($tvshows))
            return array();
        return $tvshows;
    }

    public function setTVShows($tvshows) {
        $tvshows = trim($tvshows);
        if (empty($tvshows)) return ;
        $this->tvshows = json_encode(explode(',', $tvshows));
    }

    public function getMovies() {
        $movies = json_decode($this->movies, true);
        if (is_null($movies))
            return array();
        return $movies;
    }

    public function setMovies($movies) {
        $movies = trim($movies);
        if (empty($movies)) return ;
        $this->movies = json_encode(explode(',', $movies));
    }

    public function getMusics() {
        $musics = json_decode($this->musiques, true);
        if (is_null($musics))
            return array();
        return $musics;
    }

    public function setMusics($musics) {
        $musics = trim($musics);
        if (empty($musics)) return ;
        $this->musiques = json_encode(explode(',', $musics));
    }

    public function getVIP() {
        $vip = json_decode($this->vip, true);
        if (is_null($vip))
            return array();
        return $vip;
    }

    public function setVIP($vip) {
        $vip = trim($vip);
        if (empty($vip)) return ;
        $this->vip = json_encode(explode(',', $vip));
    }

    public function getDiploma() {
        return $this->diploma;
    }

    public function setDiploma(Diploma $diploma) {
        $this->diploma = $diploma;
    }
}