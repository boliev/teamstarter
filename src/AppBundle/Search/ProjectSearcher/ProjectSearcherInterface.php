<?php

namespace AppBundle\Search\ProjectSearcher;

interface ProjectSearcherInterface
{
    public function search(string $query): array;
}
