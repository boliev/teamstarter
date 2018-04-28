<?php

namespace AppBundle\EventSubscriber;

use AppBundle\Entity\Project;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Workflow\Event\Event;

class ProjectToReviewSubscriber implements EventSubscriberInterface
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
     * WorkflowLogger constructor.
     *
     * @param \Swift_Mailer       $mailer
     * @param TranslatorInterface $translator
     * @param string              $fromEmailAddress
     */
    public function __construct(\Swift_Mailer $mailer, TranslatorInterface $translator, string $fromEmailAddress)
    {
        $this->mailer = $mailer;
        $this->translator = $translator;
        $this->fromEmailAddress = $fromEmailAddress;
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
            ->setFrom($this->fromEmailAddress)
            ->setTo($user->getEmail())
            ->setBody($this->translator->trans('project.submit_success_email.message', ['%username%' => $user->getFullName()]), 'text/html');

        $this->mailer->send($message);
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            'workflow.project_flow.completed.to_review' => 'onReview',
        );
    }
}
