<?php

namespace Mommy\SecurityBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Doctrine\Common\Persistence\ObjectManager;
use Mommy\ProfilBundle\Entity\MaternityFound;

class MaternityFoundToNameTransformer implements DataTransformerInterface
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
     * @param  MaternityFound|null $maternity
     * @return string
     */
    public function transform($maternity) {
        if (null === $maternity) {
            return "";
        }
        return $maternity->getName();
    }

    /**
     * Transforms a string (name) to an object (maternity).
     *
     * @param  string $name
     * @return MaternityFound|null
     * @throws TransformationFailedException if object (maternity) is not found.
     */
    public function reverseTransform($name)
    {
        if (!$name) {
            return null;
        }

        $maternity = $this->om->getRepository('MommyProfilBundle:MaternityFound')->findOneBy(array('name' => $name));

        if (null === $maternity) {
            throw new TransformationFailedException(sprintf(
                'A maternity with name "%s" does not exist!',
                $name
            ));
        }
        return $maternity;
    }
}