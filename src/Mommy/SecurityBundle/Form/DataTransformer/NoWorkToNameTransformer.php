<?php

namespace Mommy\SecurityBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Doctrine\Common\Persistence\ObjectManager;
use Mommy\ProfilBundle\Entity\NoWork;

class NoWorkToNameTransformer implements DataTransformerInterface
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
     * @param  NoWork|null $nowork
     * @return string
     */
    public function transform($nowork) {
        if (null === $nowork) {
            return "";
        }
        return $nowork->getName();
    }

    /**
     * Transforms a string (name) to an object (nowork).
     *
     * @param  string $name
     * @return NoWork|null
     * @throws TransformationFailedException if object (nowork) is not found.
     */
    public function reverseTransform($name)
    {
        if (!$name) {
            return null;
        }

        $nowork = $this->om->getRepository('MommyProfilBundle:NoWork')->findOneBy(array('name' => $name));

        if (null === $nowork) {
            throw new TransformationFailedException(sprintf(
                'A nowork with name "%s" does not exist!',
                $name
            ));
        }
        return $nowork;
    }
}