<?php

namespace Mommy\SecurityBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Doctrine\Common\Persistence\ObjectManager;
use Mommy\ProfilBundle\Entity\FamilySize;

class FamilySizeToNameTransformer implements DataTransformerInterface
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
     * @param  FamilySize|null $size
     * @return string
     */
    public function transform($size) {
        if (null === $size) {
            return "";
        }
        return $size->getName();
    }

    /**
     * Transforms a string (name) to an object (size).
     *
     * @param  string $name
     * @return FamilySize|null
     * @throws TransformationFailedException if object (size) is not found.
     */
    public function reverseTransform($name)
    {
        if (!$name) {
            return null;
        }

        $size = $this->om->getRepository('MommyProfilBundle:FamilySize')->findOneBy(array('name' => $name));

        if (null === $size) {
            throw new TransformationFailedException(sprintf(
                'A size with name "%s" does not exist!',
                $name
            ));
        }
        return $size;
    }
}