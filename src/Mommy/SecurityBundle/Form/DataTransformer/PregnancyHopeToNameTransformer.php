<?php

namespace Mommy\SecurityBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Doctrine\Common\Persistence\ObjectManager;
use Mommy\ProfilBundle\Entity\PregnancyHope;

class PregnancyHopeToNameTransformer implements DataTransformerInterface
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
     * @param  PregnancyHope|null $hope
     * @return string
     */
    public function transform($hope) {
        if (null === $hope) {
            return "";
        }
        return $hope->getName();
    }

    /**
     * Transforms a string (name) to an object (hope).
     *
     * @param  string $name
     * @return PregnancyHope|null
     * @throws TransformationFailedException if object (hope) is not found.
     */
    public function reverseTransform($name)
    {
        if (!$name) {
            return null;
        }

        $hope = $this->om->getRepository('MommyProfilBundle:PregnancyHope')->findOneBy(array('name' => $name));

        if (null === $hope) {
            throw new TransformationFailedException(sprintf(
                'A hope with name "%s" does not exist!',
                $name
            ));
        }
        return $hope;
    }
}