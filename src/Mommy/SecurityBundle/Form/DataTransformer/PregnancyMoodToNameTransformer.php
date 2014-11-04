<?php

namespace Mommy\SecurityBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Doctrine\Common\Persistence\ObjectManager;
use Mommy\ProfilBundle\Entity\PregnancyMood;

class PregnancyMoodToNameTransformer implements DataTransformerInterface
{
    /**
     * @var ObjectManager
     */
    private $om;

    /**
     * @param ObjectManager $om
     */
    public function __construct(ObjectManager $om) {
        $this->om = $om;
    }

    /**
     * @param  PregnancyMood|null $mood
     * @return string
     */
    public function transform($mood) {
        if (null === $mood) {
            return "";
        }
        return $mood->getName();
    }

    /**
     * Transforms a string (name) to an object (mood).
     *
     * @param  string $name
     * @return PregnancyMood|null
     * @throws TransformationFailedException if object (mood) is not found.
     */
    public function reverseTransform($name)
    {
        if (!$name) {
            return null;
        }

        $mood = $this->om->getRepository('MommyProfilBundle:PregnancyMood')->findOneBy(array('name' => $name));

        if (null === $mood) {
            throw new TransformationFailedException(sprintf(
                'A mood with name "%s" does not exist!',
                $name
            ));
        }
        return $mood;
    }
}