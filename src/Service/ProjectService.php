<?php

namespace App\Service;

use App\Entity\Project;
use App\Entity\ProjectScreen;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Config\Exception\LoaderLoadException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Workflow\Registry;
use Symfony\Contracts\Translation\TranslatorInterface;

class ProjectService
{
    /**
     * @var string
     */
    private $kernelRoot;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var int
     */
    private $screenMaxSize;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var Registry
     */
    private $workflowRegistry;

    /**
     * UserService constructor.
     *
     * @param TranslatorInterface    $translator
     * @param EntityManagerInterface $entityManager
     * @param Registry               $workflowRegistry
     * @param string                 $kernelRoot
     * @param int                    $screenMaxSize
     */
    public function __construct(
        TranslatorInterface $translator,
        EntityManagerInterface $entityManager,
        Registry $workflowRegistry,
        string $kernelRoot,
        int $screenMaxSize
    ) {
        $this->kernelRoot = $kernelRoot;
        $this->translator = $translator;
        $this->screenMaxSize = $screenMaxSize;
        $this->entityManager = $entityManager;
        $this->workflowRegistry = $workflowRegistry;
    }

    /**
     * @param Project $project
     * @param array   $uploadedFileSource
     *
     * @return string
     *
     * @throws LoaderLoadException
     *
     * @internal param UploadedFile $avatar
     */
    public function uploadScreen(Project $project, array $uploadedFileSource): string
    {
        $screen = new UploadedFile($uploadedFileSource['tmp_name'], $uploadedFileSource['name'], mime_content_type($uploadedFileSource['tmp_name']), $uploadedFileSource['size']);
        if ($this->screenMaxSize < $screen->getSize()) {
            throw new LoaderLoadException($this->translator->trans('project.screen_max_size_exception', ['{size}' => round(($this->screenMaxSize / 1000000), 1)]));
        }

        $extension = $screen->guessClientExtension();
        if (!in_array(strtolower($extension), ['jpg', 'png', 'jpeg'])) {
            throw new LoaderLoadException($this->translator->trans('project.screen_extension_exception'));
        }

        $dir = substr(md5($project->getId()), 0, 2);

        $screenName = $project->getId().time().'.'.$extension;
        $screen->move($this->kernelRoot.'/../public/screens/'.$dir.'/', $screenName);

        return '/screens/'.$dir.'/'.$screenName;
    }

    /**
     * @param ProjectScreen $screen
     *
     * @return bool
     */
    public function removeScreen(ProjectScreen $screen): bool
    {
        $filename = $this->kernelRoot.'/../public/screens/'.$screen->getScreenshot();
        $fs = $this->getFS();
        $fs->remove($filename);
        $this->entityManager->remove($screen);
        $this->entityManager->flush();

        return true;
    }

    /**
     * @param Project $project
     *
     * @return bool
     */
    public function reModerateIfNeeded(Project $project): bool
    {
        return $this->applyStatus($project, 're_moderate');
    }

    /**
     * @param Project $project
     *
     * @return bool
     */
    public function sendToModerate(Project $project)
    {
        if ($this->reModerateIfNeeded($project)) {
            return true;
        }

        return $this->applyStatus($project, 'to_review');
    }

    /**
     * @param Project $project
     *
     * @return bool
     */
    public function close(Project $project)
    {
        return $this->applyStatus($project, 'close');
    }

    public function decline(Project $project)
    {
        return $this->applyStatus($project, 'decline');
    }

    public function approve(Project $project)
    {
        return $this->applyStatus($project, 'approve');
    }

    /**
     * @param Project $project
     *
     * @return bool
     */
    public function reOpen(Project $project)
    {
        return $this->applyStatus($project, 're_open');
    }

    /**
     * @param Project $project
     *
     * @return bool
     */
    public function reModerateDeclined(Project $project)
    {
        return $this->applyStatus($project, 're_moderate_declined');
    }

    /**
     * @param Project $project
     * @param string  $status
     *
     * @return bool
     */
    private function applyStatus(Project $project, string $status)
    {
        $workflow = $this->workflowRegistry->get($project, 'project_flow');

        if ($workflow->can($project, $status)) {
            $workflow->apply($project, $status);
            $this->entityManager->persist($project);
            $this->entityManager->flush();

            return true;
        }

        return false;
    }

    /**
     * @return Filesystem
     */
    private function getFS(): Filesystem
    {
        return new Filesystem();
    }
}
