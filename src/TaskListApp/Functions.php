<?php
namespace TaskListApp;

final class Functions
{
    private function __construct() {}

    public static function checkImage($filename)
    {
        $finfo = new \finfo();
        $mime = $finfo->file($filename, FILEINFO_MIME_TYPE);

        $supportedMime = ['image/jpeg', 'image/png', 'image/gif'];

        if (!in_array($mime, $supportedMime))
            throw new \TaskListApp\Exceptions\WrongImageMimeException(WRONGIMAGEMIME);

        return $mime;
    }

    public static function resizeImage($filename, $outputFilename, $newHeight, $newWidth)
    {
        $mime = self::checkImage($filename);

        switch ($mime) {
            case 'image/jpeg':
                $source = imagecreatefromjpeg($filename);
            break;

            case 'image/gif':
                $source = imagecreatefromgif($filename);
            break;

            case 'image/png':
                $source = imagecreatefrompng($filename);
            break;
        }

        list($width, $height) = getimagesize($filename);

        $thumb = imagecreatetruecolor($newWidth, $newHeight);
        //imagealphablending($thumb, false);

        imagecopyresized($thumb, $source, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

        imagejpeg($thumb, $outputFilename);
    }

    public static function createThumbnail($filename, $outputFilename, $maxHeight, $maxWidth)
    {
        self::checkImage($filename);

        list($width, $height) = getimagesize($filename);

        if ($width > $maxWidth) {
            $coefficient = $width / $maxWidth;
            $width = $maxWidth;
            $height /= $coefficient;
        }

        if ($height > $maxHeight) {
            $coefficient = $height / $maxHeight;
            $width /= $coefficient;
            $height = $maxHeight;
        }

        self::resizeImage($filename, $outputFilename, $height, $width);
    }
}