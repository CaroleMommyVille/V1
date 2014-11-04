<?php

namespace Mommy\SecurityBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Doctrine\Common\Persistence\ObjectManager;
use Mommy\ProfilBundle\Entity\AdoptAge;

class AdoptAgeToNameTransformer implements DataTransformerInterface
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
     * @param  AdoptAge|null $age
     * @return string
     */
    public function transform($age) {
        if (null === $age) {
            return "";
        }
        return $age->getName();
    }

    /**
     * Transforms a string (name) to an object (age).
     *
     * @param  string $name
     * @return AdoptAge|null
     * @throws TransformationFailedException if object (age) is not found.
     */
    public function reverseTransform($name)
    {
        if (!$name) {
            return null;
        }

        $age = $this->om->getRepository('MommyProfilBundle:AdoptAge')->findOneBy(array('name' => $name));

        if (null === $age) {
            throw new TransformationFailedException(sprintf(
                'A age with name "%s" does not exist!',
                $name
            ));
        }
        return $age;
    }
}