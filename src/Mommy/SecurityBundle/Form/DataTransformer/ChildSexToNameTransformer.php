<?php

namespace Mommy\SecurityBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Doctrine\Common\Persistence\ObjectManager;
use Mommy\ProfilBundle\Entity\ChildSex;

class ChildSexToNameTransformer implements DataTransformerInterface
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
     * @param  ChildSex|null $sex
     * @return string
     */
    public function transform($sex) {
        if (null === $sex) {
            return "";
        }
        return $sex->getName();
    }

    /**
     * Transforms a string (name) to an object (sex).
     *
     * @param  string $name
     * @return ChildSex|null
     * @throws TransformationFailedException if object (sex) is not found.
     */
    public function reverseTransform($name)
    {
        if (!$name) {
            return null;
        }

        $sex = $this->om->getRepository('MommyProfilBundle:ChildSex')->findOneBy(array('name' => $name));

        if (null === $sex) {
            throw new TransformationFailedException(sprintf(
                'A sex with name "%s" does not exist!',
                $name
            ));
        }
        return $sex;
    }
}