<?php

namespace App\Service;

use App\Entity\User;
use Symfony\Component\Config\Exception\LoaderLoadException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Contracts\Translation\TranslatorInterface;

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
     * @throws LoaderLoadException
     *
     * @internal param UploadedFile $avatar
     */
    public function uploadAvatar(User $user, array $uploadedFileSource): string
    {
        $avatar = new UploadedFile($uploadedFileSource['tmp_name'], $uploadedFileSource['name'], mime_content_type($uploadedFileSource['tmp_name']), $uploadedFileSource['size']);
        if ($this->avatarMaxSize < $avatar->getSize()) {
            throw new LoaderLoadException($this->translator->trans('user.avatar_max_size_exception', ['{size}' => round(($this->avatarMaxSize / 1000000), 1)]));
        }

        $extension = $avatar->guessClientExtension();
        if (!in_array(strtolower($extension), ['jpg', 'png', 'jpeg'])) {
            throw new LoaderLoadException($this->translator->trans('user.avatar_extension_exception'));
        }

        $fs = $this->getFS();
        $dir = substr(md5($user->getId()), 0, 2);

        if ($user->getProfilePicture() && $fs->exists($this->kernelRoot.'/public'.$user->getProfilePicture())) {
            $fs->remove($this->kernelRoot.'/../public'.$user->getProfilePicture());
        }
        $pictureName = $user->getId().'.'.$extension;
        $avatar->move($this->kernelRoot.'/../public/avatars/'.$dir.'/', $pictureName);

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
