<?php

namespace Mommy\ProfilBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\File\Exception\UploadException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Oneup\UploaderBundle\Controller\UploaderController;
use Oneup\UploaderBundle\Uploader\Response\EmptyResponse;

// Form
use Symfony\Component\Form\Forms;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\HttpFoundation\HttpFoundationExtension;
use Symfony\Component\HttpKernel\Exception\HttpNotFoundException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

// User
use Mommy\SecurityBundle\Entity\User;

// Geo
use Ivory\GoogleMap\Base\Coordinate;
use Ivory\GoogleMap\Services\DistanceMatrix\DistanceMatrixRequest;
use Ivory\GoogleMap\Services\Base\TravelMode;
use Ivory\GoogleMap\Services\Base\UnitSystem;
use Ivory\GoogleMap\Services\DistanceMatrix\DistanceMatrix;
use Widop\HttpAdapter\CurlHttpAdapter;

// Entities
use Mommy\ProfilBundle\Entity\VIP;
use Mommy\ProfilBundle\Entity\Music;
use Mommy\ProfilBundle\Entity\TVShow;
use Mommy\ProfilBundle\Entity\Movie;
use Mommy\ProfilBundle\Entity\Activity;
use Mommy\ProfilBundle\Entity\Carousel;
use Mommy\ProfilBundle\Entity\CarouselPhoto;
use Mommy\ProfilBundle\Entity\Friend;
use Mommy\ProfilBundle\Entity\Network;

use Mommy\UIBundle\Controller\DefaultController as MommyUIBundle;

class CompatibleController extends Controller
{
	private $criteria = 0;
	private $score = 0;

	private function getScore() {
		return round(($this->score / $this->criteria)*100);
	}

	/**
	 * @Route("/compatible/toutes", name="profil-compatible-all.json")
	 */
	public function compatibleAllAction() {
		MommyUIBundle::logStatistics($this->get('request'));
		$request = $this->get('request');
		$session = $request->getSession();

		if (($user = $this->getDoctrine()->getRepository('MommySecurityBundle:User')->find($session->get('_user')->getId())) === null)
			return new Response('Vous ne pouvez éditer cet utilisateur', Response::HTTP_FORBIDDEN);

		$score = array();

	    $type = $this->getDoctrine()->getRepository('MommyProfilBundle:UserType')->findOneByUser($user);
	    foreach ($this->getDoctrine()->getRepository('MommyProfilBundle:UserType')->findByType($type->getType()) as $ut) {
			if ($ut == $type) continue;
			$woman = $ut->getUser();
		    switch ($type->getType()->getName()) {
		      case 'type-presquenceinte':
		        $this->getPresquEnceinte($user, $woman);
		        break;
		      case 'type-enceinte':
		        $this->getEnceinte($user, $woman);
		        break;
		      case 'type-maman':
		        $this->getMaman($user, $woman);
		        break;
		      case 'type-pma':
		        $this->getPma($user, $woman);
		        break;
		      case 'type-mamange':
		        $this->getMamange($user, $woman);
		        break;
		      case 'type-adoptante':
		        $this->getAdoptante($user, $woman);
		        break;
		    }
		    $this->getGeneric($user, $woman);
		    $score[] = array('woman' => $woman, 'score' => $this->getScore());
		    $this->score = 0;
		    $this->criteria = 0;
		}

		return new JsonResponse($score);
	}

	/**
	 * @Route("/compatible/{uid}", name="profil-compatible.json", requirements={"uid"=".+"})
	 */
	public function compatibleAction($uid) {
		MommyUIBundle::logStatistics($this->get('request'));
		$request = $this->get('request');
		$session = $request->getSession();

		if (($user = $this->getDoctrine()->getRepository('MommySecurityBundle:User')->find($session->get('_user')->getId())) === null)
			return new Response('Vous ne pouvez éditer cet utilisateur', Response::HTTP_FORBIDDEN);
		if (($woman = $this->getDoctrine()->getRepository('MommySecurityBundle:User')->find($uid)) === null)
			return new Response('Vous ne pouvez éditer cet utilisateur', Response::HTTP_FORBIDDEN);

	    $type = $this->getDoctrine()->getRepository('MommyProfilBundle:UserType')->findOneByUser($user);
	    switch ($type->getType()->getName()) {
	      case 'type-presquenceinte':
	        $this->getPresquEnceinte($user, $woman);
	        break;
	      case 'type-enceinte':
	        $this->getEnceinte($user, $woman);
	        break;
	      case 'type-maman':
	        $this->getMaman($user, $woman);
	        break;
	      case 'type-pma':
	        $this->getPma($user, $woman);
	        break;
	      case 'type-mamange':
	        $this->getMamange($user, $woman);
	        break;
	      case 'type-adoptante':
	        $this->getAdoptante($user, $woman);
	        break;
	    }
	    $this->getGeneric($user, $woman);

		return new JsonResponse(array('score' => abs($this->getScore())));
	}

