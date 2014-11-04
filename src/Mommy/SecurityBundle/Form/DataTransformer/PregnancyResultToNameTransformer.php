<?php

namespace Mommy\SecurityBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Doctrine\Common\Persistence\ObjectManager;
use Mommy\ProfilBundle\Entity\PregnancyResult;

class PregnancyResultToNameTransformer implements DataTransformerInterface
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
     * @param  PregnancyResult|null $result
     * @return string
     */
    public function transform($result) {
        if (null === $result) {
            return "";
        }
        return $result->getName();
    }

    /**
     * Transforms a string (name) to an object (result).
     *
     * @param  string $name
     * @return PregnancyResult|null
     * @throws TransformationFailedException if object (result) is not found.
     */
    public function reverseTransform($name)
    {
        if (!$name) {
            return null;
        }

        $result = $this->om->getRepository('MommyProfilBundle:PregnancyResult')->findOneBy(array('name' => $name));

        if (null === $result) {
            throw new TransformationFailedException(sprintf(
                'A result with name "%s" does not exist!',
                $name
            ));
        }
        return $result;
    }
}