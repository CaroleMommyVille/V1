<?php

namespace Mommy\SecurityBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Doctrine\Common\Persistence\ObjectManager;
use Mommy\ProfilBundle\Entity\PregnancySo;

class PregnancySoToNameTransformer implements DataTransformerInterface
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
     * @param  PregnancySo|null $so
     * @return string
     */
    public function transform($so) {
        if (null === $so) {
            return "";
        }
        return $so->getName();
    }

    /**
     * Transforms a string (name) to an object (so).
     *
     * @param  string $name
     * @return PregnancySo|null
     * @throws TransformationFailedException if object (so) is not found.
     */
    public function reverseTransform($name)
    {
        if (!$name) {
            return null;
        }

        $so = $this->om->getRepository('MommyProfilBundle:PregnancySo')->findOneBy(array('name' => $name));

        if (null === $so) {
            throw new TransformationFailedException(sprintf(
                'A so with name "%s" does not exist!',
                $name
            ));
        }
        return $so;
    }
}