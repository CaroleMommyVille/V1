<?php

namespace Mommy\SecurityBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Doctrine\Common\Persistence\ObjectManager;

class BooleanToNameTransformer implements DataTransformerInterface
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
     * @param  boolean|null $bool
     * @return string
     */
    public function transform($boolean) {
        if ($boolean === false) return "non";
        if ($boolean === true) return 'oui';
        return '';
    }

    /**
     * Transforms a string (name) to an object (bool).
     *
     * @param  string $name
     * @return boolean|null
     * @throws TransformationFailedException if object (bool) is not found.
     */
    public function reverseTransform($bool)
    {
        if ($bool == 'oui') return true;
        return false;
    }
}