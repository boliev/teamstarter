<?php

namespace App\Repository;

use App\Entity\Country;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

class CountryRepository extends ServiceEntityRepository
{
    private $alwaysFirstCountry;

    public function __construct(ManagerRegistry $registry, string $entityClass, string $alwaysFirstCountry)
    {
        parent::__construct($registry, $entityClass);
        $this->alwaysFirstCountry = $alwaysFirstCountry;
    }

    public function getLocalizedCountries(string $locale): array
    {
        $getNNameMethod = $this->getNameGetter($locale);
        $field = $this->getNameField($locale);

        $countriesCollection = $this->findBy([], [$field => 'ASC']);
        $countries = [];

        /** @var Country $country */
        foreach ($countriesCollection as $country) {
            if ($country->getCode() === $this->alwaysFirstCountry) {
                $countries = array_merge([$country->$getNNameMethod() => $country->getCode()], $countries);
                continue;
            }

            $countries[$country->$getNNameMethod()] = $country->getCode();
        }

        return $countries;
    }

    private function getNameGetter(string $locale): string
    {
        if ('ru' == $locale) {
            return 'getRu';
        }

        return 'getName';
    }

    private function getNameField(string $locale): string
    {
        if ('ru' == $locale) {
            return 'ru';
        }

        return 'name';
    }
}
