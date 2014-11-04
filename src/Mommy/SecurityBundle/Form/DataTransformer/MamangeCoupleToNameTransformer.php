<?php

namespace Mommy\SecurityBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Doctrine\Common\Persistence\ObjectManager;
use Mommy\ProfilBundle\Entity\MamangeCouple;

class MamangeCoupleToNameTransformer implements DataTransformerInterface
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
     * @param  MamangeCouple|null $couple
     * @return string
     */
    public function transform($couple) {
        if (null === $couple) {
            return "";
        }
        return $couple->getName();
    }

    /**
     * Transforms a string (name) to an object (couple).
     *
     * @param  string $name
     * @return MamangeCouple|null
     * @throws TransformationFailedException if object (couple) is not found.
     */
    public function reverseTransform($name)
    {
        if (!$name) {
            return null;
        }

        $couple = $this->om->getRepository('MommyProfilBundle:MamangeCouple')->findOneBy(array('name' => $name));

        if (null === $couple) {
            throw new TransformationFailedException(sprintf(
                'A couple with name "%s" does not exist!',
                $name
            ));
        }
        return $couple;
    }
}