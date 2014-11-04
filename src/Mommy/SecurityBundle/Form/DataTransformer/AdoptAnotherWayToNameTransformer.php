<?php

namespace Mommy\SecurityBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Doctrine\Common\Persistence\ObjectManager;
use Mommy\ProfilBundle\Entity\AdoptAnotherWay;

class AdoptAnotherWayToNameTransformer implements DataTransformerInterface
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
     * @param  AdoptAnotherWay|null $anotherway
     * @return string
     */
    public function transform($anotherway) {
        if (null === $anotherway) {
            return "";
        }
        return $anotherway->getName();
    }

    /**
     * Transforms a string (name) to an object (anotherway).
     *
     * @param  string $name
     * @return AdoptAnotherWay|null
     * @throws TransformationFailedException if object (anotherway) is not found.
     */
    public function reverseTransform($name)
    {
        if (!$name) {
            return null;
        }

        $anotherway = $this->om->getRepository('MommyProfilBundle:AdoptAnotherWay')->findOneBy(array('name' => $name));

        if (null === $anotherway) {
            throw new TransformationFailedException(sprintf(
                'A anotherway with name "%s" does not exist!',
                $name
            ));
        }
        return $anotherway;
    }
}