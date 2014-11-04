<?php

namespace Mommy\SecurityBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Doctrine\Common\Persistence\ObjectManager;
use Mommy\ProfilBundle\Entity\Maternity;

class MaternityToNameTransformer implements DataTransformerInterface
{
    /**
     * @var ObjectManager
     */
    private $om;

    /**
     * @param ObjectManager $om
     */
    public function __construct(ObjectManager $om) {
        $this->om =& $om;
    }

    /**
     * @param  Maternity|null $maternity
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
     * @return Maternity|null
     * @throws TransformationFailedException if object (maternity) is not found.
     */
    public function reverseTransform($name)
    {
        if (!$name) {
            return null;
        }

        $maternity = $this->om->getRepository('MommyProfilBundle:Maternity')->findOneBy(array('name' => $name));

        if (null === $maternity) {
            throw new TransformationFailedException(sprintf(
                'A maternity with name "%s" does not exist!',
                $name
            ));
        }
        return $maternity;
    }
}