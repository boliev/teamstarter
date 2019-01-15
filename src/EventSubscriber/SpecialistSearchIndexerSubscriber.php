<?php

namespace App\EventSubscriber;

use App\Entity\User;
use App\Entity\UserSkills;
use App\Entity\UserSpecializations;
use App\Search\ProjectIndexer\ProjectIndexerInterface;
use App\Search\SpecialistIndexer\SpecialistIndexerInterface;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;

class SpecialistSearchIndexerSubscriber implements EventSubscriber
{
    /** @var ProjectIndexerInterface */
    private $specialistIndexer;

    public function __construct(SpecialistIndexerInterface $specialistIndexer)
    {
        $this->specialistIndexer = $specialistIndexer;
    }

    public function postUpdate(LifecycleEventArgs $event): void
    {
        $this->index($event->getEntity());
    }

    public function postPersist(LifecycleEventArgs $event): void
    {
        $this->index($event->getEntity());
    }

    private function index($entity): void
    {
        if ($entity instanceof User) {
            $this->specialistIndexer->indexOne($entity);
        } elseif ($entity instanceof UserSpecializations) {
            $this->specialistIndexer->indexOne($entity->getUser());
        } elseif ($entity instanceof UserSkills) {
            $this->specialistIndexer->indexOne($entity->getUser());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getSubscribedEvents()
    {
        return array(
            'postUpdate',
            'postPersist',
        );
    }
}
