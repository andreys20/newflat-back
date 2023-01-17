<?php

namespace App\Services;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileService
{
    public function saveFile(UploadedFile $file, string $directory): ?File
    {
        $fileName = uniqid('', true) . '.' . $file->guessExtension();

        try {
            $movedFile = $file->move($directory, $fileName);
        } catch (FileException $e) {
            throw new FileException('Not found', 200);
        }

        return $movedFile ?? null;
    }
}