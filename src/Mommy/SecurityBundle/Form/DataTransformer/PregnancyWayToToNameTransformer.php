<?php

namespace Mommy\SecurityBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Doctrine\Common\Persistence\ObjectManager;
use Mommy\ProfilBundle\Entity\PregnancyWayTo;

class PregnancyWayToToNameTransformer implements DataTransformerInterface
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
     * @param  PregnancyWayTo|null $wayto
     * @return string
     */
    public function transform($wayto) {
        if (null === $wayto) {
            return "";
        }
        return $wayto->getName();
    }

    /**
     * Transforms a string (name) to an object (wayto).
     *
     * @param  string $name
     * @return PregnancyWayTo|null
     * @throws TransformationFailedException if object (wayto) is not found.
     */
    public function reverseTransform($name)
    {
        if (!$name) {
            return null;
        }

        $wayto = $this->om->getRepository('MommyProfilBundle:PregnancyWayTo')->findOneBy(array('name' => $name));

        if (null === $wayto) {
            throw new TransformationFailedException(sprintf(
                'A wayto with name "%s" does not exist!',
                $name
            ));
        }
        return $wayto;
    }
}