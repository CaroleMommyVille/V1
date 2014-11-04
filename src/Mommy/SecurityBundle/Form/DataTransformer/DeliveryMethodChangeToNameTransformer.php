<?php

namespace Mommy\SecurityBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Doctrine\Common\Persistence\ObjectManager;
use Mommy\ProfilBundle\Entity\DeliveryMethodChange;

class DeliveryMethodChangeToNameTransformer implements DataTransformerInterface
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
     * @param  DeliveryMethodChange|null $change
     * @return string
     */
    public function transform($change) {
        if (null === $change) {
            return "";
        }
        return $change->getName();
    }

    /**
     * Transforms a string (name) to an object (change).
     *
     * @param  string $name
     * @return DeliveryMethodChange|null
     * @throws TransformationFailedException if object (change) is not found.
     */
    public function reverseTransform($name)
    {
        if (!$name) {
            return null;
        }

        $change = $this->om->getRepository('MommyProfilBundle:DeliveryMethodChange')->findOneBy(array('name' => $name));

        if (null === $change) {
            throw new TransformationFailedException(sprintf(
                'A change with name "%s" does not exist!',
                $name
            ));
        }
        return $change;
    }
}