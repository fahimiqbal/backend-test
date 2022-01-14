<?php

namespace S3Stub;

class Client
{
    protected string $accessKeyId;
    protected string $secretAccessKey;

    public function __construct(
        string $accessKeyId,
        string $secretAccessKey
    ) {
        $this->accessKeyId = $accessKeyId;
        $this->secretAccessKey = $secretAccessKey;
    }

    /**
     * "Sends" a file to "S3".
     * 
     * @param  string|\SplFileInfo $file Either SplFileInfo or path to a file that will be sent.
     * @param  string $bucketName File to be uploaded.
     * @return FileObject File object.
     */
    public function send($file, string $bucketName): FileObject
    {
        $file = $file instanceof \SplFileInfo ? $file : new \SplFileInfo($file);
        
        return new FileObject(sprintf('http://%s.s3.amazonaws.com/%s', $bucketName, $file->getFilename()));
    }
}