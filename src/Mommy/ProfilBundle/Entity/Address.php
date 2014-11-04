<?php

namespace Mommy\ProfilBundle\Entity;

use Symfony\Component\Profil\Core\Address\AddressInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;
use Doctrine\ORM\Mapping as ORM;

/**
 * Mommy\ProfilBundle\Entity\Address
 *
 * @ORM\Table(name="mv_address", indexes={@ORM\Index(name="search_idx", columns={"id", "literal"})})
 * @ORM\Entity()
 */
class Address 
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", unique = true)
     */
    private $literal;

    /**
     * @ORM\Column(type="float", nullable = true)
     */
    private $longitude = null;

    /**
     * @ORM\Column(type="float", nullable = true)
     */
    private $latitude = null;

    public function getId() {
        return $this->id;
    }

    private function GeoEncode($literal) {
        $adapter  = new \Geocoder\HttpAdapter\CurlHttpAdapter();
        $geocoder = new \Geocoder\Geocoder();
        $chain    = new \Geocoder\Provider\ChainProvider(array(
            new \Geocoder\Provider\GoogleMapsProvider($adapter, 'fr_FR', 'France', true),
//            new \Geocoder\Provider\OpenStreetMapProvider($adapter, 'fr_FR', 'France', true),
//            new \Geocoder\Provider\ArcGISOnlineProvider($adapter, 'France', true),
            new \Geocoder\Provider\BingMapsProvider($adapter, 'AjlFMeIQkxaDOE8bqd3qSwsWk1xsUYtMVPznwFyG5cE4BwHSQVq4CJ1yRN7mpQ-h'),
            ));
        $geocoder->registerProvider($chain);

        $geocode = $geocoder->geocode($literal);
        $this->longitude = floatval($geocode->getLongitude());
        $this->latitude = floatval($geocode->getLatitude());
    }

    public function getLiteral() {
        if (($this->longitude == null) || ($this->latitude == null)) {
            $this->GeoEncode($this->literal);
        }
        return $this->literal;
    }

    public function setLiteral($literal) {
        $this->literal = (string) $literal;
        $this->GeoEncode($this->literal);
    }

    public function getLatitude() {
        return $this->latitude;
    }

    public function setLatitude($lat) {
        $this->latitude = floatval($lat);
    }

    public function getLongitude() {
        return $this->longitude;
    }

    public function setLongitude($long) {
        $this->longitude = floatval($long);
    }

    public function getCity() {
      $adapter  = new \Geocoder\HttpAdapter\CurlHttpAdapter();
      $geocoder = new \Geocoder\Geocoder();
      $chain    = new \Geocoder\Provider\ChainProvider(array(
          new \Geocoder\Provider\GoogleMapsProvider($adapter, 'fr_FR', 'France', true),
//          new \Geocoder\Provider\OpenStreetMapProvider($adapter, 'fr_FR', 'France', true),
//          new \Geocoder\Provider\ArcGISOnlineProvider($adapter, 'France', true),
          new \Geocoder\Provider\BingMapsProvider($adapter, 'AjlFMeIQkxaDOE8bqd3qSwsWk1xsUYtMVPznwFyG5cE4BwHSQVq4CJ1yRN7mpQ-h'),
          ));
      $geocoder->registerProvider($chain);
      $geocode = $geocoder->geocode($this->literal);
      return $geocode->getCity();
    }
}