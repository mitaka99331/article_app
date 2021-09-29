<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileUploader
{
    private string $publicDirectory;

    public function __construct(string $publicDirectory)
    {
        $this->publicDirectory = $publicDirectory;
    }

    public function upload(string $targetDirectory, UploadedFile $file): string
    {
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename);
        $fileName = $safeFilename . '-' . uniqid('', true) . '.' . $file->guessExtension();
        $destination = $this->publicDirectory . $targetDirectory;

        $file->move($destination, $fileName);

        return $targetDirectory . $fileName;
    }
}