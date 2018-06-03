<?php

namespace AppBundle\EventListener;

use AppBundle\Entity\Project;
use AppBundle\Entity\ProjectOpenVacancy;
use AppBundle\Entity\ProjectOpenVacancySkills;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use FOS\ElasticaBundle\Event\TransformEvent;

class IndexingPropertiesListener implements EventSubscriberInterface
{
    /**
     * @param TransformEvent $event
     */
    public function addCustomProperties(TransformEvent $event)
    {
        $document = $event->getDocument();
        $project = $event->getObject();
        $tags = new ArrayCollection();
        if ($project instanceof Project) {
            /** @var ProjectOpenVacancy $vacancy */
            foreach ($project->getOpenVacancies() as $vacancy) {
                /** @var ProjectOpenVacancySkills $vacancySkill */
                foreach ($vacancy->getSkills() as $vacancySkill) {
                    $skillSlug = $vacancySkill->getSkill()->getSlug();
                    if (!empty($skillSlug) && !$tags->contains($skillSlug)) {
                        $tags->add($skillSlug);
                    }
                }
            }
            $document->set('tags', $tags->toArray());
        }
    }

    public static function getSubscribedEvents()
    {
        return array(
            TransformEvent::POST_TRANSFORM => 'addCustomProperties',
        );
    }
}
