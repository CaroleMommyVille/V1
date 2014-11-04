<?php

namespace Mommy\SecurityBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Doctrine\Common\Persistence\ObjectManager;
use Mommy\ProfilBundle\Entity\ChildDiseaseHeart;

class ChildDiseaseHeartToNameTransformer implements DataTransformerInterface
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
     * @param  ChildDiseaseHeart|null $disease
     * @return string
     */
    public function transform($disease) {
        if (null === $disease) {
            return "";
        }
        return $disease->getName();
    }

    /**
     * Transforms a string (name) to an object (disease).
     *
     * @param  string $name
     * @return ChildDiseaseHeart|null
     * @throws TransformationFailedException if object (disease) is not found.
     */
    public function reverseTransform($name)
    {
        if (!$name) {
            return null;
        }

        $disease = $this->om->getRepository('MommyProfilBundle:ChildDiseaseHeart')->findOneBy(array('name' => $name));

        if (null === $disease) {
            throw new TransformationFailedException(sprintf(
                'A disease with name "%s" does not exist!',
                $name
            ));
        }
        return $disease;
    }
}