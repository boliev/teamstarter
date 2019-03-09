<?php

namespace App\Notifications;

use App\Entity\Project;
use App\Entity\SupportRequest;
use App\Entity\User;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Routing\RouterInterface;

class Notificator
{
    /** @var string */
    private $fromEmailAddress;

    /** @var string */
    private $fromName;

    /** @var \Swift_Mailer */
    private $mailer;

    /** @var array */
    private $reviewers;

    /** @var array */
    private $newProEmails;

    /** @var array */
    private $supportEmails;

    /** @var array */
    private $paymentErrorsEmails;

    /** @var TranslatorInterface */
    private $translator;

    /** @var RouterInterface  */
    private $router;

    public function __construct(
        string $fromEmailAddress,
        string $fromName,
        string $reviewers,
        string $newProEmails,
        string $supportEmails,
        string $paymentErrorsEmails,
        \Swift_Mailer $mailer,
        TranslatorInterface $translator,
        RouterInterface $router
    ) {
        $this->fromEmailAddress = $fromEmailAddress;
        $this->fromName = $fromName;
        $this->mailer = $mailer;
        $this->translator = $translator;
        $this->reviewers = explode(',', $reviewers);
        $this->newProEmails = explode(',', $newProEmails);
        $this->supportEmails = explode(',', $supportEmails);
        $this->paymentErrorsEmails = explode(',', $paymentErrorsEmails);
        $this->router = $router;
    }

    public function projectOnReview(Project $project)
    {
        $user = $project->getUser();
        $this->sendEmail(
            [$user->getEmail()],
            $this->trans('project.submit_success_email.subject'),
            $this->trans('project.submit_success_email.message', ['%username%' => $this->getUsername($user)])
        );

        $this->sendEmail(
            $this->reviewers,
            $this->trans('project.submit_success_admin_email.subject'),
            $this->trans('project.submit_success_admin_email.message',
                ['%link%' => $this->getAdminReviewProjectLink($project)]
            )
        );
    }

    public function projectRemoderate(Project $project)
    {
        $user = $project->getUser();

        $this->sendEmail(
            [$user->getEmail()],
            $this->trans('project.edit_success_email.subject'),
            $this->trans('project.edit_success_email.message', ['%username%' => $this->getUsername($user)])
        );

        $this->sendEmail(
            $this->reviewers,
            $this->trans('project.remoderate_admin_email.subject'),
            $this->trans('project.remoderate_admin_email.message',
                ['%link%' => $this->getAdminReviewProjectLink($project)]
            )
        );
    }

    public function projectDeclined(Project $project)
    {
        $user = $project->getUser();
        $comment = $project->getLastModeratorComment();

        $this->sendEmail(
            [$user->getEmail()],
            $this->trans('project.decline_email.subject'),
            $this->trans('project.decline_email.message', [
                '%username%' => $this->getUsername($user),
                '%comment%' => $comment->getComment(),
            ])
        );
    }

    public function projectApproved(Project $project)
    {
        $user = $project->getUser();

        $this->sendEmail(
            [$user->getEmail()],
            $this->trans('project.approve_success_email.subject'),
            $this->trans('project.approve_success_email.message', [
                '%username%' => $this->getUsername($user),
                '%link%' => $this->getProjectLink($project),
            ])
        );
    }

    public function projectRemoderateDeclined(Project $project)
    {
        $user = $project->getUser();

        $this->sendEmail(
            [$user->getEmail()],
            $this->trans('project.remoderate_declined_email.subject'),
            $this->trans('project.remoderate_declined_email.message', [
                '%username%' => $this->getUsername($user),
            ])
        );

        $this->sendEmail(
            $this->reviewers,
            $this->trans('project.remoderate_declinned_admin_email.subject'),
            $this->trans('project.remoderate_declinned_admin_email.message')
        );
    }

    public function newPromoCodeRequest()
    {
        $this->sendEmail(
            $this->newProEmails,
            $this->trans('user.new_request_for_beta_email_admins.subject'),
            $this->trans('user.new_request_for_beta_email_admins.message')
        );
    }

    public function newContactFormRequest(SupportRequest $supportRequest)
    {
        $this->sendEmail(
            $this->supportEmails,
            $this->trans('contact.support_email.subject'),
            $this->trans('contact.support_email.message', [
                '%id%' => $supportRequest->getId(),
                '%email%' => $supportRequest->getEmail(),
                '%name%' => $supportRequest->getUser() ? $supportRequest->getUser()->getFullName() : '-',
                '%user_id%' => $supportRequest->getUser() ? $supportRequest->getUser()->getId() : '-',
                '%user_pro%' => $supportRequest->getUser() && $supportRequest->getUser()->isPro() ? '+' : '-',
                '%title%' => $supportRequest->getTitle(),
                '%text%' => $supportRequest->getDescription(),
            ])
        );
    }

    public function proUserSet(User $user)
    {
        $this->sendEmail(
            [$user->getEmail()],
            $this->trans('pro.buy_success_email.subject'),
            $this->trans('pro.buy_success_email.message', [
                '%username%' => $this->getUsername($user),
            ])
        );

        $this->sendEmail(
            $this->newProEmails,
            $this->trans('pro.buy_success_email_admins.subject'),
            $this->trans('pro.buy_success_email_admins.message')
        );
    }

    public function paymentCreateError(User $user, \Exception $exception)
    {
        $this->sendEmail(
            $this->paymentErrorsEmails,
            $this->trans('pro.buy_error_email_admins.subject'),
            $this->trans('pro.buy_error_email_admins.message', [
                '%message%' => $exception->getMessage(),
                '%code%' => $exception->getCode(),
                '%type%' => get_class($exception),
                '%user_id%' => $user->getId(),
                '%user_email%' => $user->getEmail(),
            ], 'text')
        );
    }

    public function paymentProcessError(array $data, \Exception $exception)
    {
        $this->sendEmail(
            $this->paymentErrorsEmails,
            $this->trans('pro.buy_error_email_admins.subject'),
            $this->trans('pro.buy_error_email_admins.message', [
                '%message%' => print_r($data, true),
                '%code%' => $exception->getCode(),
                '%type%' => get_class($exception),
                '%user_id%' => '-',
                '%user_email%' => '-',
            ], 'text')
        );
    }

    private function getAdminReviewProjectLink(Project $project)
    {
        return $this->router->generate(
            'easyadmin',
            ['action' => 'show', 'entity' => 'ProjectsInReview', 'id' => $project->getId()],
            UrlGeneratorInterface::ABSOLUTE_URL
        );
    }

    private function getProjectLink(Project $project)
    {
        return $this->router->generate(
            'project_more',
            ['project' => $project->getId()],
            UrlGeneratorInterface::ABSOLUTE_URL
        );
    }

    private function sendEmail(array $emails, string $subject, string $body)
    {
        $message = (new \Swift_Message($subject))
            ->setFrom($this->fromEmailAddress, $this->fromName)
            ->setTo($emails)
            ->setBody($body, 'text/html');

        $this->mailer->send($message);
    }

    private function getUsername(User $user)
    {
        return $user->getFirstName() ?? $user->getEmail();
    }

    private function trans(string $key, array $params = [])
    {
        return $this->translator->trans($key, $params);
    }
}