	private function isFirstChild(User $user) {
		if (($family = $this->getDoctrine()->getRepository('MommyProfilBundle:Family')->findOneByUser($user)) !== null) {
			if (is_object($family->getSize())) return false;
		}
		return true;
	}

	private function getFamilySize(User $user) {
		if (($family = $this->getDoctrine()->getRepository('MommyProfilBundle:Family')->findOneByUser($user)) === null)
		return $family->getSize();
	}

	private function hasTriple(User $user) {
		if (($family = $this->getDoctrine()->getRepository('MommyProfilBundle:Family')->findOneByUser($user)) !== null) {
			if (is_object($family->getSize())) {
				if ($family->getSize()->getName() == "nbenfants-triples")
					return true;
			}
		}
		return false;
	}

	private function hasTwin(User $user) {
		if (($family = $this->getDoctrine()->getRepository('MommyProfilBundle:Family')->findOneByUser($user)) !== null) {
			if (is_object($family->getSize())) {
				if ($family->getSize()->getName() == "nbenfants-jumeaux")
					return true;
			}
		}
		return false;
	}

	private function whichMaternity(User $user) {
		if (($plan = $this->getDoctrine()->getRepository('MommyProfilBundle:BirthPlan')->findOneByUser($user)) === null)
			return null;
		if (!is_object($plan->getMaternityFound()))
			return null;
		if (!is_object($plan->getMaternity()))
			return null;
		return $plan->getMaternity();
	}

	private function getPregnancyPreparation(User $user) {
		if (($plan = $this->getDoctrine()->getRepository('MommyProfilBundle:BirthPlan')->findOneByUser($user)) === null)
			return array();
		return $plan->getPreparation();
	}

	private function birthInWater(User $user) {
		if (($plan = $this->getDoctrine()->getRepository('MommyProfilBundle:BirthPlan')->findOneByUser($user)) === null)
			return null;
		if (!is_object($plan->getMethod()))
			return null;
		return ($plan->getMethod()->getName() == 'methode-eau');
	}

	private function getBreastfed(User $user) {
		if (($plan = $this->getDoctrine()->getRepository('MommyProfilBundle:BirthPlan')->findOneByUser($user)) === null)
			return null;
		if (!is_object($plan->getBreastfed()))
			return null;
		return $plan->getBreastfed();
	}

	private function hasSpecialFood(User $user) {
		if (($plan = $this->getDoctrine()->getRepository('MommyProfilBundle:BirthPlan')->findOneByUser($user)) === null)
			return false;
		if (!is_object($plan->getFood()))
			return false;
		if ($plan->getFood()->getName() == 'alimentation-vegetarien')
			return true;
		else if ($plan->getFood()->getName() == 'alimentation-sans-gluten')
			return true;
		return false;
	}

	private function isAlmostOk(User $user) {
		if (($almost = $this->getDoctrine()->getRepository('MommyProfilBundle:AlmostPregnant')->findOneByUser($user)) === null)
			return null;
		if (is_null($almost->getSo()))
			return null;
		return $almost->getSo();
	}

	private function isDadOk(User $user) {
		if (($almost = $this->getDoctrine()->getRepository('MommyProfilBundle:AlmostPregnant')->findOneByUser($user)) === null)
			return null;
		if (!is_object($almost->getDad()))
			return null;
		return $almost->getDad();
	}

	private function isMeOk(User $user) {
		if (($almost = $this->getDoctrine()->getRepository('MommyProfilBundle:AlmostPregnant')->findOneByUser($user)) === null)
			return null;
		if (!is_object($almost->getMood()))
			return null;
		return $almost->getMood();
	}

