<?php

namespace AppBundle\EventSubscriber;

use AppBundle\Entity\Project;
use AppBundle\Entity\ProjectOpenRole;
use AppBundle\Entity\ProjectOpenRoleSkills;
use AppBundle\Search\ProjectIndexer\ProjectIndexerInterface;
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
