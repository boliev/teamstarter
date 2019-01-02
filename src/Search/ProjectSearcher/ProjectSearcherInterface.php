<?php

namespace App\Search\ProjectSearcher;

interface ProjectSearcherInterface
{
    public function search(string $query): array;
}
