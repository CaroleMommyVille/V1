<?php

namespace Mommy\SecurityBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Doctrine\Common\Persistence\ObjectManager;
use Mommy\ProfilBundle\Entity\Diploma;

class DiplomaToNameTransformer implements DataTransformerInterface
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
     * @param  Diploma|null $diploma
     * @return string
     */
    public function transform($diploma) {
        if (null === $diploma) {
            return "";
        }
        return $diploma->getName();
    }

    /**
     * Transforms a string (name) to an object (diploma).
     *
     * @param  string $name
     * @return Diploma|null
     * @throws TransformationFailedException if object (diploma) is not found.
     */
    public function reverseTransform($name)
    {
        if (!$name) {
            return null;
        }

        $diploma = $this->om->getRepository('MommyProfilBundle:Diploma')->findOneBy(array('name' => $name));

        if (null === $diploma) {
            throw new TransformationFailedException(sprintf(
                'A diploma with name "%s" does not exist!',
                $name
            ));
        }
        return $diploma;
    }
}