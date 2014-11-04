<?php

namespace Mommy\SecurityBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Doctrine\Common\Persistence\ObjectManager;
use Mommy\ProfilBundle\Entity\JobActivities;

class JobActivitiesToNameTransformer implements DataTransformerInterface
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
     * @param  JobActivities|null $activity
     * @return string
     */
    public function transform($activity) {
        if (null === $activity) {
            return "";
        }
        return $activity->getName();
    }

    /**
     * Transforms a string (name) to an object (activity).
     *
     * @param  string $name
     * @return JobActivities|null
     * @throws TransformationFailedException if object (activity) is not found.
     */
    public function reverseTransform($name)
    {
        if (!$name) {
            return null;
        }

        $activity = $this->om->getRepository('MommyProfilBundle:JobActivities')->findOneBy(array('name' => $name));

        if (null === $activity) {
            throw new TransformationFailedException(sprintf(
                'A activity with name "%s" does not exist!',
                $name
            ));
        }
        return $activity;
    }
}
