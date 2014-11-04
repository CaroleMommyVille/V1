<?php

namespace Mommy\SecurityBundle\Entity;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;
use Doctrine\ORM\Mapping as ORM;

use Mommy\ProfilBundle\Entity\Address;

/**
 * Mommy\SecurityBundle\Entity\User
 *
 * @ORM\Table(name="mv_users", indexes={@ORM\Index(name="search_idx", columns={"id", "username"})})
 * @ORM\Entity()
 * @DoctrineAssert\UniqueEntity(fields={"username"}, message="Cet utilisateur est déjà enregistré" )
 */
class User implements UserInterface, AdvancedUserInterface, \Serializable
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=125, unique=true)
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=64)
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=125, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=254)
     */
    private $firstname;

    /**
     * @ORM\Column(type="string", length=254)
     */
    private $lastname;

    /**
     * @ORM\Column(name="is_active", type="boolean")
     */
    private $isActive;

    /**
     * @ORM\Column(name="is_locked", type="boolean")
     */
    private $isLocked;

    /**
     * @ORM\Column(type="integer")
     */
    private $birthday;

    /**
     * @ORM\Column(type="boolean")
     */
    private $cnil = false;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $photo = null;

    /**
     * @ORM\ManyToOne(targetEntity="\Mommy\ProfilBundle\Entity\Address")
     * @ORM\JoinColumn(name="aid", referencedColumnName="id")
     */
    private $address;
    
    /**
     * @ORM\Column(type="integer")
     */
    private $since;
    
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description = null;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $logintime;

    public function __construct() {
        $this->isActive = true;
        $this->isLocked = false;
    }

    /**
     * @inheritDoc
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @inheritDoc
     */
    public function getEmail() {
        return $this->email;
    }

    /**
     * @inheritDoc
     */
    public function setEmail($email) {
    	if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) return false;
        $this->email = trim($email);
        $this->username = trim($email);
    }

    /**
     * @inheritDoc
     */
    public function getUsername() {
        return $this->username;
    }

    /**
     * @inheritDoc
     */
    public function setUsername($username) {
    	if (filter_var($username, FILTER_VALIDATE_EMAIL) === false) return false;
        $this->username = trim($username);
    }

    /**
     * @inheritDoc
     */
    public function getLastname() {
        return $this->lastname;
    }

    /**
     * @inheritDoc
     */
    public function setLastname($lastname) {
		$lastname = ucwords(strtolower($lastname));
		$this->lastname = trim($lastname);
    }

    /**
     * @inheritDoc
     */
    public function getFirstname() {
        return $this->firstname;
    }

    /**
     * @inheritDoc
     */
    public function setFirstname($firstname) {
		$firstname = ucwords(strtolower($firstname));
		$this->firstname = trim($firstname);
    }

    /**
     * @inheritDoc
     */
    public function getSalt() {
        return null;
    }

    /**
     * @inheritDoc
     */
    public function getPassword() {
        return $this->password;
    }

    /**
     * @inheritDoc
     */
    public function setPassword($password) {
		$this->password = password_hash($password, PASSWORD_BCRYPT, array('cost' => 13));
    }

    /**
     * @inheritDoc
     */
    public function getRoles() {
        return array('ROLE_USER');
    }

    /**
     * @inheritDoc
     */
    public function eraseCredentials() {
    }

    /**
     * @see \Serializable::serialize()
     */
    public function serialize() {
        return serialize(array(
            $this->id,
            $this->username,
            $this->password,
            $this->firstname,
            $this->lastname,
        ));
    }

    /**
     * @see \Serializable::unserialize()
     */
    public function unserialize($serialized) {
        list (
            $this->id,
            $this->username,
            $this->password,
            $this->firstname,
            $this->lastname,
        ) = unserialize($serialized);
    }

    public function isAccountNonLocked() {
        return !$this->isLocked;
    }

    public function lockAccount($locked) {
		$this->isLocked = ($locked !== false);
    }

    public function isAccountNonExpired() {
        return $this->isActive;
    }

    public function isCredentialsNonExpired() {
       return $this->isActive;
    }

    public function isEnabled() {
        return $this->isActive;
    }

    public function enableAccount() {
        $this->isActive = true;
    }

    public function disableAccount() {
        $this->isActive = false;
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

    public function getCnil() {
    	return $this->cnil;
    }

    public function setCnil($cnil) {
    	$this->cnil = ($cnil === true);
    }

    public function getAddress() {
        return $this->address;
    }

    public function setAddress(Address $address) {
        $this->address = $address;
    }

    public function getSince($as_int = false) {
        if ($as_int) return $this->since;
        return date('d-m-Y', $this->since);
    }

    public function setSince($since) {
        if ($since == 'now') $since = time();
        else $since = strtotime($since);
        $this->since = $since;
    }

    public function getRole() {
        return array('ROLE_USER');
    }

    public function getDescription() {
        return $this->description;
    }

    public function setDescription($description) {
        $this->description = filter_var($description, FILTER_SANITIZE_STRING);
    }

    public function getLoginTime() {
        return $this->logintime;
    }

    public function setLoginTime(\DateTime $logintime) {
        $this->logintime = $logintime;
    }

    public function getPhoto() {
        return $this->photo;
    }

    public function setPhoto($photo) {
        $this->photo = filter_var($photo, FILTER_SANITIZE_URL);
    }

    public function getCity() {
        if (!is_null($this->address)) return $this->address->getCity();
        else return '';
    }
}