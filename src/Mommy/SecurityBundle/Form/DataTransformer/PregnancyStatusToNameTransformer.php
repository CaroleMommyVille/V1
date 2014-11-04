<?php

namespace Mommy\SecurityBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Doctrine\Common\Persistence\ObjectManager;
use Mommy\ProfilBundle\Entity\PregnancyStatus;

class PregnancyStatusToNameTransformer implements DataTransformerInterface
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
     * @param  PregnancyStatus|null $status
     * @return string
     */
    public function transform($status) {
        if (null === $status) {
            return "";
        }
        return $status->getName();
    }

    /**
     * Transforms a string (name) to an object (status).
     *
     * @param  string $name
     * @return PregnancyStatus|null
     * @throws TransformationFailedException if object (status) is not found.
     */
    public function reverseTransform($name)
    {
        if (!$name) {
            return null;
        }

        $status = $this->om->getRepository('MommyProfilBundle:PregnancyStatus')->findOneBy(array('name' => $name));

        if (null === $status) {
            throw new TransformationFailedException(sprintf(
                'A status with name "%s" does not exist!',
                $name
            ));
        }
        return $status;
    }
}