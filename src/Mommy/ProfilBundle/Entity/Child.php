<?php

namespace Mommy\ProfilBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;

use Mommy\SecurityBundle\Entity\User;

/**
 * Mommy\ProfilBundle\Entity\Child
 *
 * @ORM\Table(name="mv_child", indexes={@ORM\Index(name="search_idx", columns={"id", "uid"})})
 * @ORM\Entity()
 */
class Child
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     */
    private $firstname;

    /**
     * @ORM\ManyToOne(targetEntity="\Mommy\SecurityBundle\Entity\User", cascade={"remove"})
     * @ORM\JoinColumn(name="uid", referencedColumnName="id", onDelete="CASCADE")
     */
    private $user;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $birthday;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $sa;

    /**
     * @ORM\ManyToOne(targetEntity="ChildSex")
     * @ORM\JoinColumn(name="sex_id", referencedColumnName="id", nullable=true)
     */
    private $sex;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $twin;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $adopted;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $ondate;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $sick;

    /**
     * @ORM\ManyToOne(targetEntity="DeliveryMethodChange")
     * @ORM\JoinColumn(name="change_id", referencedColumnName="id", nullable=true)
     */
    protected $delivery_method_change;

    /**
     * @ORM\ManyToOne(targetEntity="DeliveryMethod")
     * @ORM\JoinColumn(name="method_id", referencedColumnName="id", nullable=true)
     */
    protected $delivery_method;

    /**
     * @ORM\ManyToOne(targetEntity="PregnancySpeed")
     * @ORM\JoinColumn(name="speed_id", referencedColumnName="id", nullable=true)
     */
    protected $speed;

    /**
     * @ORM\ManyToOne(targetEntity="Reaction")
     * @ORM\JoinColumn(name="reaction_id", referencedColumnName="id", nullable=true)
     */
    protected $reaction;

    /**
     * @ORM\ManyToOne(targetEntity="Maternity")
     * @ORM\JoinColumn(name="maternity_id", referencedColumnName="id", nullable=true)
     */
    protected $maternity;

    /**
     * @ORM\ManyToOne(targetEntity="PregnancyPreparation")
     * @ORM\JoinColumn(name="preparation_id", referencedColumnName="id", nullable=true)
     */
    protected $preparation;

    /**
     * @ORM\ManyToOne(targetEntity="PregnancyStatus")
     * @ORM\JoinColumn(name="status_id", referencedColumnName="id", nullable=true)
     */
    protected $pregnancy_status;

    /**
     * @ORM\ManyToOne(targetEntity="ChildBreastfed")
     * @ORM\JoinColumn(name="breastfed_id", referencedColumnName="id", nullable=true)
     */
    protected $breastfed;

    /**
     * @ORM\ManyToOne(targetEntity="ChildDisease")
     * @ORM\JoinColumn(name="disease_id", referencedColumnName="id", nullable=true)
     */
    protected $disease;

    /**
     * @ORM\ManyToOne(targetEntity="ChildDiseaseHeart")
     * @ORM\JoinColumn(name="disease_heart_id", referencedColumnName="id", nullable=true)
     */
    protected $disease_heart;

    /**
     * @ORM\ManyToOne(targetEntity="ChildDaycare")
     * @ORM\JoinColumn(name="daycare_id", referencedColumnName="id", nullable=true)
     */
    protected $daycare;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $hobbies;

    /**
     * @ORM\ManyToOne(targetEntity="ChildSchool")
     * @ORM\JoinColumn(name="school_id", referencedColumnName="id", nullable=true)
     */
    protected $school;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $sport;

    /**
     * @ORM\ManyToOne(targetEntity="ChildSchoolType")
     * @ORM\JoinColumn(name="school_type_id", referencedColumnName="id", nullable=true)
     */
    protected $school_type;

    /**
     * @ORM\ManyToOne(targetEntity="AdoptCountry")
     * @ORM\JoinColumn(name="country_id", referencedColumnName="id", nullable=true)
     */
    protected $country;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $episiotomie;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $planned;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $complications;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $pathogro;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $pathobb;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $symptoms;

    public function __construct() {
        $this->pathogro = json_encode(array());
        $this->pathobb = json_encode(array());
        $this->symptoms = json_encode(array());
        $this->sports = json_encode(array());
        $this->hobbies = json_encode(array());
    }

    public function getId() {
        return $this->id;
    }    

    public function getName() {
        return $this->firstname;
    }

    public function setName($name) {
        if (!is_string($name)) return false;
        $this->firstname = $name;
    }

    public function getSex() {
        return $this->sex;
    }

    public function setSex(ChildSex $sex) {
        $this->sex = $sex;
    }


    public function getMother() {
        return $this->user;
    }

    public function setMother(User $user) {
        $this->user = $user;
    }

    public function getTwin() {
        return $this->twin;
    }

    public function setTwin($twin) {
        $this->twin = $twin;
    }

    public function getAdopted() {
        return $this->adopted;
    }

    public function setAdopted($bool) {
        $this->adopted = ($bool == true);
    }

    public function getBirthday($as_int = false) {
        if ($as_int) return $this->birthday;
        return date('d-m-Y', $this->birthday);
    }

    public function getAge() {
        $secPerYear = 31556926;
        return floor((time() - $this->birthday) / $secPerYear);
    }

    public function setBirthday($date) {
        if (strlen($date) != 10) return false;
        $date = strtotime($date);
        if ($date > (time() - 14*365*24*3600)) return false;
        if ($date < (time() - 60*365*24*3600)) return false;
        $this->birthday = $date;
    }

    public function getPregnancySpeed() {
        return $this->speed;
    }

    public function setPregnancySpeed(PregnancySpeed $speed) {
        $this->speed = $speed;
    }

    public function getOnDate() {
        return $this->ondate;
    }

    public function setOnDate($bool) {
        $this->ondate = ($bool == true);
    }

    public function getReaction() {
        return $this->reaction;
    }

    public function setReaction(Reaction $reaction) {
        $this->reaction = $reaction;
    }

    public function getSA() {
        return $this->sa;
    }

    public function setSA($sa) {
        $this->sa = $sa;
    }

    public function getMaternity() {
        return $this->maternity;
    }

    public function setMaternity(Maternity $maternity) {
        $this->maternity = $maternity;
    }

    public function getMethodChange() {
        return $this->delivery_method_change;
    }

    public function setMethodChange(DeliveryMethodChange $delivery_method_change) {
        $this->delivery_method_change = $delivery_method_change;
    }

    public function getDeliveryMethod() {
        return $this->delivery_method;
    }

    public function setDeliveryMethod(DeliveryMethod $delivery_method) {
        $this->delivery_method = $delivery_method;
    }

    public function getPreparation() {
        return $this->preparation;
    }

    public function setPreparation(PregnancyPreparation $preparation) {
        $this->preparation = $preparation;
    }

    public function getPregnancyStatus() {
        return $this->pregnancy_status;
    }

    public function setPregnancyStatus(PregnancyStatus $pregnancy_status) {
        $this->pregnancy_status = $pregnancy_status;
    }

    public function getBreastfed() {
        return $this->breastfed;
    }

    public function setBreastfed(ChildBreastfed $breastfed) {
        $this->breastfed = $breastfed;
    }

    public function getSick() {
        return $this->sick;
    }

    public function setSick($bool) {
        $this->sick = ($bool == true);
    }

    public function getDisease() {
        return $this->disease;
    }

    public function setDisease(ChildDisease $disease) {
        $this->disease = $disease;
    }

    public function getDiseaseHeart() {
        return $this->disease_heart;
    }

    public function setDiseaseHeart(ChildDiseaseHeart $disease_heart) {
        $this->disease_heart = $disease_heart;
    }

    public function getDaycare() {
        return $this->daycare;
    }

    public function setDaycare(ChildDaycare $daycare) {
        $this->daycare = $daycare;
    }

    public function getSchool() {
        return $this->school;
    }

    public function setSchool(ChildSchool $school) {
        $this->school = $school;    
    }

    public function getSchoolType() {
        return $this->school_type;
    }

    public function setSchoolType(ChildSchoolType $school_type) {
        $this->school_type = $school_type;  
    }

    public function getCountry() {
        return $this->country;
    }

    public function setCountry(AdoptCountry $country) {
        $this->country = $country;    
    }

    public function getEpisiotomie() {
        return $this->episiotomie;
    }

    public function setEpisiotomie($episiotomie) {
        $this->episiotomie = ($episiotomie == true);
    }

    public function getPlanned() {
        return $this->planned;
    }

    public function setPlanned($planned) {
        $this->planned = ($planned == true);
    }

    public function getComplications() {
        return $this->complications;
    }

    public function setComplications($complications) {
        $this->complications = $complications;
    }

    public function getPathologyPregnancy() {
        $pathogro = json_decode($this->pathogro, true);
        if (is_null($pathogro))
            return array();
        return $pathogro;
    }

    public function setPathologyPregnancy($pathogro) {
        $pathogro = trim($pathogro);
        if (empty($pathogro)) return ;
        $this->pathogro = json_encode(explode(',', $pathogro));
    }

    public function getPathologyBaby() {
        $pathobb = json_decode($this->pathobb, true);
        if (is_null($pathobb))
            return array();
        return $pathobb;
    }

    public function setPathologyBaby($pathobb) {
        $pathobb = trim($pathobb);
        if (empty($pathobb)) return ;
        $this->pathobb = json_encode(explode(',', $pathobb));
    }

    public function getPregnancySymptoms() {
        $symptoms = json_decode($this->symptoms, true);
        if (is_null($symptoms))
            return array();
        return $symptoms;
    }

    public function setPregnancySymptoms($symptoms) {
        $symptoms = trim($symptoms);
        if (empty($symptoms)) return ;
        $this->symptoms = json_encode(explode(',', $symptoms));
    }

    public function getChildSport() {
        $sports = json_decode($this->sports, true);
        if (is_null($sports))
            return array();
        return $sports;
    }

    public function setChildSport($sports) {
        $sports = trim($sports);
        if (empty($sports)) return ;
        $this->sports = json_encode(explode(',', $sports));
    }

    public function getChildHobby() {
        $hobbies = json_decode($this->hobbies, true);
        if (is_null($hobbies))
            return array();
        return $hobbies;
    }

    public function setChildHobby($hobbies) {
        $hobbies = trim($hobbies);
        if (empty($hobbies)) return ;
        $this->hobbies = json_encode(explode(',', $hobbies));
    }
}