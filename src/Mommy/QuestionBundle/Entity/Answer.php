<?php

namespace Mommy\ProfilBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;
use Doctrine\ORM\Mapping as ORM;

/**
 * Mommy\ProfilBundle\Entity\Answer
 *
 * @ORM\Table(name="mv_answer", indexes={@ORM\Index(name="search_idx", columns={"owner", "question"})}))
 * @ORM\Entity()
 */
class Answer
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
     * @ORM\Column(type="integer")
     */
    private $question;

    /**
     * @ORM\Column(type="text")
     */
    private $answer;

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
        if (!is_integer($question)) return;
        $q = $this->getDoctrine()->getRepository('MommyQuestionBundle:Question')->find($question);
        if ($q->getId())
            $this->question = $question;
    }

    public function getAnswer() {
        return $this->question;
    }

    public function setAnswer($answer) {
        if (!is_string($answer)) return;
        $answer = trim($answer);
        $this->answer = $answer;
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
}