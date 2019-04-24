<?php

namespace App\Notifications;

use App\Entity\Article;
use App\Entity\Comment;
use App\Entity\Message;
use App\Entity\Project;
use App\Entity\SupportRequest;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Routing\RouterInterface;

class Notificator
{
    /** @var string */
    protected $fromEmailAddress;

    /** @var string */
    protected $fromName;

    /** @var \Swift_Mailer */
    protected $mailer;

    /** @var TelegramSender */
    protected $telegramSender;

    /** @var array */
    protected $reviewers;

    /** @var array */
    protected $newProEmails;

    /** @var array */
    protected $supportEmails;

    /** @var array */
    protected $paymentErrorsEmails;

    /** @var string */
    protected $foundersChatTg;

    /** @var TranslatorInterface */
    protected $translator;

    /** @var RouterInterface */
    protected $router;

    /** @var UserRepository */
    protected $userRepository;

    public function __construct(
        string $fromEmailAddress,
        string $fromName,
        string $reviewers,
        string $newProEmails,
        string $supportEmails,
        string $paymentErrorsEmails,
        string $foundersChatTg,
        \Swift_Mailer $mailer,
        TelegramSender $telegramSender,
        TranslatorInterface $translator,
        RouterInterface $router,
        UserRepository $userRepository
    ) {
        $this->fromEmailAddress = $fromEmailAddress;
        $this->fromName = $fromName;
        $this->mailer = $mailer;
        $this->telegramSender = $telegramSender;
        $this->translator = $translator;
        $this->reviewers = explode(',', $reviewers);
        $this->newProEmails = explode(',', $newProEmails);
        $this->supportEmails = explode(',', $supportEmails);
        $this->paymentErrorsEmails = explode(',', $paymentErrorsEmails);
        $this->foundersChatTg = $foundersChatTg;
        $this->router = $router;
        $this->userRepository = $userRepository;
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

        $this->telegramSender->sendMessage(
            $this->foundersChatTg,
            $this->trans('project.submit_success_admin_telegram', [
                '%link%' => $this->getAdminReviewProjectLink($project),
                '%title%' => $project->getName(),
            ])
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

        $this->telegramSender->sendMessage(
            $this->foundersChatTg,
            $this->trans('project.remoderate_admin_telegram', [
                '%link%' => $this->getAdminReviewProjectLink($project),
                '%title%' => $project->getName(),
            ])
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
            $this->trans('project.remoderate_declined_admin_email.subject'),
            $this->trans('project.remoderate_declined_admin_email.message')
        );

        $this->telegramSender->sendMessage(
            $this->foundersChatTg,
            $this->trans('project.remoderate_declined_admin_telegram', [
                '%link%' => $this->getAdminReviewProjectLink($project),
                '%title%' => $project->getName(),
                '%comment%' => $project->getLastModeratorComment()->getComment(),
            ])
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

    public function registrationSuccess(User $user)
    {
        $this->telegramSender->sendMessage(
            $this->foundersChatTg,
            $this->trans('user.telegram_new_registration_success', [
                '%fullName%' => $user->getFullName(),
                '%link%' => $this->router->generate(
                    'specialists_more',
                    ['user' => $user->getId()],
                    UrlGeneratorInterface::ABSOLUTE_URL),
            ])
        );
    }

    public function newProjectComment(Project $project, Comment $comment)
    {
        $user = $project->getUser();
        $this->sendEmail(
            [$user->getEmail()],
            $this->trans('comments.new_project_comment_author_email.subject'),
            $this->trans('comments.new_project_comment_author_email.message', [
                '%username%' => $this->getUsername($user),
                '%link%' => $this->getProjectLink($project),
            ])
        );

        $subscribedUsers = $this->userRepository->getAllUserSubscribedToProjectComments($project);
        /** @var User $subscribedUser */
        foreach($subscribedUsers as $subscribedUser) {
            if($subscribedUser->getId() === $user->getId() || $subscribedUser->getId() == $comment->getFrom()->getId()) {
                continue;
            }

            $this->sendEmail(
                [$subscribedUser->getEmail()],
                $this->trans('comments.new_project_comment_subscribed_email.subject'),
                $this->trans('comments.new_project_comment_subscribed_email.message', [
                    '%username%' => $this->getUsername($subscribedUser),
                    '%link%' => $this->getProjectLink($project),
                    '%title%' => $project->getName(),
                ])
            );
        }

        $sender = $comment->getFrom();
        $this->telegramSender->sendMessage(
            $this->foundersChatTg,
            $this->trans('comments.new_project_comment_admin_tg', [
                '%fullName%' => $sender->getFullName(),
                '%link%' => $this->getProjectLink($project),
                '%title%' => $project->getName(),
                '%comment%' => $comment->getMessage(),
            ])
        );
    }

    public function newArticleComment(Article $article, Comment $comment)
    {
        $subscribedUsers = $this->userRepository->getAllUserSubscribedToArticleComments($article);
        /** @var User $subscribedUser */
        foreach($subscribedUsers as $subscribedUser) {
            if($subscribedUser->getId() == $comment->getFrom()->getId()) {
                continue;
            }

            $this->sendEmail(
                [$subscribedUser->getEmail()],
                $this->trans('comments.new_article_comment_subscribed_email.subject'),
                $this->trans('comments.new_article_comment_subscribed_email.message', [
                    '%username%' => $this->getUsername($subscribedUser),
                    '%link%' => $this->getArticleLink($article),
                    '%title%' => $article->getTitle(),
                ])
            );
        }

        $sender = $comment->getFrom();
        $this->telegramSender->sendMessage(
            $this->foundersChatTg,
            $this->trans('comments.new_article_comment_admin_tg', [
                '%fullName%' => $sender->getFullName(),
                '%link%' => $this->getArticleLink($article),
                '%title%' => $article->getTitle(),
                '%comment%' => $comment->getMessage(),
            ])
        );
    }

    public function newMessage(Message $message)
    {
        $user = $message->getTo();
        $this->sendEmail(
            [$user->getEmail()],
            $this->trans('dialogs.new_message_email.subject'),
            $this->trans('dialogs.new_message_email.message', [
                '%username%' => $this->getUsername($user),
                '%link%' => $this->router->generate('dialogs_list', [], UrlGeneratorInterface::ABSOLUTE_URL),
            ])
        );
    }

    public function newMessagesNotificationsWereSent(int $count)
    {
        $this->telegramSender->sendMessage(
            $this->foundersChatTg,
            $this->trans('dialogs.new_message_admin_telegram', [
                '%count%' => $count
            ])
        );
    }

    protected function getAdminReviewProjectLink(Project $project)
    {
        return $this->router->generate(
            'easyadmin',
            ['action' => 'show', 'entity' => 'ProjectsInReview', 'id' => $project->getId()],
            UrlGeneratorInterface::ABSOLUTE_URL
        );
    }

    protected function getProjectLink(Project $project)
    {
        return $this->router->generate(
            'project_more',
            ['project' => $project->getId()],
            UrlGeneratorInterface::ABSOLUTE_URL
        );
    }

    protected function getArticleLink(Article $article)
    {
        return $this->router->generate(
            'blog_more',
            ['article' => $article->getId()],
            UrlGeneratorInterface::ABSOLUTE_URL
        );
    }

    protected function getSpecialistLink(User $user)
    {
        // specialists_more
        return $this->router->generate(
            'specialists_more',
            ['user' => $user->getId()],
            UrlGeneratorInterface::ABSOLUTE_URL
        );
    }

    protected function sendEmail(array $emails, string $subject, string $body)
    {
        $message = (new \Swift_Message($subject))
            ->setFrom($this->fromEmailAddress, $this->fromName)
            ->setTo($emails)
            ->setBody($body, 'text/html');

        $this->mailer->send($message);
    }

    protected function getUsername(User $user)
    {
        return $user->getFirstName() ?? $user->getEmail();
    }

    protected function trans(string $key, array $params = [])
    {
        return $this->translator->trans($key, $params);
    }
}
