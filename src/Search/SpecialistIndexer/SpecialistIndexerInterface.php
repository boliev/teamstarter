<?php

namespace App\Search\SpecialistIndexer;

use App\Entity\User;

interface SpecialistIndexerInterface
{
    public function indexAll(): void;

    public function indexOne(User $user): void;
}
