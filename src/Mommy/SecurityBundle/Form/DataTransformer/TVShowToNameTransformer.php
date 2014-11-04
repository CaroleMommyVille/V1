<?php

namespace Mommy\SecurityBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Doctrine\Common\Persistence\ObjectManager;
use Mommy\ProfilBundle\Entity\TVShow;

class TVShowToNameTransformer implements DataTransformerInterface
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
     * @param  TVShow|null $tvshow
     * @return string
     */
    public function transform($tvshow) {
        if (null === $tvshow) {
            return "";
        }
        return $tvshow->getName();
    }

    /**
     * Transforms a string (name) to an object (tvshow).
     *
     * @param  string $name
     * @return TVShow|null
     * @throws TransformationFailedException if object (tvshow) is not found.
     */
    public function reverseTransform($name)
    {
        if (!$name) {
            return null;
        }

        $tvshow = $this->om->getRepository('MommyProfilBundle:TVShow')->findOneBy(array('name' => $name));

        if (null === $tvshow) {
            throw new TransformationFailedException(sprintf(
                'A tvshow with name "%s" does not exist!',
                $name
            ));
        }
        return $tvshow;
    }
}