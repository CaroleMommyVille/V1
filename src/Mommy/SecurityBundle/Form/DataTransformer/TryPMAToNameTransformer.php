<?php

namespace Mommy\SecurityBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Doctrine\Common\Persistence\ObjectManager;
use Mommy\ProfilBundle\Entity\TryPMA;

class TryPMAToNameTransformer implements DataTransformerInterface
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
     * @param  TryPMA|null $try
     * @return string
     */
    public function transform($try) {
        if (null === $try) {
            return "";
        }
        return $try->getName();
    }

    /**
     * Transforms a string (name) to an object (try).
     *
     * @param  string $name
     * @return TryPMA|null
     * @throws TransformationFailedException if object (try) is not found.
     */
    public function reverseTransform($name)
    {
        if (!$name) {
            return null;
        }

        $try = $this->om->getRepository('MommyProfilBundle:TryPMA')->findOneBy(array('name' => $name));

        if (null === $try) {
            throw new TransformationFailedException(sprintf(
                'A try with name "%s" does not exist!',
                $name
            ));
        }
        return $try;
    }
}