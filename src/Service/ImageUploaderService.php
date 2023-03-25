<?php

namespace App\Service;

use Intervention\Image\ImageManagerStatic as Image;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\AsciiSlugger;

class ImageUploaderService
{

    private AsciiSlugger $slugger;
    private Filesystem $fileSystem;

    public function __construct(
        private $targetDirectory
    ) {
        $this->slugger = new AsciiSlugger();
        Image::configure(['driver' => 'imagick']);
        $this->fileSystem = new Filesystem();
    }
    public function upload(UploadedFile $file, ?string $sub_dir = null, int $width = 1200) : ?string
    {
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
//        $copy_name = pathinfo($file->getPathname(), PATHINFO_DIRNAME) . '/' . uniqid();
//        $this->fileSystem->copy($file->getPathname(), $copy_name);
//        $file  = new UploadedFile($copy_name, $copy_name);
        $safeFilename = $this->slugger->slug($originalFilename);
        $fileName = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();

        $folder_path = $this->getTargetDirectory() . ($sub_dir ? '/' . $sub_dir  : '');

        try {
//            $file->move($folder_path, $fileName);
            $this->fileSystem->copy($file->getPathname(), $folder_path . '/' . $fileName);
            $image  = Image::make($folder_path .'/'. $fileName);
            $_width = $image->getWidth();
            $_height = $image->getHeight() * ($width / $_width);
            $image->resize($width, $_height)->save(null, 100);
        } catch (FileException $e) {
            return null;
        }
        return $fileName;
    }
    public function upload_thumbnail(UploadedFile $file, ?string $sub_dir = null, int $width = 1200) : ?string
    {
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $this->slugger->slug($originalFilename);
        $fileName = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();

        $folder_path = $this->getTargetDirectory() . ($sub_dir ? '/' . $sub_dir : '');

        try {
            $file->move($folder_path, $fileName);
            $image  = Image::make($folder_path .'/'. $fileName);
            $_width = $image->getWidth();
            $_height = $image->getHeight() * ($width / $_width);
            $image->resize(200, $image->getHeight() * 200 / $_width)->fit($width, (int) $_height)->save(null, 100);
        } catch (FileException $e) {
            return null;
        }
        return $fileName;
    }

    public function remove_image(?string $sub_dir = null, string $file_name)
    {
        try {
            $folder_path = $this->getTargetDirectory() . ($sub_dir ? '/' . $sub_dir : '');
            $this->fileSystem->remove($folder_path . '/' . $file_name);
        } catch (IOException){
            //
        }
    }



    public function getTargetDirectory()
    {
        return $this->targetDirectory;
    }
    public function setTargetDirectory(string $targetDirectory)
    {
        $this->targetDirectory = $targetDirectory;
    }

}