<?php

namespace Mommy\SecurityBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Doctrine\Common\Persistence\ObjectManager;
use Mommy\ProfilBundle\Entity\MamangeIVG;

class MamangeIVGToNameTransformer implements DataTransformerInterface
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
     * @param  MamangeIVG|null $ivg
     * @return string
     */
    public function transform($ivg) {
        if (null === $ivg) {
            return "";
        }
        return $ivg->getName();
    }

    /**
     * Transforms a string (name) to an object (ivg).
     *
     * @param  string $name
     * @return MamangeIVG|null
     * @throws TransformationFailedException if object (ivg) is not found.
     */
    public function reverseTransform($name)
    {
        if (!$name) {
            return null;
        }

        $ivg = $this->om->getRepository('MommyProfilBundle:MamangeIVG')->findOneBy(array('name' => $name));

        if (null === $ivg) {
            throw new TransformationFailedException(sprintf(
                'A ivg with name "%s" does not exist!',
                $name
            ));
        }
        return $ivg;
    }
}