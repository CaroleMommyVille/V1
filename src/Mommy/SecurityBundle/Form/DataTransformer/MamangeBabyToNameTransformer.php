<?php

namespace Mommy\SecurityBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Doctrine\Common\Persistence\ObjectManager;
use Mommy\ProfilBundle\Entity\MamangeBaby;

class MamangeBabyToNameTransformer implements DataTransformerInterface
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
     * @param  MamangeBaby|null $baby
     * @return string
     */
    public function transform($baby) {
        if (null === $baby) {
            return "";
        }
        return $baby->getName();
    }

    /**
     * Transforms a string (name) to an object (baby).
     *
     * @param  string $name
     * @return MamangeBaby|null
     * @throws TransformationFailedException if object (baby) is not found.
     */
    public function reverseTransform($name)
    {
        if (!$name) {
            return null;
        }

        $baby = $this->om->getRepository('MommyProfilBundle:MamangeBaby')->findOneBy(array('name' => $name));

        if (null === $baby) {
            throw new TransformationFailedException(sprintf(
                'A baby with name "%s" does not exist!',
                $name
            ));
        }
        return $baby;
    }
}