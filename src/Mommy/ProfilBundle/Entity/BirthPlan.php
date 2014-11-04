<?php

namespace Mommy\ProfilBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;
use Doctrine\ORM\Mapping as ORM;

use Mommy\SecurityBundle\Entity\User;

/**
 * Mommy\ProfilBundle\Entity\BirthPlan
 *
 * @ORM\Table(name="mv_birth_plan", indexes={@ORM\Index(name="search_idx", columns={"id", "uid"})})
 * @ORM\Entity()
 */
class BirthPlan
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
     * @ORM\ManyToOne(targetEntity="Maternity")
     * @ORM\JoinColumn(name="mid", referencedColumnName="id")
     */
    protected $maternity;

    /**
     * @ORM\ManyToOne(targetEntity="MaternityFound")
     * @ORM\JoinColumn(name="mfid", referencedColumnName="id")
     */
    protected $maternity_found;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $preparation = null;

    /**
     * @ORM\ManyToOne(targetEntity="PregnancyFood")
     * @ORM\JoinColumn(name="food_id", referencedColumnName="id")
     */
    protected $food;

    /**
     * @ORM\ManyToOne(targetEntity="DeliveryMethod")
     * @ORM\JoinColumn(name="method_id", referencedColumnName="id")
     */
    protected $method;

    /**
     * @ORM\ManyToOne(targetEntity="ChildBreastfed")
     * @ORM\JoinColumn(name="breastfed_id", referencedColumnName="id")
     */
    protected $breastfed;

    public function __construct() {
        $this->preparation = json_encode(array());
    }

    public function getId() {
        return $this->id;
    }    

    public function getMaternityFound() {
        return $this->maternity_found;
    }

    public function setMaternityFound(MaternityFound $maternity_found) {
        $this->maternity_found = $maternity_found;
    }

    public function getPreparation() {
        $preparation = json_decode($this->preparation, true);
        if (is_null($preparation))
            return array();
        return $preparation;
    }

    public function setPreparation($preparation) {
        $preparation = trim($preparation);
        if (empty($preparation)) return ;
        $this->preparation = json_encode(explode(',', $preparation));
    }

    public function getFood() {
        return $this->food;
    }

    public function setFood(PregnancyFood $food) {
        $this->food = $food;
    }

    public function getMethod() {
        return $this->method;
    }

    public function setMethod(DeliveryMethod $method) {
        $this->method = $method;
    }

    public function getBreastfed() {
        return $this->breastfed;
    }

    public function setBreastfed(ChildBreastfed $breastfed) {
        $this->breastfed = $breastfed;
    }

    public function getUser() {
        return $this->user;
    }

    public function setUser(User $user) {
        $this->user = $user;
    }

    public function getMaternity() {
        return $this->maternity;
    }

    public function setMaternity(Maternity $maternity) {
        $this->maternity = $maternity;
    }
}