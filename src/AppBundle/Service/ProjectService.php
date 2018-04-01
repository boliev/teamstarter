<?php

namespace AppBundle\Service;

use AppBundle\Entity\Project;
use Symfony\Component\Config\Exception\FileLoaderLoadException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Translation\TranslatorInterface;

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
     * UserService constructor.
     *
     * @param TranslatorInterface $translator
     * @param string              $kernelRoot
     * @param int                 $screenMaxSize
     */
    public function __construct(TranslatorInterface $translator, string $kernelRoot, int $screenMaxSize)
    {
        $this->kernelRoot = $kernelRoot;
        $this->translator = $translator;
        $this->screenMaxSize = $screenMaxSize;
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

        $fs = $this->getFS();
        $dir = substr(md5($project->getId()), 0, 2);

        $screenName = $project->getId().time().'.'.$extension;
        $screen->move($this->kernelRoot.'/../web/screens/'.$dir.'/', $screenName);

        return '/screens/'.$dir.'/'.$screenName;
    }

    /**
     * @return Filesystem
     */
    private function getFS(): Filesystem
    {
        return new Filesystem();
    }
}