	private function getLanguages(User $user) {
		if (($woman = $this->getDoctrine()->getRepository('MommyProfilBundle:Woman')->findOneByUser($user)) === null)
			return array();
		return $woman->getLanguages();
	}

	private function isWidow(User $user) {
		if (($woman = $this->getDoctrine()->getRepository('MommyProfilBundle:Woman')->findOneByUser($user)) === null)
			return null;
		if (!is_object($woman->getMarriage()))
			return null;
		if ($woman->getMarriage()->getName() == 'Marriage-widow')
			return true;
		return false;
	}

	private function isSingle(User $user) {
		if (($woman = $this->getDoctrine()->getRepository('MommyProfilBundle:Woman')->findOneByUser($user)) === null)
			return null;
		if (!is_object($woman->getMarriage()))
			return null;
		if ($woman->getMarriage()->getName() == 'Marriage-alone')
			return true;
		return false;
	}

	private function isUnknowCouple(User $user) {
		if (($woman = $this->getDoctrine()->getRepository('MommyProfilBundle:Woman')->findOneByUser($user)) === null)
			return null;
		if (!is_object($woman->getMarriage()))
			return null;
		if ($woman->getMarriage()->getName() == 'Marriage-tbd')
			return true;
		return false;
	}

	private function getDistance(User $user, User $woman) {
		if (!is_object($user->getAddress()))
			return null;
		if (!is_object($woman->getAddress()))
			return null;
		$request = new DistanceMatrixRequest();
		$request->setOrigins(array(new Coordinate($user->getAddress()->getLatitude(), $user->getAddress()->getLongitude(), true)));
		$request->setDestinations(array(new Coordinate($woman->getAddress()->getLatitude(), $woman->getAddress()->getLongitude(), true)));
		$request->setRegion('fr');
		$request->setLanguage('fr');
		$request->setTravelMode(TravelMode::DRIVING);
		$request->setUnitSystem(UnitSystem::METRIC);
		$request->setSensor(false);

		$distanceMatrix = new DistanceMatrix(new CurlHttpAdapter());
		$response = $distanceMatrix->process($request);
		foreach ($response as $row) {
			foreach ($row as $element) {
				$distance = $element->getDistance();
				return $distance->getValue();
			}
		}
		return null;
	}

	private function getDeliveryDate(User $user) {
		if (($pregnancy = $this->getDoctrine()->getRepository('MommyProfilBundle:Pregnancy')->findOneByUser($user)) === null)
			return null;
		return $pregnancy->getAmenorrhee();
	}

	private function getDiploma(User $user) {
		if (($woman = $this->getDoctrine()->getRepository('MommyProfilBundle:Woman')->findOneByUser($user)) === null)
			return null;
		if (!is_object($woman->getDiploma()))
			return null;
		return $woman->getDiploma();
	}

	private function getStyles(User $user) {
		if (($woman = $this->getDoctrine()->getRepository('MommyProfilBundle:Woman')->findOneByUser($user)) === null)
			return array();
		return $woman->getStyle();
	}

	private function getSports(User $user) {
		if (($woman = $this->getDoctrine()->getRepository('MommyProfilBundle:Woman')->findOneByUser($user)) === null)
			return array();
		return $woman->getSports();
	}

	private function getActivities(User $user) {
		if (($woman = $this->getDoctrine()->getRepository('MommyProfilBundle:Woman')->findOneByUser($user)) === null)
			return array();
		return $woman->getActivities();
	}

	private function getPathologyBaby(User $user) {
		if (($pregnant = $this->getDoctrine()->getRepository('MommyProfilBundle:Pregnancy')->findOneByUser($user)) === null)
			return array();		
		if (($trim = $this->getDoctrine()->getRepository('MommyProfilBundle:Quarter1')->findOneByPregnancy($pregnant)) === null) {
			if (($trimM = $this->getDoctrine()->getRepository('MommyProfilBundle:Quarter2')->findOneByPregnancy($pregnant)) === null) {
				if (($trimM = $this->getDoctrine()->getRepository('MommyProfilBundle:Quarter3')->findOneByPregnancy($pregnant)) === null) {
					return null;
				}
			}
		}
		return $trim->getPathologyBaby();
	}

