<?php

namespace Mommy\SecurityBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Doctrine\Common\Persistence\ObjectManager;
//use Mommy\ProfilBundle\Entity\AdoptCountry;

class AdoptCountryToNameTransformer implements DataTransformerInterface
{
    /**
     * @var ObjectManager
     */
    private $om;

    /**
     * @param ObjectManager $om
     */
    public function __construct(ObjectManager $om) {
        $this->om =& $om;
        $this->om->getConnection()->getConfiguration()->setSQLLogger(null);
    }

    /**
     * @param  AdoptCountry|null $country
     * @return string
     */
    public function transform($country) {
        if (null === $country) {
            return "";
        }
        return $country->getName();
    }

    /**
     * Transforms a string (name) to an object (country).
     *
     * @param  string $name
     * @return AdoptCountry|null
     * @throws TransformationFailedException if object (country) is not found.
     */
    public function reverseTransform($name)
    {
        if (!$name) {
            return null;
        }

        $country = $this->om->getRepository('MommyProfilBundle:AdoptCountry')->findOneBy(array('name' => $name));

        if (null === $country) {
            throw new TransformationFailedException(sprintf(
                'A country with name "%s" does not exist!',
                $name
            ));
        }
        return $country;
    }
}