<?php

namespace Mommy\UIBundle\Service;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Gaufrette\Filesystem;

class PhotoUploader {
    private static $allowedMimeTypes = array('image/jpeg', 'image/png', 'image/gif');
    private $formats = array(
        'miniature' => array(
            'width'  => '50',
            'height' => '50',
            'fit'    => false,
            ),
        'vignette' => array(
            'width'  => '70',
            'height' => '70',
            'fit'    => false,
            ),
        'medium' => array(
            'width'  => '100',
            'height' => '100',
            'fit'    => false,
            ),
        'preview' => array(
            'width'  => '240',
            'height' => '135',
            'fit'    => true,
            ),
        'post' => array(
            'width'  => '380',
            'height' => '270',
            'fit'    => true,
            ),
        'photo' => array(
            'width'  => '190',
            'height' => '190',
            'fit'    => false,
            ),
        'original' => array(
            'width'  => 'max',
            'height' => 'max',
            'fit'    => true,
            ),
        );
    private $filesystem;
 
    public function __construct(Filesystem $filesystem) {
        $this->filesystem = $filesystem;
    }

    public function upload(UploadedFile $file) {
        // Check if the file's mime type is in the list of allowed mime types.
        if (!in_array($file->getClientMimeType(), self::$allowedMimeTypes)) {
            throw new \InvalidArgumentException(sprintf('Files of type %s are not allowed.', $file->getClientMimeType()));
        }

        // Generate a unique filename based on the date and add file extension of the uploaded file
        $filename = sprintf('%s/%s/%s/%s', date('Y'), date('m'), date('d'), uniqid());

        foreach ($this->formats as $fmt => $size) {
            $adapter = $this->filesystem->getAdapter();
            $thumb = new \Imagick();
            $thumb->readImage($file->getPathname());
            $thumb->stripImage();
            if ($fmt != 'original') {
                $f = sprintf('%s-%s.%s', $filename, $fmt, $file->getClientOriginalExtension());
                $thumb->scaleImage($size['width'],$size['height'],$size['fit']);
            } else {
                $f = sprintf('%s.%s', $filename, $file->getClientOriginalExtension());
            }
            $output = sprintf('%s-%s', $file->getPathname(), $fmt);
            $thumb->writeImage($output);
            $adapter->setMetadata($f, array('contentType' => $file->getClientMimeType()));
            $adapter->write($f, file_get_contents($output));
        }
        return $filename.'.'.$file->getClientOriginalExtension();
    }
 
    public function uploadFromUrl($url) {
        // Get file extension
        $extension = pathinfo($url, PATHINFO_EXTENSION);
 
        // Generate a unique filename based on the date and add file extension of the uploaded file
        $filename = sprintf('%s/%s/%s/%s.%s', date('Y'), date('m'), date('d'), uniqid(), $extension);
 
        // Guess mime type
        $mimeType = $this->guessMimeType($extension);
 
        $adapter = $this->filesystem->getAdapter();
        $adapter->setMetadata($filename, array('contentType' => $mimeType));
        $adapter->write($filename, file_get_contents($url));
 
        return $filename;
    }
 
    private function guessMimeType($extension) {
        $mimeTypes = array(
 
            'txt'  => 'text/plain',
            'htm'  => 'text/html',
            'html' => 'text/html',
            'php'  => 'text/html',
            'css'  => 'text/css',
            'js'   => 'application/javascript',
            'json' => 'application/json',
            'xml'  => 'application/xml',
            'swf'  => 'application/x-shockwave-flash',
            'flv'  => 'video/x-flv',
 
            // images
            'png'  => 'image/png',
            'jpe'  => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'jpg'  => 'image/jpeg',
            'gif'  => 'image/gif',
            'bmp'  => 'image/bmp',
            'ico'  => 'image/vnd.microsoft.icon',
            'tiff' => 'image/tiff',
            'tif'  => 'image/tiff',
            'svg'  => 'image/svg+xml',
            'svgz' => 'image/svg+xml',
 
            // archives
            'zip'  => 'application/zip',
            'rar'  => 'application/x-rar-compressed',
            'exe'  => 'application/x-msdownload',
            'msi'  => 'application/x-msdownload',
            'cab'  => 'application/vnd.ms-cab-compressed',
 
            // audio/video
            'mp3'  => 'audio/mpeg',
            'qt'   => 'video/quicktime',
            'mov'  => 'video/quicktime',
 
            // adobe
            'pdf'  => 'application/pdf',
            'psd'  => 'image/vnd.adobe.photoshop',
            'ai'   => 'application/postscript',
            'eps'  => 'application/postscript',
            'ps'   => 'application/postscript',
 
            // ms office
            'doc'  => 'application/msword',
            'rtf'  => 'application/rtf',
            'xls'  => 'application/vnd.ms-excel',
            'ppt'  => 'application/vnd.ms-powerpoint',
            'docx' => 'application/msword',
            'xlsx' => 'application/vnd.ms-excel',
            'pptx' => 'application/vnd.ms-powerpoint',
 
            // open office
            'odt'  => 'application/vnd.oasis.opendocument.text',
            'ods'  => 'application/vnd.oasis.opendocument.spreadsheet',
        );
 
        if (array_key_exists($extension, $mimeTypes)){
            return $mimeTypes[$extension];
        } else {
            return 'application/octet-stream';
        }
 
    }
 
}