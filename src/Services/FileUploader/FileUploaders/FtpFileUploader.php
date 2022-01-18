<?php
namespace Services\FileUploader;


use FTPStub\FTPUploader;

use SplFileInfo;

class FtpFileUploader extends AbstractFileUploader implements FileUploaderInterface
{
    private $data;

    function __construct(array $ftpConfig)
    {
        $this->config = $ftpConfig;
    }


    public function transferFile(SplFileInfo $file)
    {
        $ftpUploader = new FTPUploader();
        if($ftpUploader->uploadFile($file, $this->config['hostname'], $this->config['username'],  $this->config['password'], $this->config['destination'])){
            $this->data['url'] = "ftp://{$this->config['hostname']}/{$this->config['destination']}/{$file->getFilename()}";
        }

        return $this->data['url'];
    }
}