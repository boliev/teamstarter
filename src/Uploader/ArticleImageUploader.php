<?php

namespace App\Uploader;

use App\Entity\Article;
use Symfony\Component\Config\Exception\LoaderLoadException;
use Symfony\Contracts\Translation\TranslatorInterface;

class ArticleImageUploader
{
    /** @var int */
    private $maxUploadedImageSize;

    /** @var TranslatorInterface */
    private $translator;

    /** @var FileUploader */
    private $uploader;

    public function __construct(TranslatorInterface $translator, FileUploader $uploader, int $maxUploadedImageSize)
    {
        $this->maxUploadedImageSize = $maxUploadedImageSize;
        $this->translator = $translator;
        $this->uploader = $uploader;
    }

    /**
     * @param Article $article
     * @param array   $source
     *
     * @return string
     *
     * @throws LoaderLoadException
     */
    public function upload(Article $article, array $source): string
    {
        $image = $this->uploader->createUploadedFile($source);

        if ($this->maxUploadedImageSize < $image->getSize()) {
            throw new LoaderLoadException($this->translator->trans('editor.upload_max_size_exception', ['{size}' => round(($this->maxUploadedImageSize / 1000000), 1)]));
        }

        $extension = $this->uploader->getImageExtension($image);
        if (null === $extension) {
            throw new LoaderLoadException($this->translator->trans('editor.upload_extension_exception'));
        }

        return $this->uploader->upload($image, 'articles', $article->getId());
    }
}
