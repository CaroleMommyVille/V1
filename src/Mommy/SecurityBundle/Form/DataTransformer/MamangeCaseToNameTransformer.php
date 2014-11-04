<?php

namespace Mommy\SecurityBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Doctrine\Common\Persistence\ObjectManager;
use Mommy\ProfilBundle\Entity\MamangeCase;

class MamangeCaseToNameTransformer implements DataTransformerInterface
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
     * @param  MamangeCase|null $case
     * @return string
     */
    public function transform($case) {
        if (null === $case) {
            return "";
        }
        return $case->getName();
    }

    /**
     * Transforms a string (name) to an object (case).
     *
     * @param  string $name
     * @return MamangeCase|null
     * @throws TransformationFailedException if object (case) is not found.
     */
    public function reverseTransform($name)
    {
        if (!$name) {
            return null;
        }

        $case = $this->om->getRepository('MommyProfilBundle:MamangeCase')->findOneBy(array('name' => $name));

        if (null === $case) {
            throw new TransformationFailedException(sprintf(
                'A case with name "%s" does not exist!',
                $name
            ));
        }
        return $case;
    }
}