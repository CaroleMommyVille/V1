<?php

namespace Mommy\ProfilBundle\Entity;

use Symfony\Component\Profil\Core\Pro\ProInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;

use Mommy\MapBundle\Entity\Station;
use Mommy\SecurityBundle\Entity\User;

/**
 * Mommy\ProfilBundle\Entity\Pro
 *
 * @ORM\Table(name="mv_pro", indexes={@ORM\Index(name="search_idx", columns={"id", "uid", "name", "jaid"})})
 * @ORM\Entity()
 */
class Pro 
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="\Mommy\SecurityBundle\Entity\User", cascade={"remove"})
     * @ORM\JoinColumn(name="uid", referencedColumnName="id", nullable=true, onDelete="CASCADE")
     */
    private $user = null;

    /**
     * @ORM\Column(type="string")
     */
    private $name;

    /**
     * @ORM\Column(type="string")
     */
    private $stations;

    /**
     * @ORM\Column(type="string")
     */
    private $phone;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $opening;

    /**
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @ORM\ManyToOne(targetEntity="Address",cascade={"merge"})
     * @ORM\JoinColumn(name="aid", referencedColumnName="id")
     */
    private $address;

    /**
     * @ORM\ManyToOne(targetEntity="JobActivities")
     * @ORM\JoinColumn(name="jaid", referencedColumnName="id")
     */
    private $activity;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $photo = null;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $url = null;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $time_as_text = null;

    public function _construct() {
        $this->stations = json_encode(array());
        $this->opening = json_encode(array(
            'mon' => array(
                'am' => array(
                    'start' => '',
                    'end' => '',
                    ),
                'pm' => array(
                    'start' => '',
                    'end' => '',
                    ),
            ),
            'tue' => array(
                'am' => array(
                    'start' => '',
                    'end' => '',
                    ),
                'pm' => array(
                    'start' => '',
                    'end' => '',
                    ),
            ),
            'wed' => array(
                'am' => array(
                    'start' => '',
                    'end' => '',
                    ),
                'pm' => array(
                    'start' => '',
                    'end' => '',
                    ),
            ),
            'thu' => array(
                'am' => array(
                    'start' => '',
                    'end' => '',
                    ),
                'pm' => array(
                    'start' => '',
                    'end' => '',
                    ),
            ),
            'fri' => array(
                'am' => array(
                    'start' => '',
                    'end' => '',
                    ),
                'pm' => array(
                    'start' => '',
                    'end' => '',
                    ),
            ),
            'sat' => array(
                'am' => array(
                    'start' => '',
                    'end' => '',
                    ),
                'pm' => array(
                    'start' => '',
                    'end' => '',
                    ),
            ),
            'sun' => array(
                'am' => array(
                    'start' => '',
                    'end' => '',
                    ),
                'pm' => array(
                    'start' => '',
                    'end' => '',
                    ),
            ),
        ));
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

    public function getName() {
        return $this->name;
    }

    public function setName($name) {
        $this->name = (string) $name;
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

    public function getMondayMorningStart() {
        $opening = json_decode($this->opening, true);
        return (isset($opening['mon']['am']['start']) ? $opening['mon']['am']['start'] : '');
    }

    public function setMondayMorningStart($time) {
        $opening = json_decode($this->opening, true);
        $opening['mon']['am']['start'] = $time;
        $this->opening = json_encode($opening);
    }

    public function getMondayMorningEnd() {
        $opening = json_decode($this->opening, true);
        return (isset($opening['mon']['am']['end']) ? $opening['mon']['am']['end'] : '');
    }

    public function setMondayMorningEnd($time) {
        $opening = json_decode($this->opening, true);
        $opening['mon']['am']['end'] = $time;
        $this->opening = json_encode($opening);
    }

    public function getMondayAfternoonStart() {
        $opening = json_decode($this->opening, true);
        return (isset($opening['mon']['end']['start']) ? $opening['mon']['end']['start'] : '');
    }

    public function setMondayAfternoonStart($time) {
        $opening = json_decode($this->opening, true);
        $opening['mon']['pm']['start'] = $time;
        $this->opening = json_encode($opening);
    }

    public function getMondayAfternoonEnd() {
        $opening = json_decode($this->opening, true);
        return (isset($opening['mon']['end']['end']) ? $opening['mon']['end']['end'] : '');
    }

    public function setMondayAfternoonEnd($time) {
        $opening = json_decode($this->opening, true);
        $opening['mon']['pm']['end'] = $time;
        $this->opening = json_encode($opening);
    }

    public function getTuesdayMorningStart() {
        $opening = json_decode($this->opening, true);
        return (isset($opening['tue']['am']['start']) ? $opening['tue']['am']['start'] : '');
    }

    public function setTuesdayMorningStart($time) {
        $opening = json_decode($this->opening, true);
        $opening['tue']['am']['start'] = $time;
        $this->opening = json_encode($opening);
    }

    public function getTuesdayMorningEnd() {
        $opening = json_decode($this->opening, true);
        return (isset($opening['tue']['am']['end']) ? $opening['tue']['am']['end'] : '');
    }

    public function setTuesdayMorningEnd($time) {
        $opening = json_decode($this->opening, true);
        $opening['tue']['am']['end'] = $time;
        $this->opening = json_encode($opening);
    }

    public function getTuesdayAfternoonStart() {
        $opening = json_decode($this->opening, true);
        return (isset($opening['tue']['pm']['start']) ? $opening['tue']['pm']['start'] : '');
    }

    public function setTuesdayAfternoonStart($time) {
        $opening = json_decode($this->opening, true);
        $opening['tue']['pm']['start'] = $time;
        $this->opening = json_encode($opening);
    }

    public function getTuesdayAfternoonEnd() {
        $opening = json_decode($this->opening, true);
        return (isset($opening['tue']['pm']['end']) ? $opening['tue']['pm']['end'] : '');
    }

    public function setTuesdayAfternoonEnd($time) {
        $opening = json_decode($this->opening, true);
        $opening['tue']['pm']['end'] = $time;
        $this->opening = json_encode($opening);
    }

    public function getWednesdayMorningStart() {
        $opening = json_decode($this->opening, true);
        return (isset($opening['wed']['am']['start']) ? $opening['wed']['am']['start'] : '');
    }

    public function setWednesdayMorningStart($time) {
        $opening = json_decode($this->opening, true);
        $opening['wed']['am']['start'] = $time;
        $this->opening = json_encode($opening);
    }

    public function getWednesdayMorningEnd() {
        $opening = json_decode($this->opening, true);
        return (isset($opening['wed']['am']['end']) ? $opening['wed']['am']['end'] : '');
    }

    public function setWednesdayMorningEnd($time) {
        $opening = json_decode($this->opening, true);
        $opening['wed']['am']['end'] = $time;
        $this->opening = json_encode($opening);
    }

    public function getWednesdayAfternoonStart() {
        $opening = json_decode($this->opening, true);
        return (isset($opening['wed']['pm']['start']) ? $opening['wed']['pm']['start'] : '');
    }

    public function setWednesdayAfternoonStart($time) {
        $opening = json_decode($this->opening, true);
        $opening['wed']['pm']['start'] = $time;
        $this->opening = json_encode($opening);
    }

    public function getWednesdayAfternoonEnd() {
        $opening = json_decode($this->opening, true);
        return (isset($opening['wed']['pm']['end']) ? $opening['wed']['pm']['end'] : '');
    }

    public function setWednesdayAfternoonEnd($time) {
        $opening = json_decode($this->opening, true);
        $opening['wed']['pm']['end'] = $time;
        $this->opening = json_encode($opening);
    }

    public function getThursdayMorningStart() {
        $opening = json_decode($this->opening, true);
        return (isset($opening['thu']['am']['start']) ? $opening['thu']['am']['start'] : '');
    }

    public function setThursdayMorningStart($time) {
        $opening = json_decode($this->opening, true);
        $opening['thu']['am']['start'] = $time;
        $this->opening = json_encode($opening);
    }

    public function getThursdayMorningEnd() {
        $opening = json_decode($this->opening, true);
        return (isset($opening['thu']['am']['end']) ? $opening['thu']['am']['end'] : '');
    }

    public function setThursdayMorningEnd($time) {
        $opening = json_decode($this->opening, true);
        $opening['thu']['am']['end'] = $time;
        $this->opening = json_encode($opening);
    }

    public function getThursdayAfternoonStart() {
        $opening = json_decode($this->opening, true);
        return (isset($opening['thu']['pm']['start']) ? $opening['thu']['pm']['start'] : '');
    }

    public function setThursdayAfternoonStart($time) {
        $opening = json_decode($this->opening, true);
        $opening['thu']['pm']['start'] = $time;
        $this->opening = json_encode($opening);
    }

    public function getThursdayAfternoonEnd() {
        $opening = json_decode($this->opening, true);
        return (isset($opening['thu']['pm']['end']) ? $opening['thu']['pm']['end'] : '');
    }

    public function setThursdayAfternoonEnd($time) {
        $opening = json_decode($this->opening, true);
        $opening['thu']['pm']['end'] = $time;
        $this->opening = json_encode($opening);
    }

    public function getFridayMorningStart() {
        $opening = json_decode($this->opening, true);
        return (isset($opening['fri']['am']['start']) ? $opening['fri']['am']['start'] : '');
    }

    public function setFridayMorningStart($time) {
        $opening = json_decode($this->opening, true);
        $opening['fri']['am']['start'] = $time;
        $this->opening = json_encode($opening);
    }

    public function getFridayMorningEnd() {
        $opening = json_decode($this->opening, true);
        return (isset($opening['fri']['am']['end']) ? $opening['fri']['am']['end'] : '');
    }

    public function setFridayMorningEnd($time) {
        $opening = json_decode($this->opening, true);
        $opening['fri']['am']['end'] = $time;
        $this->opening = json_encode($opening);
    }

    public function getFridayAfternoonStart() {
        $opening = json_decode($this->opening, true);
        return (isset($opening['fri']['pm']['start']) ? $opening['fri']['pm']['start'] : '');
    }

    public function setFridayAfternoonStart($time) {
        $opening = json_decode($this->opening, true);
        $opening['fri']['pm']['start'] = $time;
        $this->opening = json_encode($opening);
    }

    public function getFridayAfternoonEnd() {
        $opening = json_decode($this->opening, true);
        return (isset($opening['fri']['pm']['end']) ? $opening['fri']['pm']['end'] : '');
    }

    public function setFridayAfternoonEnd($time) {
        $opening = json_decode($this->opening, true);
        $opening['fri']['pm']['end'] = $time;
        $this->opening = json_encode($opening);
    }

    public function getSaturdayMorningStart() {
        $opening = json_decode($this->opening, true);
        return (isset($opening['sat']['am']['start']) ? $opening['sat']['am']['start'] : '');
    }

    public function setSaturdayMorningStart($time) {
        $opening = json_decode($this->opening, true);
        $opening['sat']['am']['start'] = $time;
        $this->opening = json_encode($opening);
    }

    public function getSaturdayMorningEnd() {
        $opening = json_decode($this->opening, true);
        return (isset($opening['sat']['am']['end']) ? $opening['sat']['am']['end'] : '');
    }

    public function setSaturdayMorningEnd($time) {
        $opening = json_decode($this->opening, true);
        $opening['sat']['am']['end'] = $time;
        $this->opening = json_encode($opening);
    }

    public function getSaturdayAfternoonStart() {
        $opening = json_decode($this->opening, true);
        return (isset($opening['sat']['pm']['start']) ? $opening['sat']['pm']['start'] : '');
    }

    public function setSaturdayAfternoonStart($time) {
        $opening = json_decode($this->opening, true);
        $opening['sat']['pm']['start'] = $time;
        $this->opening = json_encode($opening);
    }

    public function getSaturdayAfternoonEnd() {
        $opening = json_decode($this->opening, true);
        return (isset($opening['sat']['pm']['end']) ? $opening['sat']['pm']['end'] : '');
    }

    public function setSaturdayAfternoonEnd($time) {
        $opening = json_decode($this->opening, true);
        $opening['sat']['pm']['end'] = $time;
        $this->opening = json_encode($opening);
    }

    public function getSundayMorningStart() {
        $opening = json_decode($this->opening, true);
        return (isset($opening['sun']['am']['start']) ? $opening['sun']['am']['start'] : '');
    }

    public function setSundayMorningStart($time) {
        $opening = json_decode($this->opening, true);
        $opening['sun']['am']['start'] = $time;
        $this->opening = json_encode($opening);
    }

    public function getSundayMorningEnd() {
        $opening = json_decode($this->opening, true);
        return (isset($opening['sun']['am']['end']) ? $opening['sun']['am']['end'] : '');
    }

    public function setSundayMorningEnd($time) {
        $opening = json_decode($this->opening, true);
        $opening['sun']['am']['end'] = $time;
        $this->opening = json_encode($opening);
    }

    public function getSundayAfternoonStart() {
        $opening = json_decode($this->opening, true);
        return (isset($opening['sun']['pm']['start']) ? $opening['sun']['pm']['start'] : '');
    }

    public function setSundayAfternoonStart($time) {
        $opening = json_decode($this->opening, true);
        $opening['sun']['pm']['start'] = $time;
        $this->opening = json_encode($opening);
    }

    public function getSundayAfternoonEnd() {
        $opening = json_decode($this->opening, true);
        return (isset($opening['sun']['pm']['end']) ? $opening['sun']['pm']['end'] : '');
    }

    public function setSundayAfternoonEnd($time) {
        $opening = json_decode($this->opening, true);
        $opening['sun']['pm']['end'] = $time;
        $this->opening = json_encode($opening);
    }

    public function getOpening() {
        return $this->opening;
    }

    public function setOpening($hours) {
        $this->opening = (string) $hours;
    }

    public function getDescription() {
        return $this->description;
    }

    public function setDescription($description) {
        $this->description = (string) $description;
    }

    public function getPhone() {
        return $this->phone;
    }

    public function setPhone($phone) {
        $this->phone = filter_var($phone, FILTER_SANITIZE_NUMBER_INT);
    }

    public function getAddress() {
        return $this->address;
    }

    public function setAddress(Address $address) {
        $this->address = $address;
    }

    public function getActivity() {
        return $this->activity;
    }

    public function setActivity(JobActivities $activity) {
        $this->activity = $activity;
    }

    public function getPhoto() {
        return $this->photo;
    }

    public function setPhoto($photo) {
        $this->photo = filter_var($photo, FILTER_SANITIZE_URL);
    }

    public function getUrl() {
        return $this->url;
    }

    public function setUrl($url) {
        $this->url = filter_var($url, FILTER_SANITIZE_URL);
    }

    public function getTimeAsText() {
        return $this->time_as_text;
    }

    public function setTimeAsText($time) {
        $this->time_as_text = filter_var($time, FILTER_SANITIZE_STRING);
    }
}