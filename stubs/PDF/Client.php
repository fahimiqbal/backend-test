<?php

namespace PDFStub;

class Client
{
    /**
     * @var string
     */
    private string $appId;

    /**
     * @var string
     */
    private string $accessToken;

    /**
     * Client constructor.
     *
     * @param string $appId
     * @param string $accessToken
     */
    public function __construct(string $appId, string $accessToken)
    {
        $this->appId = $appId;
        $this->accessToken = $accessToken;
    }

    /**
     * "Encodes" a file and returns URL to the result.
     * 
     * @param  \SplFileInfo $file   File to be encoded.
     * @param  string      $format Format to encode to - webm, avi, ogv or mov.
     * @return string URL to the result file.
     */
    public function convertFile(\SplFileInfo $file, string $format): string
    {
        $format = strtolower($format);

        if (!in_array($format, array('jpg', 'webp', 'png'))) {
            throw new \InvalidArgumentException('Trying to encode to an unsupported image format!');
        }

        return sprintf('http://pdf-convertor.com/results/%s/%s.%s', $this->appId, str_replace('.', '_', $file->getFilename()), $format);
    }
}