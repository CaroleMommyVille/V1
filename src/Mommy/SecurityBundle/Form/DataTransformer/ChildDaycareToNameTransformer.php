<?php

namespace Mommy\SecurityBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Doctrine\Common\Persistence\ObjectManager;
use Mommy\ProfilBundle\Entity\ChildDaycare;

class ChildDaycareToNameTransformer implements DataTransformerInterface
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
     * @param  ChildDaycare|null $daycare
     * @return string
     */
    public function transform($daycare) {
        if (null === $daycare) {
            return "";
        }
        return $daycare->getName();
    }

    /**
     * Transforms a string (name) to an object (daycare).
     *
     * @param  string $name
     * @return ChildDaycare|null
     * @throws TransformationFailedException if object (daycare) is not found.
     */
    public function reverseTransform($name)
    {
        if (!$name) {
            return null;
        }

        $daycare = $this->om->getRepository('MommyProfilBundle:ChildDaycare')->findOneBy(array('name' => $name));

        if (null === $daycare) {
            throw new TransformationFailedException(sprintf(
                'A daycare with name "%s" does not exist!',
                $name
            ));
        }
        return $daycare;
    }
}