	private function getPathologyPregnancy(User $user) {
		if (($pregnant = $this->getDoctrine()->getRepository('MommyProfilBundle:Pregnancy')->findOneByUser($user)) === null)
			return array();		
		if (($trim = $this->getDoctrine()->getRepository('MommyProfilBundle:Quarter1')->findOneByPregnancy($pregnant)) === null) {
			if (($trimM = $this->getDoctrine()->getRepository('MommyProfilBundle:Quarter2')->findOneByPregnancy($pregnant)) === null) {
				if (($trimM = $this->getDoctrine()->getRepository('MommyProfilBundle:Quarter3')->findOneByPregnancy($pregnant)) === null) {
					return null;
				}
			}
		}
		return $trim->getPathologyPregnancy();
	}

	private function getPregnancySymptoms(User $user) {
		if (($pregnant = $this->getDoctrine()->getRepository('MommyProfilBundle:Pregnancy')->findOneByUser($user)) === null)
			return array();		
		if (($trim = $this->getDoctrine()->getRepository('MommyProfilBundle:Quarter1')->findOneByPregnancy($pregnant)) === null) {
			if (($trimM = $this->getDoctrine()->getRepository('MommyProfilBundle:Quarter2')->findOneByPregnancy($pregnant)) === null) {
				if (($trimM = $this->getDoctrine()->getRepository('MommyProfilBundle:Quarter3')->findOneByPregnancy($pregnant)) === null) {
					return null;
				}
			}
		}
		return $trim->getPregnancySymptoms();
	}

	private function isPMAPregnant(User $user) {
		if (($pma = $this->getDoctrine()->getRepository('MommyProfilBundle:PMA')->findOneByUser($user)) === null)
			return null;
		return $pma->getPregnant();
	}

	private function getPMADeliveryDate(User $user) {
		if (($pma = $this->getDoctrine()->getRepository('MommyProfilBundle:PMA')->findOneByUser($user)) === null)
			return null;
		return $pma->getAmenorrhee();
	}

	private function getPathologyGyneco(User $user) {
		if (($pma = $this->getDoctrine()->getRepository('MommyProfilBundle:PMA')->findOneByUser($user)) === null)
			return null;
		return $pma->getPathologyGyneco();
	}

	private function getPMATech(User $user) {
		if (($pma = $this->getDoctrine()->getRepository('MommyProfilBundle:PMA')->findOneByUser($user)) === null)
			return null;
		return $pma->getTechPMA();
	}

	private function getSoftMethod(User $user) {
		if (($pma = $this->getDoctrine()->getRepository('MommyProfilBundle:PMA')->findOneByUser($user)) === null)
			return null;
		return $pma->getSoftMethod();
	}

	private function hasOvulationStimulation(User $user) {
		if (($pma = $this->getDoctrine()->getRepository('MommyProfilBundle:PMA')->findOneByUser($user)) === null)
			return null;
		return (sizeof($pma->getOvulationStimulator()) > 0);
	}

	private function getPma(User $user, User $woman) {
		$this->criteria += 20;
		if ($this->isPMAPregnant($user) === $this->isPMAPregnant($woman))
			$this->score += 20;

		$this->criteria += 19;
		if ($this->getPMADeliveryDate($user) === $this->getPMADeliveryDate($woman))
			$this->score += 19;

		$this->criteria += 18;
		$pbU = $this->getPathologyGyneco($user);
		$pbW = $this->getPathologyGyneco($woman);
		if (is_array($pbU) && is_array($pbW) && sizeof(array_intersect($pbU, $pbW)))
			$this->score += 18*(1-sizeof(array_merge($pbU,$pbW)));

		$this->criteria += 17;
		if ($this->hasOvulationStimulation($user) === $this->hasOvulationStimulation($woman))
			$this->score += 17;

		$this->criteria += 16;
		$techU = $this->getPMATech($user);
		$techW = $this->getPMATech($woman);
		if (is_array($techU) && is_array($techW) && sizeof(array_intersect($techU, $techW)))
			$this->score += 16*(1-sizeof(array_merge($techU,$techW)));

		$this->criteria += 15;
		$softU = $this->getSoftMethod($user);
		$softW = $this->getSoftMethod($woman);
		if (is_array($softU) && is_array($softW) && sizeof(array_intersect($softU, $softW)))
			$this->score += 15*(1-sizeof(array_merge($softU,$softW)));
	}

	private function getIMG(User $user) {
		if (($mamange = $this->getDoctrine()->getRepository('MommyProfilBundle:Mamange')->findOneByUser($user)) === null)
			return null;
		return $mamange->getIMG();
	}

