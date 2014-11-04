<?php

namespace Mommy\SecurityBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Doctrine\Common\Persistence\ObjectManager;
use Mommy\ProfilBundle\Entity\Marriage;

class MarriageToNameTransformer implements DataTransformerInterface
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
     * @param  Marriage|null $marriage
     * @return string
     */
    public function transform($marriage) {
        if (null === $marriage) {
            return "";
        }
        return $marriage->getName();
    }

    /**
     * Transforms a string (name) to an object (marriage).
     *
     * @param  string $name
     * @return Marriage|null
     * @throws TransformationFailedException if object (marriage) is not found.
     */
    public function reverseTransform($name)
    {
        if (!$name) {
            return null;
        }

        $marriage = $this->om->getRepository('MommyProfilBundle:Marriage')->findOneBy(array('name' => $name));

        if (null === $marriage) {
            throw new TransformationFailedException(sprintf(
                'A marriage with name "%s" does not exist!',
                $name
            ));
        }
        return $marriage;
    }
}