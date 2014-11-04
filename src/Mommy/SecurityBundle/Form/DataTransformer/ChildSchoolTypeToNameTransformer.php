<?php

namespace Mommy\SecurityBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Doctrine\Common\Persistence\ObjectManager;
use Mommy\ProfilBundle\Entity\ChildSchoolType;

class ChildSchoolTypeToNameTransformer implements DataTransformerInterface
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
     * @param  ChildSchoolType|null $type
     * @return string
     */
    public function transform($type) {
        if (null === $type) {
            return "";
        }
        return $type->getName();
    }

    /**
     * Transforms a string (name) to an object (type).
     *
     * @param  string $name
     * @return ChildSchoolType|null
     * @throws TransformationFailedException if object (type) is not found.
     */
    public function reverseTransform($name)
    {
        if (!$name) {
            return null;
        }

        $type = $this->om->getRepository('MommyProfilBundle:ChildSchoolType')->findOneBy(array('name' => $name));

        if (null === $type) {
            throw new TransformationFailedException(sprintf(
                'A type with name "%s" does not exist!',
                $name
            ));
        }
        return $type;
    }
}