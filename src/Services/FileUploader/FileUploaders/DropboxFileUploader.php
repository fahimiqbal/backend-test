<?php
namespace Services\FileUploader\FileUploaders;


use Services\FileUploader\AbstractFileUploader;
use Services\FileUploader\FileUploaderInterface;
use SplFileInfo;
use DropboxStub\DropboxClient;

class DropboxFileUploader extends AbstractFileUploader implements FileUploaderInterface
{
    private $data;
    private $dropboxClient;

    function __construct(array $s3Config)
    {
        $this->config = $s3Config;

        $this->dropboxClient = new DropboxClient($this->config['access_key'], $this->config['secret_token'], $this->config['container']);
    }


    public function transferFile(SplFileInfo $file)
    {
        $this->data['url'] = $this->dropboxClient->upload(new SplFileInfo($file));

        return $this->data['url'];
    }
}