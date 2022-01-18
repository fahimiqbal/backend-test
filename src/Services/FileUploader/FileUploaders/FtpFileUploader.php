<?php
namespace Services\FileUploader\FileUploaders;


use FTPStub\FTPUploader;
use Services\FileUploader\AbstractFileUploader;
use Services\FileUploader\FileUploaderInterface;
use SplFileInfo;

class FtpFileUploader extends AbstractFileUploader implements FileUploaderInterface
{
    private $data;
    private $ftpUploader;

    function __construct(array $ftpConfig)
    {
        $this->config = $ftpConfig;

        $this->ftpUploader = new FTPUploader();
    }


    public function transferFile(SplFileInfo $file)
    {
        if($this->ftpUploader->uploadFile($file, $this->config['hostname'], $this->config['username'],  $this->config['password'], $this->config['destination'])){
            $this->data['url'] = "ftp://{$this->config['hostname']}/{$this->config['destination']}/{$file->getFilename()}";
        }

        return $this->data['url'];
    }
}