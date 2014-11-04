<?php

namespace Mommy\SecurityBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Doctrine\Common\Persistence\ObjectManager;
use Mommy\ProfilBundle\Entity\MamangeDisease;

class MamangeDiseaseToNameTransformer implements DataTransformerInterface
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
     * @param  MamangeDisease|null $maladie
     * @return string
     */
    public function transform($maladie) {
        if (null === $maladie) {
            return "";
        }
        return $maladie->getName();
    }

    /**
     * Transforms a string (name) to an object (maladie).
     *
     * @param  string $name
     * @return MamangeDisease|null
     * @throws TransformationFailedException if object (maladie) is not found.
     */
    public function reverseTransform($name)
    {
        if (!$name) {
            return null;
        }

        $maladie = $this->om->getRepository('MommyProfilBundle:MamangeDisease')->findOneBy(array('name' => $name));

        if (null === $maladie) {
            throw new TransformationFailedException(sprintf(
                'A maladie with name "%s" does not exist!',
                $name
            ));
        }
        return $maladie;
    }
}