<?php

namespace Mommy\SecurityBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Doctrine\Common\Persistence\ObjectManager;
use Mommy\ProfilBundle\Entity\AdoptApproval;

class AdoptApprovalToNameTransformer implements DataTransformerInterface
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
     * @param  AdoptApproval|null $approval
     * @return string
     */
    public function transform($approval) {
        if (null === $approval) return "";
        return $approval->getName();
    }

    /**
     * Transforms a string (name) to an object (approval).
     *
     * @param  string $name
     * @return AdoptApproval|null
     * @throws TransformationFailedException if object (approval) is not found.
     */
    public function reverseTransform($name)
    {
        if (!$name) return null;

        $approval = $this->om->getRepository('MommyProfilBundle:AdoptApproval')->findOneByName($name);

        if (null === $approval) {
            throw new TransformationFailedException(sprintf(
                'An approval with name "%s" does not exist!',
                $name
            ));
        }
        return $approval;
    }
}