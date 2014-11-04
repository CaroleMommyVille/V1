<?php

namespace Mommy\SecurityBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Doctrine\Common\Persistence\ObjectManager;
use Mommy\ProfilBundle\Entity\PregnancySpeed;

class SpeedToNameTransformer implements DataTransformerInterface
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
     * @param  PregnancySpeed|null $speed
     * @return string
     */
    public function transform($speed) {
        if (null === $speed) return "";
        return $speed->getName();
    }

    /**
     * Transforms a string (name) to an object (speed).
     *
     * @param  string $name
     * @return PregnancySpeed|null
     * @throws TransformationFailedException if object (speed) is not found.
     */
    public function reverseTransform($name)
    {
        if (!$name) return null;

        $speed = $this->om->getRepository('MommyProfilBundle:PregnancySpeed')->findOneByName($name);

        if (null === $speed) {
            throw new TransformationFailedException(sprintf(
                'A speed with name "%s" does not exist!',
                $name
            ));
        }
        return $speed;
    }
}