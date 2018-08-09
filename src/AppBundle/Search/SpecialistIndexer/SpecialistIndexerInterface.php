<?php

namespace AppBundle\Search\SpecialistIndexer;

use AppBundle\Entity\User;

interface SpecialistIndexerInterface
{
    public function indexAll(): void;

    public function indexOne(User $user): void;
}
