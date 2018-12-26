<?php

namespace App\Search\ProjectIndexer;

use App\Entity\Project;

interface ProjectIndexerInterface
{
    public function indexAll(): void;

    public function indexOne(Project $project): void;
}
