<?php
namespace Services\FileUploader;

use PDFStub\Client;

abstract class AbstractFileUploader
{
    abstract public function transfer();


    /**
     * Converts file to deisred formats
     *
     * @return object
     */
    public function convert()
    {
        if(!empty($this->converts)){
            $this->pdfConverter = new Client($this->config['pdf-convertor.com']['app_id'], $this->config['pdf-convertor.com']['access_token']);

            foreach($this->converts as $format){
                $this->convertedFiles[$format] = $this->pdfConverter->convertFile($this->file, $format);
            }
        }

        return $this;
    }
}