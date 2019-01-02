<?php

namespace App\Search\SpecialistSearcher;

interface SpecialistSearcherInterface
{
    public function search(string $query): array;
}
