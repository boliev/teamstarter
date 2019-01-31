<?php

namespace App\EventSubscriber;

use App\Entity\Project;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBag;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Workflow\Event\Event;
use Symfony\Contracts\Translation\TranslatorInterface;

class ProjectChangeProgressStatusSubscriber implements EventSubscriberInterface
{
    /**
     * @var \Swift_Mailer
     */
    private $mailer;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var string
     */
    private $fromEmailAddress;

    /**
     * @var string
     */
    private $fromName;

    /**
     * @var FlashBag
     */
    private $flashBag;

    /**
     * WorkflowLogger constructor.
     *
     * @param \Swift_Mailer       $mailer
     * @param TranslatorInterface $translator
     * @param string              $fromEmailAddress
     * @param string              $fromName
     * @param FlashBagInterface   $flashBag
     */
    public function __construct(
        \Swift_Mailer $mailer,
        TranslatorInterface $translator,
        string $fromEmailAddress,
        string $fromName,
        FlashBagInterface $flashBag
    ) {
        $this->mailer = $mailer;
        $this->translator = $translator;
        $this->fromEmailAddress = $fromEmailAddress;
        $this->flashBag = $flashBag;
        $this->fromName = $fromName;
    }

    /**
     * @param Event $event
     */
    public function onReview(Event $event)
    {
        /** @var Project $project */
        $project = $event->getSubject();
        $user = $project->getUser();

        $message = (new \Swift_Message($this->translator->trans('project.submit_success_email.subject')))
            ->setFrom($this->fromEmailAddress, $this->fromName)
            ->setTo($user->getEmail())
            ->setBody($this->translator->trans('project.submit_success_email.message', ['%username%' => $user->getFirstName() ?? $user->getEmail()]), 'text/html');

        $this->mailer->send($message);
        $this->flashBag->add('project-saved', $this->translator->trans('project.submit_success'));
    }

    /**
     * @param Event $event
     */
    public function onReModerate(Event $event)
    {
        /** @var Project $project */
        $project = $event->getSubject();
        $user = $project->getUser();

        $message = (new \Swift_Message($this->translator->trans('project.edit_success_email.subject')))
            ->setFrom($this->fromEmailAddress, $this->fromName)
            ->setTo($user->getEmail())
            ->setBody($this->translator->trans('project.edit_success_email.message', ['%username%' => $user->getFirstName() ?? $user->getEmail()]), 'text/html');

        $this->mailer->send($message);
        $this->flashBag->add('project-saved', $this->translator->trans('project.edit_success'));
    }

    /**
     * @param Event $event
     */
    public function onDecline(Event $event)
    {
        /** @var Project $project */
        $project = $event->getSubject();
        $user = $project->getUser();
        $comment = $project->getLastModeratorComment();

        $message = (new \Swift_Message($this->translator->trans('project.decline_email.subject')))
            ->setFrom($this->fromEmailAddress, $this->fromName)
            ->setTo($user->getEmail())
            ->setBody($this->translator->trans('project.decline_email.message', [
                '%username%' => $user->getFirstName() ?? $user->getEmail(),
                '%comment%' => $comment->getComment(),
            ]), 'text/html');

        $this->mailer->send($message);
    }

    /**
     * @param Event $event
     */
    public function onRemoderateDeclined(Event $event)
    {
        /** @var Project $project */
        $project = $event->getSubject();
        $user = $project->getUser();

        $message = (new \Swift_Message($this->translator->trans('project.remoderate_declined_email.subject')))
            ->setFrom($this->fromEmailAddress, $this->fromName)
            ->setTo($user->getEmail())
            ->setBody($this->translator->trans('project.remoderate_declined_email.message', [
                '%username%' => $user->getFirstName() ?? $user->getEmail(),
            ]), 'text/html');

        $this->mailer->send($message);
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
        );
    }
}
