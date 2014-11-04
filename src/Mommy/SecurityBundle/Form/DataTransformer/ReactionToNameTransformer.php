<?php

namespace Mommy\SecurityBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Doctrine\Common\Persistence\ObjectManager;
use Mommy\ProfilBundle\Entity\Reaction;

class ReactionToNameTransformer implements DataTransformerInterface
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
     * @param  Reaction|null $reaction
     * @return string
     */
    public function transform($reaction) {
        if (null === $reaction) return "";
        return $reaction->getName();
    }

    /**
     * Transforms a string (name) to an object (reaction).
     *
     * @param  string $name
     * @return Reaction|null
     * @throws TransformationFailedException if object (reaction) is not found.
     */
    public function reverseTransform($name)
    {
        if (!$name) return null;

        $reaction = $this->om->getRepository('MommyProfilBundle:Reaction')->findOneByName($name);

        if (null === $reaction) {
            throw new TransformationFailedException(sprintf(
                'A reaction with name "%s" does not exist!',
                $name
            ));
        }
        return $reaction;
    }
}