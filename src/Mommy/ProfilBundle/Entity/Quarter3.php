<?php

namespace Mommy\ProfilBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;
use Doctrine\ORM\Mapping as ORM;

/**
 * Mommy\ProfilBundle\Entity\Quarter3
 *
 * @ORM\Table(name="mv_quarter3", indexes={@ORM\Index(name="search_idx", columns={"id", "pid"})})
 * @ORM\Entity()
 */
class Quarter3
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Pregnancy", cascade={"remove"})
     * @ORM\JoinColumn(name="pid", referencedColumnName="id", onDelete="CASCADE")
     */
    private $pregnancy;

    /**
     * @ORM\ManyToOne(targetEntity="PregnancyResult")
     * @ORM\JoinColumn(name="result_id", referencedColumnName="id", nullable=true)
     */
    private $result;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $stopped;

    /**
     * @ORM\ManyToOne(targetEntity="PregnancyStatus", cascade={"persist"})
     * @ORM\JoinColumn(name="status_id", referencedColumnName="id", nullable=true)
     */
    private $status;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $consult3;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $PathologyPregnancy;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $PathologyBaby;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $PregnancySymptoms;

    /**
     * @ORM\ManyToOne(targetEntity="PathologyBabyDigestive")
     * @ORM\JoinColumn(name="pathobb_digestive_id", referencedColumnName="id", nullable=true)
     */
    private $pathobb_digestive;

    /**
     * @ORM\ManyToOne(targetEntity="PathologyBabyBladder")
     * @ORM\JoinColumn(name="pathobb_bladder_id", referencedColumnName="id", nullable=true)
     */
    private $pathobb_bladder;

    /**
     * @ORM\ManyToOne(targetEntity="PathologyBabyEar")
     * @ORM\JoinColumn(name="pathobb_ear_id", referencedColumnName="id", nullable=true)
     */
    private $pathobb_ear;

    /**
     * @ORM\ManyToOne(targetEntity="PathologyBabyEye")
     * @ORM\JoinColumn(name="pathobb_eye_id", referencedColumnName="id", nullable=true)
     */
    private $pathobb_eye;

    /**
     * @ORM\ManyToOne(targetEntity="PathologyBabyGenitale")
     * @ORM\JoinColumn(name="pathobb_genitale_id", referencedColumnName="id", nullable=true)
     */
    private $pathobb_genitale;

    /**
     * @ORM\ManyToOne(targetEntity="PathologyBabyLung")
     * @ORM\JoinColumn(name="pathobb_lung_id", referencedColumnName="id", nullable=true)
     */
    private $pathobb_lung;

    /**
     * @ORM\ManyToOne(targetEntity="PathologyBabyNerves")
     * @ORM\JoinColumn(name="pathobb_nerves_id", referencedColumnName="id", nullable=true)
     */
    private $pathobb_nerves;

    /**
     * @ORM\ManyToOne(targetEntity="PathologyBabySkel")
     * @ORM\JoinColumn(name="pathobb_skel_id", referencedColumnName="id", nullable=true)
     */
    private $pathobb_skel;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $daycare_planned;

    /**
     * @ORM\ManyToOne(targetEntity="ChildDaycare")
     * @ORM\JoinColumn(name="daycare_id", referencedColumnName="id", nullable=true)
     */
    private $daycare;

    public function __construct() {
        $this->PathologyPregnancy = json_encode(array());
        $this->PathologyBaby = json_encode(array());
        $this->PregnancySymptoms = json_encode(array());
    }

    public function getId() {
        return $this->id;
    }    

    public function getConsult3() {
        return $this->consult3;
    }

    public function setConsult3($bool) {
        $this->consult3 = ($bool === true);
    }

    public function getPregnancy() {
        return $this->pregnancy;
    }

    public function setPregnancy(Pregnancy $pregnancy) {
        $this->pregnancy = $pregnancy;
    }

    public function getResult() {
        return $this->result;
    }

    public function setResult($result) {
        $this->result = $result;
    }

    public function getStopped() {
        return $this->stopped;
    }

    public function setStopped($bool) {
        $this->stopped = ($bool === true);
    }

    public function getStatus() {
        return $this->status;
    }

    public function setStatus($status) {
        $this->status = $status;
    }

    public function getPathologyPregnancy() {
        $patho = json_decode($this->PathologyPregnancy, true);
        if (is_null($patho))
            return array();
        return $patho;
    }

    public function setPathologyPregnancy($patho) {
        $patho = trim($patho);
        if (empty($patho)) return ;
        $this->PathologyPregnancy = json_encode(explode(',', $patho));
    }

    public function getPathologyBaby() {
        $patho = json_decode($this->PathologyBaby, true);
        if (is_null($patho))
            return array();
        return $patho;
    }

    public function setPathologyBaby($patho) {
        $patho = trim($patho);
        if (empty($patho)) return ;
        $this->PathologyBaby = json_encode(explode(',', $patho));
    }

    public function getPregnancySymptoms() {
        $patho = json_decode($this->PregnancySymptoms, true);
        if (is_null($patho))
            return array();
        return $patho;
    }

    public function setPregnancySymptoms($patho) {
        $patho = trim($patho);
        if (empty($patho)) return ;
        $this->PregnancySymptoms = json_encode(explode(',', $patho));
    }

    public function getPathoBabyDigestive() {
        return $this->pathobb_digestive;
    }

    public function setPathoBabyDigestive(PathologyBabyDigestive $patho) {
        $this->pathobb_digestive = $patho;
    }

    public function getPathoBabyBladder() {
        return $this->pathobb_bladder;
    }

    public function setPathoBabyBladder(PathologyBabyBladder $patho) {
        $this->pathobb_bladder = $patho;
    }

    public function getPathoBabyEar() {
        return $this->pathobb_ear;
    }

    public function setPathoBabyEar(PathologyBabyEar $patho) {
        $this->pathobb_ear = $patho;
    }

    public function getPathoBabyEye() {
        return $this->pathobb_eye;
    }

    public function setPathoBabyEye(PathologyBabyEye $patho) {
        $this->pathobb = $patho;
    }

    public function getPathoBabyGenitale() {
        return $this->pathobb_genitale;
    }

    public function setPathoBabyGenitale(PathologyBabyGenitale $patho) {
        $this->pathobb_genitale = $patho;
    }

    public function getPathoBabyLung() {
        return $this->pathobb_lung;
    }

    public function setPathoBabyLung(PathologyBabyLung $patho) {
        $this->pathobb_lung = $patho;
    }

    public function getPathoBabyNerves() {
        return $this->pathobb_nerves;
    }

    public function setPathoBabyNerves(PathologyBabyNerves $patho) {
        $this->pathobb_nerves = $patho;
    }

    public function getPathoBabySkel() {
        return $this->pathobb_skel;
    }

    public function setPathoBabySkel(PathologyBabySkel $patho) {
        $this->pathobb_skel = $patho;
    }

    public function getDaycarePlanned() {
        return $this->daycare_planned;
    }

    public function setDaycarePlanned($bool) {
        $this->daycare_planned = ($bool === true);
    }

    public function getDaycare() {
        return $this->daycare;
    }

    public function setDaycare(ChildDaycare $daycare) {
        $this->daycare = $daycare;
    }
}