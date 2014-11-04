<?php

namespace Mommy\SecurityBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Doctrine\Common\Persistence\ObjectManager;
use Mommy\ProfilBundle\Entity\VIP;

class VIPToNameTransformer implements DataTransformerInterface
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
     * @param  VIP|null $vip
     * @return string
     */
    public function transform($vip) {
        if (null === $vip) {
            return "";
        }
        return $vip->getName();
    }

    /**
     * Transforms a string (name) to an object (vip).
     *
     * @param  string $name
     * @return VIP|null
     * @throws TransformationFailedException if object (vip) is not found.
     */
    public function reverseTransform($name)
    {
        if (!$name) {
            return null;
        }

        $vip = $this->om->getRepository('MommyProfilBundle:VIP')->findOneBy(array('name' => $name));

        if (null === $vip) {
            throw new TransformationFailedException(sprintf(
                'A vip with name "%s" does not exist!',
                $name
            ));
        }
        return $vip;
    }
}