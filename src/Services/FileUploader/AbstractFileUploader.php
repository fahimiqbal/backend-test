<?php
namespace Services\FileUploader;

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