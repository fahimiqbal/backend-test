<?php
namespace DropboxStub;

use SplFileInfo;

class DropboxClient
{
    protected string $accessKey;
    protected string $secretToken;
    protected string $container;

    public function __construct(
        string $accessKey,
        string $secretToken,
        string $container
    ) {
        $this->accessKey = $accessKey;
        $this->secretToken = $secretToken;
        $this->container = $container;
    }

    /**
     * "Uploads" file to Dropbox.
     * 
     * @param  SplFileInfo $file File to be uploaded.
     * @return string URL to the uploaded file.
     */
    public function upload(SplFileInfo $file): string
    {
        return sprintf('http://ipedis.dropbox.com/%s/%s', $this->container, $file->getFilename());
    }
}