	private function getIVG(User $user) {
		if (($mamange = $this->getDoctrine()->getRepository('MommyProfilBundle:Mamange')->findOneByUser($user)) === null)
			return null;
		return $mamange->getIVG();
	}

	private function getMamangeCase(User $user) {
		if (($mamange = $this->getDoctrine()->getRepository('MommyProfilBundle:Mamange')->findOneByUser($user)) === null)
			return null;
		return $mamange->getCase();
	}

	private function getMamangeDisease(User $user) {
		if (($mamange = $this->getDoctrine()->getRepository('MommyProfilBundle:Mamange')->findOneByUser($user)) === null)
			return null;
		return $mamange->getDisease();
	}

	private function stillCouple(User $user) {
		if (($mamange = $this->getDoctrine()->getRepository('MommyProfilBundle:Mamange')->findOneByUser($user)) === null)
			return null;
		if (!is_object($mamange->getCouple()))
			return null;
		return ($mamange->getCouple()->getName() == 'mamange-couple-3');
	}

	private function makeBaby(User $user) {
		if (($mamange = $this->getDoctrine()->getRepository('MommyProfilBundle:Mamange')->findOneByUser($user)) === null)
			return null;
		if (!is_object($mamange->getBaby()))
			return null;
		return (($mamange->getBaby()->getName() == 'mamange-baby-3') || ($mamange->getBaby()->getName() == 'mamange-baby-4'));
	}

	private function getAdoptante(User $user, User $woman) {
	}

	private function getMamange(User $user, User $woman) {
		$this->criteria += 20;
		if ($this->getIMG($user) && ($this->getIMG($user) === $this->getIMG($woman)))
			$this->score += 20;
		else if ($this->getIVG($user) && ($this->getIVG($user) === $this->getIVG($woman)))
			$this->score += 20;
		else if ($this->getMamangeCase($user) === $this->getMamangeCase($woman))
			$this->score += 20;
		$this->criteria += 19;
		if ($this->getMamangeDisease($user) === $this->getMamangeDisease($woman))
			$this->score += 19;
		$this->criteria += 18;
		if ($this->stillCouple($user) === $this->stillCouple($woman))
			$this->score += 18;
		$this->criteria += 17;
		if ($this->makeBaby($user) === $this->makeBaby($woman))
			$this->score += 17;
	}

	private function getMaman(User $user, User $woman) {
		$this->criteria += 20;
		if ($this->getFamilySize($user) === $this->getFamilySize($woman))
			$this->score += 20;

		$childrenU = $this->getDoctrine()->getRepository('MommyProfilBundle:Child')->findByUser($user);
		$childrenW = $this->getDoctrine()->getRepository('MommyProfilBundle:Child')->findByUser($woman);
		$c = 0;
		$s = 0;
		$this->criteria += 19;
		foreach ($childrenU as $childU) {
			foreach ($childrenW as $childW) {
				$c++;
				if (($childU->getAge() >= $childW->getAge() - 1) && ($childU->getAge() <= $childW->getAge() + 1))
					$s++;
			}
		}
		$this->score += 19*($s / $c);
		$c = 0;
		$s = 0;
		$this->criteria += 18;
		foreach ($childrenU as $childU) {
			foreach ($childrenW as $childW) {
				foreach ($childU->getPathologyBaby() as $pathobbU) {
					$c++;
					foreach ($childW->getPathologyBaby() as $pathobbW) {
						if ($pathobbU === $pathobbW) {
							$s++;
							break 2;
						}
					}
				}
			}
		}
		$this->score += 18*($s / $c);
		$c = 0;
		$s = 0;
		$this->criteria += 17;
		foreach ($childrenU as $childU) {
			foreach ($childrenW as $childW) {
				foreach ($childU->getPathologyPregnancy() as $pathogroU) {
					$c++;
					foreach ($childW->getPathologyPregnancy() as $pathogroW) {
						if ($pathogroU === $pathogroW) {
							$s++;
							break 2;
						}
					}
				}
			}
		}
		$this->score += 17*($s / $c);
		$c = 0;
		$s = 0;
		$this->criteria += 16;
		foreach ($childrenU as $childU) {
			foreach ($childrenW as $childW) {
				foreach ($childU->setChildSport() as $sportU) {
					$c++;
					foreach ($childW->setChildSport() as $sportW) {
						if ($sportU == $sportW) {
							$s++;
							break 2;
						}
					}
				}
			}
		}
		$this->score += 16*($s / $c);
		$c = 0;
		$s = 0;
		$this->criteria += 15;
		foreach ($childrenU as $childU) {
			foreach ($childrenW as $childW) {
				foreach ($childU->getChildHobby() as $hobbyU) {
					$c++;
					foreach ($childW->getChildHobby() as $hobbyW) {
						if ($hobbyU == $hobbyW) {
							$s++;
							break 2;
						}
					}
				}
			}
		}
		$this->score += 15*($s / $c);
	}

