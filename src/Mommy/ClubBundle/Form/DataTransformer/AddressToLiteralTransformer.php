<?php

namespace Mommy\ClubBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Doctrine\Common\Persistence\ObjectManager;
use Mommy\ProfilBundle\Entity\Address;

class AddressToLiteralTransformer implements DataTransformerInterface
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
     * @param  Address|null $address
     * @return string
     */
    public function transform($address) {
        if (null === $address) {
            return "";
        }
        return $address->getLiteral();
    }

    /**
     * Transforms a string (literal) to an object (address).
     *
     * @param  string $literal
     * @return Address|null
     * @throws TransformationFailedException if object (address) is not found.
     */
    public function reverseTransform($literal)
    {
        if (!$literal) {
            return null;
        }

        $address = $this->om->getRepository('MommyProfilBundle:Address')->findOneBy(array('literal' => $literal));

        if (null === $address) {
            $address = new Address();
            $address->setLiteral($literal);
            $this->om->persist($address);
            $this->om->flush();
        }
        return $address;
    }
}