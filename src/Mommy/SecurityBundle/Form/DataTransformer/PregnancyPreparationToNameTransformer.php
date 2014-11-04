<?php

namespace Mommy\SecurityBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Doctrine\Common\Persistence\ObjectManager;
use Mommy\ProfilBundle\Entity\PregnancyPreparation;

class PregnancyPreparationToNameTransformer implements DataTransformerInterface
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
     * @param  PregnancyPreparation|null $preparation
     * @return string
     */
    public function transform($preparation) {
        if (null === $preparation) {
            return "";
        }
        return $preparation->getName();
    }

    /**
     * Transforms a string (name) to an object (preparation).
     *
     * @param  string $name
     * @return PregnancyPreparation|null
     * @throws TransformationFailedException if object (preparation) is not found.
     */
    public function reverseTransform($name)
    {
        if (!$name) {
            return null;
        }

        $preparation = $this->om->getRepository('MommyProfilBundle:PregnancyPreparation')->findOneBy(array('name' => $name));

        if (null === $preparation) {
            throw new TransformationFailedException(sprintf(
                'A preparation with name "%s" does not exist!',
                $name
            ));
        }
        return $preparation;
    }
}