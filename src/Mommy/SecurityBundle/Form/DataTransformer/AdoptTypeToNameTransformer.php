<?php

namespace Mommy\SecurityBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Doctrine\Common\Persistence\ObjectManager;
use Mommy\ProfilBundle\Entity\AdoptType;

class AdoptTypeToNameTransformer implements DataTransformerInterface
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
     * @param  AdoptType|null $AdoptType
     * @return string
     */
    public function transform($AdoptType) {
        if (null === $AdoptType) {
            return "";
        }
        return $AdoptType->getName();
    }

    /**
     * Transforms a string (name) to an object (AdoptType).
     *
     * @param  string $name
     * @return AdoptType|null
     * @throws TransformationFailedException if object (AdoptType) is not found.
     */
    public function reverseTransform($name)
    {
        if (!$name) {
            return null;
        }

        $AdoptType = $this->om->getRepository('MommyProfilBundle:AdoptType')->findOneBy(array('name' => $name));

        if (null === $AdoptType) {
            throw new TransformationFailedException(sprintf(
                'A AdoptType with name "%s" does not exist!',
                $name
            ));
        }
        return $AdoptType;
    }
}