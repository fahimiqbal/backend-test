<?php
namespace Services\FileUploader\FileUploaders;


use Services\FileUploader\AbstractFileUploader;
use Services\FileUploader\FileUploaderInterface;
use SplFileInfo;
use S3Stub\Client as S3StubClient;

class S3FileUploader extends AbstractFileUploader implements FileUploaderInterface
{
    private $data;
    private $s3Client;

    function __construct(array $s3Config)
    {
        $this->config = $s3Config;

        $this->s3Client = new S3StubClient($this->config['access_key_id'], $this->config['secret_access_key']);
    }


    public function transferFile(SplFileInfo $file)
    {
        $this->data['url'] = $this->s3Client->send($file, $this->config['bucketname'])->getPublicUrl();

        return $this->data['url'];
    }
}