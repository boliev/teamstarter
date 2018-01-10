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
     * UserService constructor.
     *
     * @param TranslatorInterface $translator
     * @param string              $kernelRoot
     */
    public function __construct(TranslatorInterface $translator, string $kernelRoot)
    {
        $this->kernelRoot = $kernelRoot;
        $this->translator = $translator;
    }

    /**
     * @param User         $user
     * @param UploadedFile $avatar
     *
     * @return string
     *
     * @throws FileLoaderLoadException
     */
    public function uploadAvatar(User $user, UploadedFile $avatar): string
    {
        if (2000000 < $avatar->getClientSize()) {
            throw new FileLoaderLoadException($this->translator->trans('user.avatar_max_size_exception', ['2 MB']));
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
