<?php

namespace S3Stub;

class FileObject
{
    protected string $publicUrl;

    public function __construct(string $publicUrl)
    {
        $this->publicUrl = $publicUrl;
    }

    public function getPublicUrl(): string
    {
        return $this->publicUrl;
    }
}