<?php

namespace AppBundle\Service;

use AppBundle\Entity\User;
use Symfony\Component\Config\Exception\FileLoaderLoadException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Translation\TranslatorInterface;

class UserService
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
    private $avatarMaxSize;

    /**
     * UserService constructor.
     *
     * @param TranslatorInterface $translator
     * @param string              $kernelRoot
     * @param int                 $avatarMaxSize
     */
    public function __construct(TranslatorInterface $translator, string $kernelRoot, int $avatarMaxSize)
    {
        $this->kernelRoot = $kernelRoot;
        $this->translator = $translator;
        $this->avatarMaxSize = $avatarMaxSize;
    }

    /**
     * @param User  $user
     * @param array $uploadedFileSource
     *
     * @return string
     *
     * @throws FileLoaderLoadException
     *
     * @internal param UploadedFile $avatar
     */
    public function uploadAvatar(User $user, array $uploadedFileSource): string
    {
        $avatar = new UploadedFile($uploadedFileSource['tmp_name'], $uploadedFileSource['name'], mime_content_type($uploadedFileSource['tmp_name']), $uploadedFileSource['size']);
        if ($this->avatarMaxSize < $avatar->getClientSize()) {
            throw new FileLoaderLoadException($this->translator->trans('user.avatar_max_size_exception', ['{size}' => round(($this->avatarMaxSize / 1000000), 1)]));
        }

        $extension = $avatar->guessClientExtension();
        if (!in_array(strtolower($extension), ['jpg', 'png', 'jpeg'])) {
            throw new FileLoaderLoadException($this->translator->trans('user.avatar_extension_exception'));
        }

        $fs = $this->getFS();
        $dir = substr(md5($user->getId()), 0, 2);

        if ($user->getProfilePicture() && $fs->exists($this->kernelRoot.'/web'.$user->getProfilePicture())) {
            $fs->remove($this->kernelRoot.'/../web'.$user->getProfilePicture());
        }
        $pictureName = $user->getId().'.'.$extension;
        $avatar->move($this->kernelRoot.'/../web/avatars/'.$dir.'/', $pictureName);

        return '/avatars/'.$dir.'/'.$pictureName;
    }

    /**
     * @return Filesystem
     */
    private function getFS(): Filesystem
    {
        return new Filesystem();
    }
}
