<?php

namespace Mommy\SecurityBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Doctrine\Common\Persistence\ObjectManager;
use Mommy\ProfilBundle\Entity\ChildNew;

class ChildNewToNameTransformer implements DataTransformerInterface
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
     * @param  ChildNew|null $new
     * @return string
     */
    public function transform($new) {
        if (null === $new) {
            return "";
        }
        return $new->getName();
    }

    /**
     * Transforms a string (name) to an object (new).
     *
     * @param  string $name
     * @return ChildNew|null
     * @throws TransformationFailedException if object (new) is not found.
     */
    public function reverseTransform($name)
    {
        if (!$name) {
            return null;
        }

        $new = $this->om->getRepository('MommyProfilBundle:ChildNew')->findOneBy(array('name' => $name));

        if (null === $new) {
            throw new TransformationFailedException(sprintf(
                'A new with name "%s" does not exist!',
                $name
            ));
        }
        return $new;
    }
}