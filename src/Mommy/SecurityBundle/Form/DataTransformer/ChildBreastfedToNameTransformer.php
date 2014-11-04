<?php

namespace Mommy\SecurityBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Doctrine\Common\Persistence\ObjectManager;
use Mommy\ProfilBundle\Entity\ChildBreastfed;

class ChildBreastfedToNameTransformer implements DataTransformerInterface
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
     * @param  ChildBreastfed|null $breastfed
     * @return string
     */
    public function transform($breastfed) {
        if (null === $breastfed) {
            return "";
        }
        return $breastfed->getName();
    }

    /**
     * Transforms a string (name) to an object (breastfed).
     *
     * @param  string $name
     * @return ChildBreastfed|null
     * @throws TransformationFailedException if object (breastfed) is not found.
     */
    public function reverseTransform($name)
    {
        if (!$name) {
            return null;
        }

        $breastfed = $this->om->getRepository('MommyProfilBundle:ChildBreastfed')->findOneBy(array('name' => $name));

        if (null === $breastfed) {
            throw new TransformationFailedException(sprintf(
                'A breastfed with name "%s" does not exist!',
                $name
            ));
        }
        return $breastfed;
    }
}