<?php

namespace App\EventSubscriber;

use App\Entity\Project;
use App\Entity\ProjectOpenRole;
use App\Entity\ProjectOpenRoleSkills;
use App\Search\ProjectIndexer\ProjectIndexerInterface;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;

class ProjectSearchIndexerSubscriber implements EventSubscriber
{
    /** @var ProjectIndexerInterface */
    private $projectIndexer;

    public function __construct(ProjectIndexerInterface $projectIndexer)
    {
        $this->projectIndexer = $projectIndexer;
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
        if ($entity instanceof Project) {
            $this->projectIndexer->indexOne($entity);
        } elseif ($entity instanceof ProjectOpenRoleSkills) {
            $this->projectIndexer->indexOne($entity->getOpenRole()->getProject());
        } elseif ($entity instanceof ProjectOpenRole) {
            $this->projectIndexer->indexOne($entity->getProject());
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
