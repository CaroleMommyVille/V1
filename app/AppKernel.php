<?php

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Symfony\Bundle\MonologBundle\MonologBundle(),
            new Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
            new Symfony\Bundle\AsseticBundle\AsseticBundle(),
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new Doctrine\Bundle\FixturesBundle\DoctrineFixturesBundle(),
            new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
//            new Aequasi\Bundle\CacheBundle\AequasiCacheBundle(),
            new Ivory\GoogleMapBundle\IvoryGoogleMapBundle(),
            new Widop\HttpAdapterBundle\WidopHttpAdapterBundle(),
            new Bazinga\Bundle\GeocoderBundle\BazingaGeocoderBundle(),
            new Knp\Bundle\GaufretteBundle\KnpGaufretteBundle(),
            new Oneup\UploaderBundle\OneupUploaderBundle(),
            new Mommy\ProfilBundle\MommyProfilBundle(),
            new Mommy\UIBundle\MommyUIBundle(),
            new Mommy\ErrorBundle\MommyErrorBundle(),
            new Mommy\ClubBundle\MommyClubBundle(),
            new Mommy\SecurityBundle\MommySecurityBundle(),
            new Mommy\MapBundle\MommyMapBundle(),
            new Mommy\BlogBundle\MommyBlogBundle(),
            new Mommy\PlayBundle\MommyPlayBundle(),
            new Mommy\ShopBundle\MommyShopBundle(),
            new Mommy\BoxBundle\MommyBoxBundle(),
            new Mommy\SearchBundle\MommySearchBundle(),
            new Mommy\QuestionBundle\MommyQuestionBundle(),
            new Mommy\PubBundle\MommyPubBundle(),
            new Mommy\CGUBundle\MommyCGUBundle(),
            new Mommy\PageBundle\MommyPageBundle(),
            new Mommy\CharteBundle\MommyCharteBundle(),
            new Mommy\LegalBundle\MommyLegalBundle(),
            new Mommy\NousBundle\MommyNousBundle(),
            new Mommy\FeedbackBundle\MommyFeedbackBundle(),
            new Mommy\HomeBundle\MommyHomeBundle(),
            new Mommy\StatsBundle\MommyStatsBundle(),
            new Mommy\NotificationBundle\MommyNotificationBundle(),
            new Mommy\SondageBundle\MommySondageBundle(),
        );

        if (in_array($this->getEnvironment(), array('dev', 'test'))) {
            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new Sensio\Bundle\DistributionBundle\SensioDistributionBundle();
            $bundles[] = new Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();
        }

        return $bundles;
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__.'/config/config_'.$this->getEnvironment().'.yml');
    }
}
