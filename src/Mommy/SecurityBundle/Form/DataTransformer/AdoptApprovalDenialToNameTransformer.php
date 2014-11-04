<?php

namespace Mommy\SecurityBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Doctrine\Common\Persistence\ObjectManager;
use Mommy\ProfilBundle\Entity\AdoptApprovalDenial;

class AdoptApprovalDenialToNameTransformer implements DataTransformerInterface
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
     * @param  AdoptApprovalDenial|null $denial
     * @return string
     */
    public function transform($denial) {
        if (null === $denial) {
            return "";
        }
        return $denial->getName();
    }

    /**
     * Transforms a string (name) to an object (denial).
     *
     * @param  string $name
     * @return AdoptApprovalDenial|null
     * @throws TransformationFailedException if object (denial) is not found.
     */
    public function reverseTransform($name)
    {
        if (!$name) {
            return null;
        }

        $denial = $this->om->getRepository('MommyProfilBundle:AdoptApprovalDenial')->findOneBy(array('name' => $name));

        if (null === $denial) {
            throw new TransformationFailedException(sprintf(
                'A denial with name "%s" does not exist!',
                $name
            ));
        }
        return $denial;
    }
}