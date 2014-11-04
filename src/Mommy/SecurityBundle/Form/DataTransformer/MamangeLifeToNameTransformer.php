<?php

namespace Mommy\SecurityBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Doctrine\Common\Persistence\ObjectManager;
use Mommy\ProfilBundle\Entity\MamangeLife;

class MamangeLifeToNameTransformer implements DataTransformerInterface
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
     * @param  MamangeLife|null $life
     * @return string
     */
    public function transform($life) {
        if (null === $life) {
            return "";
        }
        return $life->getName();
    }

    /**
     * Transforms a string (name) to an object (life).
     *
     * @param  string $name
     * @return MamangeLife|null
     * @throws TransformationFailedException if object (life) is not found.
     */
    public function reverseTransform($name)
    {
        if (!$name) {
            return null;
        }

        $life = $this->om->getRepository('MommyProfilBundle:MamangeLife')->findOneBy(array('name' => $name));

        if (null === $life) {
            throw new TransformationFailedException(sprintf(
                'A life with name "%s" does not exist!',
                $name
            ));
        }
        return $life;
    }
}