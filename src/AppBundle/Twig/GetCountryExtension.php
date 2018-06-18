<?php

namespace AppBundle\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class GetCountryExtension extends AbstractExtension
{
    public function getFilters()
    {
        return array(
            new TwigFilter('country_name', array($this, 'countryName')),
        );
    }

    public function countryName($countryCode)
    {
        return \Symfony\Component\Intl\Intl::getRegionBundle()->getCountryName($countryCode);
    }
}