	private function getPresquEnceinte(User $user, User $woman) {
		$this->criteria += 20;
		if ($this->isFirstChild($user) && ($this->isFirstChild($user) === $this->isFirstChild($woman)))
			$this->score += 20;
		else if ($this->hasTwin($user) && ($this->hasTwin($user) === $this->hasTwin($woman)))
			$this->score += 20;
		else if ($this->hasTriple($user) && ($this->hasTriple($user) === $this->hasTriple($woman)))
			$this->score += 20;

		$this->criteria += 19;
		if ($this->whichMaternity($user) === $this->whichMaternity($woman))
			$this->score += 19;

		$this->criteria += 18;
		$prepU = $this->getPregnancyPreparation($user);
		$prepW = $this->getPregnancyPreparation($woman);
		if (is_array($prepU) && is_array($prepW) && sizeof(array_intersect($prepU, $prepW)))
			$this->score += 18*(1-sizeof(array_merge($prepW,$prepU)));

		$this->criteria += 17;
		if ($this->birthInWater($user) === $this->birthInWater($woman))
			$this->score += 17;

		$this->criteria += 16;
		if ($this->getBreastfed($user) === $this->getBreastfed($woman))
			$this->score += 16;

		$this->criteria += 15;
		if ($this->hasSpecialFood($user) === $this->hasSpecialFood($woman))
			$this->score += 15;

		$this->criteria += 14;
		if ($this->isAlmostOk($user) === $this->isAlmostOk($woman))
			$this->score += 14;

		$this->criteria += 13;
		if ($this->isDadOk($user) === $this->isDadOk($woman))
			$this->score += 13;

		$this->criteria += 12;
		if ($this->isMeOk($user) === $this->isMeOk($woman))
			$this->score += 12;
	}

	private function getEnceinte(User $user, User $woman) {
		$this->criteria += 20;
		if ($this->isFirstChild($user) === $this->isFirstChild($woman))
			$this->score += 20;
		else if ($this->hasTwin($user) === $this->hasTwin($woman))
			$this->score += 20;
		else if ($this->hasTriple($user) === $this->hasTriple($woman))
			$this->score += 20;

		$this->criteria += 19;
		if ($this->getDeliveryDate($user) === $this->getDeliveryDate($woman))
			$this->score += 19;

		$this->criteria += 18;
		$pathobbU = $this->getPathologyBaby($user);
		$pathobbW = $this->getPathologyBaby($woman);
		if (is_array($pathobbU) && is_array($pathobbW) && sizeof(array_intersect($pathobbU, $pathobbW)))
			$this->score += 18*(1-sizeof(array_merge($pathobbW,$pathobbU)));

		$this->criteria += 17;
		$pathogroU = $this->getPathologyPregnancy($user);
		$pathogroW = $this->getPathologyPregnancy($woman);
		if (is_array($pathogroU) && is_array($pathogroW) && sizeof(array_intersect($pathogroU, $pathogroW)))
			$this->score += 17*(1-sizeof(array_merge($pathogroW,$pathogroU)));

		$this->criteria += 16;
		if ($this->whichMaternity($user) === $this->whichMaternity($woman))
			$this->score += 16;

		$this->criteria += 15;
		$prepU = $this->getPregnancyPreparation($user);
		$prepW = $this->getPregnancyPreparation($woman);
		if (is_array($prepU) && is_array($prepW) && sizeof(array_intersect($prepU, $prepW)))
			$this->score += 15*(1-sizeof(array_merge($prepW,$prepU)));

		$this->criteria += 14;
		if ($this->birthInWater($user) === $this->birthInWater($woman))
			$this->score += 14;

		$this->criteria += 13;
		if ($this->getBreastfed($user) === $this->getBreastfed($woman))
			$this->score += 13;

		$this->criteria += 12;
		if ($this->hasSpecialFood($user) === $this->hasSpecialFood($woman))
			$this->score += 12;

		$this->criteria += 11;
		$symptomsU = $this->getPregnancySymptoms($user);
		$symptomsW = $this->getPregnancySymptoms($woman);
		if (is_array($symptomsU) && is_array($symptomsW) && sizeof(array_intersect($symptomsU, $symptomsW)))
			$this->score += 11*(1-sizeof(array_merge($symptomsU,$symptomsW)));
	}

