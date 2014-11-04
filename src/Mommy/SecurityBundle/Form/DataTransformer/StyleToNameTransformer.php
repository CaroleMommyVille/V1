<?php

namespace Mommy\SecurityBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Doctrine\Common\Persistence\ObjectManager;
use Mommy\ProfilBundle\Entity\Style;

class StyleToNameTransformer implements DataTransformerInterface
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
     * @param  Style|null $style
     * @return string
     */
    public function transform($style) {
        if (null === $style) {
            return "";
        }
        return $style->getName();
    }

    /**
     * Transforms a string (name) to an object (style).
     *
     * @param  string $name
     * @return Style|null
     * @throws TransformationFailedException if object (style) is not found.
     */
    public function reverseTransform($name)
    {
        if (!$name) {
            return null;
        }

        $style = $this->om->getRepository('MommyProfilBundle:Style')->findOneBy(array('name' => $name));

        if (null === $style) {
            throw new TransformationFailedException(sprintf(
                'A style with name "%s" does not exist!',
                $name
            ));
        }
        return $style;
    }
}