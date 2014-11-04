<?php

namespace Mommy\SecurityBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Doctrine\Common\Persistence\ObjectManager;
use Mommy\ProfilBundle\Entity\DeliveryMethod;

class DeliveryMethodToNameTransformer implements DataTransformerInterface
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
     * @param  DeliveryMethod|null $method
     * @return string
     */
    public function transform($method) {
        if (null === $method) {
            return "";
        }
        return $method->getName();
    }

    /**
     * Transforms a string (name) to an object (method).
     *
     * @param  string $name
     * @return DeliveryMethod|null
     * @throws TransformationFailedException if object (method) is not found.
     */
    public function reverseTransform($name)
    {
        if (!$name) {
            return null;
        }

        $method = $this->om->getRepository('MommyProfilBundle:DeliveryMethod')->findOneBy(array('name' => $name));

        if (null === $method) {
            throw new TransformationFailedException(sprintf(
                'A method with name "%s" does not exist!',
                $name
            ));
        }
        return $method;
    }
}