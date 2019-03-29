<?php

namespace App\Uploader;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Contracts\Translation\TranslatorInterface;

class FileUploader
{
    /** @var TranslatorInterface */
    protected $translator;

    /** @var string */
    private $kernelRoot;


    public function __construct(string $kernelRoot)
    {
        $this->kernelRoot = $kernelRoot;
    }

    public function createUploadedFile(array $uploadedFileSource): UploadedFile
    {
        return new UploadedFile($uploadedFileSource['tmp_name'], $uploadedFileSource['name'], mime_content_type($uploadedFileSource['tmp_name']), $uploadedFileSource['size']);
    }

    public function getImageExtension(UploadedFile $file): string
    {
        $extension = $file->guessClientExtension();
        if (!in_array(strtolower($extension), ['jpg', 'png', 'jpeg'])) {
            return null;
        }

        return $extension;
    }

    public function generateDirName(int $entityId): string
    {
        return substr(md5($entityId), 0, 2);
    }

    public function generateFileName(int $entityId, string $extension): string
    {
        return $entityId.time().'.'.$extension;
    }

    public function upload(UploadedFile $file, string $entityDir, int $entityId): string
    {
        $extension = $this->getImageExtension($file);
        $fileDir = $this->generateDirName($entityId);
        $fileName = $this->generateFileName($entityId, $extension);
        $newFileDir = $this->kernelRoot.'/../public/'.$entityDir.'/'.$fileDir;
        $file->move($newFileDir, $fileName);

        return '/'.$entityDir.'/'.$fileDir.'/'.$fileName;
    }
}
