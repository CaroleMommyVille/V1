<?php

namespace Mommy\SecurityBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Doctrine\Common\Persistence\ObjectManager;
use Mommy\ProfilBundle\Entity\MamangeIMG;

class MamangeIMGToNameTransformer implements DataTransformerInterface
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
     * @param  MamangeIMG|null $img
     * @return string
     */
    public function transform($img) {
        if (null === $img) {
            return "";
        }
        return $img->getName();
    }

    /**
     * Transforms a string (name) to an object (img).
     *
     * @param  string $name
     * @return MamangeIMG|null
     * @throws TransformationFailedException if object (img) is not found.
     */
    public function reverseTransform($name)
    {
        if (!$name) {
            return null;
        }

        $img = $this->om->getRepository('MommyProfilBundle:MamangeIMG')->findOneBy(array('name' => $name));

        if (null === $img) {
            throw new TransformationFailedException(sprintf(
                'A img with name "%s" does not exist!',
                $name
            ));
        }
        return $img;
    }
}