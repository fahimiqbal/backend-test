<?php
namespace Services\FileUploader;

use PDFStub\Client;

abstract class AbstractFileUploader
{
    /**
     * returns data
     *
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }
}