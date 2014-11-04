<?php

namespace Mommy\ProfilBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;
use Doctrine\ORM\Mapping as ORM;

/**
 * Mommy\ProfilBundle\Entity\Question
 *
 * @ORM\Table(name="mv_question", indexes={@ORM\Index(name="search_idx", columns={"owner"})}))
 * @ORM\Entity()
 */
class Question
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
     * @ORM\Column(type="text")
     */
    private $question;

    /**
     * @ORM\Column(type="string", length=14)
     */
    private $submitted;

    /**
     * @ORM\Column(type="boolean")
     */
    private $active;

    public function getOwner() {
        return $this->owner;
    }

    public function setOwner($owner) {
        if (!is_integer($owner)) return;
        $user = $this->getDoctrine()->getRepository('MommyProfilBundle:User')->find($owner);
        if ($user->getId())
            $this->owner = $owner;
    }

    public function getQuestion() {
        return $this->question;
    }

    public function setQuestion($question) {
        if (!is_string($question)) return;
        $question = trim($question);
        $this->question = $question;
    }

    public function getSubmitted() {
        return $this->submitted;
    }

    public function setSubmitted($submitted = null) {
        if (is_null($submitted)) $submitted = date('YmdHis');
        if (!is_string($submitted)) return;
        $this->submitted = $submitted;
    }

    public function getActive() {
        return $this->active;
    }

    public function setActive($active) {
        if (!is_bool($active)) return;
        $this->active = $active;
    }

    public function getAnswers() {
        return $this->getDoctrine()->getRepository('MommyQuestionBundle:Answer')->find(array('question' => $this->id));
    }
}