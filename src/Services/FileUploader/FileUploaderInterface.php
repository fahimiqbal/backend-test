<?php

namespace Services\FileUploader;

use SplFileInfo;

interface FileUploaderInterface
{
    /**
     * 
     *
     * @return object
     */
    public function transferFile(SplFileInfo $file): string;
}
