<?php

namespace AppBundle\Service;

use AppBundle\Entity\Project;
use AppBundle\Entity\ProjectScreen;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Config\Exception\FileLoaderLoadException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Workflow\Registry;

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
     * @throws FileLoaderLoadException
     *
     * @internal param UploadedFile $avatar
     */
    public function uploadScreen(Project $project, array $uploadedFileSource): string
    {
        $screen = new UploadedFile($uploadedFileSource['tmp_name'], $uploadedFileSource['name'], mime_content_type($uploadedFileSource['tmp_name']), $uploadedFileSource['size']);
        if ($this->screenMaxSize < $screen->getClientSize()) {
            throw new FileLoaderLoadException($this->translator->trans('project.screen_max_size_exception', ['{size}' => round(($this->screenMaxSize / 1000000), 1)]));
        }

        $extension = $screen->guessClientExtension();
        if (!in_array(strtolower($extension), ['jpg', 'png', 'jpeg'])) {
            throw new FileLoaderLoadException($this->translator->trans('project.screen_extension_exception'));
        }

        $dir = substr(md5($project->getId()), 0, 2);

        $screenName = $project->getId().time().'.'.$extension;
        $screen->move($this->kernelRoot.'/../web/screens/'.$dir.'/', $screenName);

        return '/screens/'.$dir.'/'.$screenName;
    }

    /**
     * @param ProjectScreen $screen
     *
     * @return bool
     */
    public function removeScreen(ProjectScreen $screen): bool
    {
        $filename = $this->kernelRoot.'/../web/screens/'.$screen->getScreenshot();
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
        $workflow = $this->workflowRegistry->get($project, 'project_flow');
        if ($workflow->can($project, 're_moderate')) {
            $workflow->apply($project, 're_moderate');
            $this->entityManager->persist($project);
            $this->entityManager->flush();

            return true;
        }

        return false;
    }

    /**
     * @param Project $project
     *
     * @return bool
     */
    public function sendToModerate(Project $project)
    {
        $workflow = $this->workflowRegistry->get($project, 'project_flow');

        if ($this->reModerateIfNeeded($project)) {
            return true;
        }

        if ($workflow->can($project, 'to_review')) {
            $workflow->apply($project, 'to_review');
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
