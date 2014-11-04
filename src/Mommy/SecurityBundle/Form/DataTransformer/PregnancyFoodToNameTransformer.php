<?php

namespace Mommy\SecurityBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Doctrine\Common\Persistence\ObjectManager;
use Mommy\ProfilBundle\Entity\PregnancyFood;

class PregnancyFoodToNameTransformer implements DataTransformerInterface
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
     * @param  PregnancyFood|null $food
     * @return string
     */
    public function transform($food) {
        if (null === $food) {
            return "";
        }
        return $food->getName();
    }

    /**
     * Transforms a string (name) to an object (food).
     *
     * @param  string $name
     * @return PregnancyFood|null
     * @throws TransformationFailedException if object (food) is not found.
     */
    public function reverseTransform($name)
    {
        if (!$name) {
            return null;
        }

        $food = $this->om->getRepository('MommyProfilBundle:PregnancyFood')->findOneBy(array('name' => $name));

        if (null === $food) {
            throw new TransformationFailedException(sprintf(
                'A food with name "%s" does not exist!',
                $name
            ));
        }
        return $food;
    }
}