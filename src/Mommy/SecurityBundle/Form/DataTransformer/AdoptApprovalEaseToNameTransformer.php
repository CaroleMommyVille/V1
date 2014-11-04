<?php

namespace Mommy\SecurityBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Doctrine\Common\Persistence\ObjectManager;
use Mommy\ProfilBundle\Entity\AdoptApprovalEase;

class AdoptApprovalEaseToNameTransformer implements DataTransformerInterface
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
     * @param  AdoptApprovalEase|null $ease
     * @return string
     */
    public function transform($ease) {
        if (null === $ease) {
            return "";
        }
        return $ease->getName();
    }

    /**
     * Transforms a string (name) to an object (ease).
     *
     * @param  string $name
     * @return AdoptApprovalEase|null
     * @throws TransformationFailedException if object (ease) is not found.
     */
    public function reverseTransform($name)
    {
        if (!$name) {
            return null;
        }

        $ease = $this->om->getRepository('MommyProfilBundle:AdoptApprovalEase')->findOneBy(array('name' => $name));

        if (null === $ease) {
            throw new TransformationFailedException(sprintf(
                'A ease with name "%s" does not exist!',
                $name
            ));
        }
        return $ease;
    }
}