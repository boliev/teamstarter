<?php

namespace AppBundle\Search\ProjectIndexer;

use AppBundle\Entity\Project;

interface ProjectIndexerInterface
{
    public function indexAll(): void;

    public function indexOne(Project $project): void;
}