	private function getGeneric(User $user, User $woman) {
		$this->criteria += 9;
		$langU = $this->getLanguages($user);
		$langW = $this->getLanguages($woman);
		if (is_array($langU) && is_array($langW) && sizeof(array_intersect($langU, $langW)))
			$this->score += 9*(sizeof(array_intersect($langU, $langW)) / 2);

		$this->criteria += 8;
		if (($user->getAge() >= $woman->getAge()-2) && ($user->getAge() <= $woman->getAge()+2))
			$this->score += 8;

		$this->criteria += 7;
		if ($this->isWidow($user) === $this->isWidow($woman))
			$this->score += 7;

		$this->criteria += 6;
		$distance = $this->getDistance($user, $woman);
		if (($distance <= 2000) && ($distance !== null))
			$this->score += 6;

		$this->criteria += 5;
		if ($this->getDiploma($user) === $this->getDiploma($woman))
			$this->score += 5;

		$this->criteria += 4;
		$styleU = $this->getStyles($user);
		$styleW = $this->getStyles($woman);
		if (is_array($styleU) && is_array($styleW) && sizeof(array_intersect($styleU, $styleW)))
			$this->score += 4*(1-sizeof(array_merge($styleU,$styleW)));

		$this->criteria += 3;
		if ($this->isSingle($user) === $this->isSingle($woman))
			$this->score += 3;
		if ($this->isUnknowCouple($user) === $this->isUnknowCouple($woman))
			$this->score += 3;

		$this->criteria += 2;
		$sportsU = $this->getSports($user);
		$sportsW = $this->getSports($woman);
		if (is_array($sportsU) && is_array($sportsW) && sizeof(array_intersect($sportsU, $sportsW)))
			$this->score += 2*(1-sizeof(array_merge($sportsU,$sportsW)));

		$this->criteria += 1;
		$activitiesU = $this->getActivities($user);
		$activitiesW = $this->getActivities($woman);
		if (is_array($activitiesU) && is_array($activitiesW) && sizeof(array_intersect($activitiesU, $activitiesW)))
			$this->score += 1*(1-sizeof(array_merge($activitiesU,$activitiesW)));
	}

  /**
   * @Route("/fiche/{uid}", name="profil-fiche", requirements={"uid"=".+"})
   * @Template
   */
  public function ficheAction($uid) {
    MommyUIBundle::logStatistics($this->get('request'));
    $request = $this->get('request');
    $session = $request->getSession();

    $user = $this->getDoctrine()->getRepository('MommySecurityBundle:User')->findOneBy(array('id' => $uid, 'isActive' => true, 'isLocked' => false), array());

    $woman = $this->getDoctrine()->getRepository('MommyProfilBundle:Woman')->findOneByUser($user);
    if (is_object($woman) && is_object($woman->getMarriage())) {
      $title = $woman->getMarriage()->getDescFR();
    } else {
      $title = 'Femme';
    }
    $woman = null; unset($woman);
    $family = $this->getDoctrine()->getRepository('MommyProfilBundle:Family')->findOneByUser($user);

    if (is_object($family) && is_object($family->getSize())) {
      $title .= ' avec '.lcfirst($family->getSize()->getDescFR());
    } else {
      $title .= ' sans enfant';
    }
    $family = null; unset($family);
    $score = json_decode($this->compatibleAction($uid)->getContent(), true);

    return new JsonResponse(array(
      'title' => $title,
      'id' => $user->getId(),
      'name' => $user->getFirstname().' '.substr($user->getLastname(), 0, 1).'.',
      'age' => $user->getAge(),
      'city' => $user->getCity(),
      'photo' => $user->getPhoto(),
      'compatible' => $score['score'],
      ));
  }
}