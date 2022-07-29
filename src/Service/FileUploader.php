<?php

namespace App\Service;

use Exception;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class FileUploader
{
    public function __construct(public SluggerInterface $slugger)
    {
    }

    /**
     * @throws Exception
     */
    public function upload(string $targetDirectory, UploadedFile $file, string $currentFile = ''): string
    {
        if ('' === $currentFile) {
            $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $safeFilename = $this->slugger->slug($originalFilename);
            $fileName = $safeFilename . '-' . uniqid() . '.' . $file->guessExtension();
        } else {
            $fileName = $currentFile;
        }

        try {
            $file->move($targetDirectory, $fileName);
        } catch (FileException $e) {
            throw new Exception($e->getMessage());
        }

        return $fileName;
    }
}