<?php

namespace App\EventSubscriber;

use App\Entity\Project;
use App\Notifications\Notificator;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBag;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Workflow\Event\Event;
use Symfony\Contracts\Translation\TranslatorInterface;

class ProjectChangeProgressStatusSubscriber implements EventSubscriberInterface
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var FlashBag
     */
    private $flashBag;

    /**
     * @var Notificator
     */
    private $notificator;

    /**
     * @param TranslatorInterface $translator
     * @param FlashBagInterface   $flashBag
     * @param Notificator         $notificator
     */
    public function __construct(
        TranslatorInterface $translator,
        FlashBagInterface $flashBag,
        Notificator $notificator
    ) {
        $this->translator = $translator;
        $this->flashBag = $flashBag;
        $this->notificator = $notificator;
    }

    /**
     * @param Event $event
     */
    public function onReview(Event $event)
    {
        /** @var Project $project */
        $project = $event->getSubject();

        $this->flashBag->add('project-saved', $this->translator->trans('project.submit_success'));

        $this->notificator->projectOnReview($project);
    }

    /**
     * @param Event $event
     */
    public function onReModerate(Event $event)
    {
        /** @var Project $project */
        $project = $event->getSubject();

        $this->flashBag->add('project-saved', $this->translator->trans('project.edit_success'));

        $this->notificator->projectRemoderate($project);
    }

    /**
     * @param Event $event
     */
    public function onDecline(Event $event)
    {
        /** @var Project $project */
        $project = $event->getSubject();

        $this->notificator->projectDeclined($project);
    }

    /**
     * @param Event $event
     */
    public function onApprove(Event $event)
    {
        /** @var Project $project */
        $project = $event->getSubject();

        $this->notificator->projectApproved($project);
    }

    /**
     * @param Event $event
     */
    public function onRemoderateDeclined(Event $event)
    {
        /** @var Project $project */
        $project = $event->getSubject();

        $this->notificator->projectRemoderateDeclined($project);
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            'workflow.project_flow.completed.to_review' => 'onReview',
            'workflow.project_flow.completed.re_moderate' => 'onReModerate',
            'workflow.project_flow.completed.re_open' => 'onReModerate',
            'workflow.project_flow.completed.decline' => 'onDecline',
            'workflow.project_flow.completed.re_moderate_declined' => 'onRemoderateDeclined',
            'workflow.project_flow.completed.approve' => 'onApprove',
        );
    }
}
