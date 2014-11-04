<?php

namespace Mommy\SecurityBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Doctrine\Common\Persistence\ObjectManager;
use Mommy\ProfilBundle\Entity\AfterDelivery;

class AfterDeliveryToNameTransformer implements DataTransformerInterface
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
     * @param  AfterDelivery|null $after
     * @return string
     */
    public function transform($after) {
        if (null === $after) {
            return "";
        }
        return $after->getName();
    }

    /**
     * Transforms a string (name) to an object (after).
     *
     * @param  string $name
     * @return AfterDelivery|null
     * @throws TransformationFailedException if object (after) is not found.
     */
    public function reverseTransform($name)
    {
        if (!$name) {
            return null;
        }

        $after = $this->om->getRepository('MommyProfilBundle:AfterDelivery')->findOneBy(array('name' => $name));

        if (null === $after) {
            throw new TransformationFailedException(sprintf(
                'A after with name "%s" does not exist!',
                $name
            ));
        }
        return $after;
    }
}