<?php
namespace Services\FileUploader;


use PDFStub\Client;
use Services\FileUploader\FileUploaders\DropboxFileUploader;
use Services\FileUploader\FileUploaders\FtpFileUploader;
use Services\FileUploader\FileUploaders\S3FileUploader;
use SplFileInfo;

class FileUploader
{
    protected $file;
    protected $config;
    protected $upload;
    protected $converts;

    protected $convertedFiles;
    protected $pdfConverter;

    protected $data;


    /**
     *
     * @param array $config
     * @param SplFileInfo $file
     * @param string $upload
     * @param array $converts
     */
    function __construct(array $config, SplFileInfo $file, string $upload, array $converts)
    {
        $this->config = $config;

        $this->file = $file;

        $this->upload = $upload;

        $this->converts = $converts;

        $this->ftpFileUploader = new FtpFileUploader($this->config['ftp']);
        $this->s3FileUploader = new S3FileUploader($this->config['s3']);
        $this->dropboxFileUploader = new DropboxFileUploader($this->config['dropbox']);
    }

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

    /**
     * Uploads files to mentioned storage
     *
     * @return object
     */
    public function upload()
    {
        $uploaderInstance = $this->getUploaderInstance();

        $this->data['url'] = $uploaderInstance->transferFile($this->file);

        if(!empty($this->convertedFiles)){
            foreach($this->convertedFiles as $format=>$convertedFile){
                $file = new SplFileInfo($convertedFile);
                $this->data['formats'][$format] = $uploaderInstance->transferFile($file);
                
            }
        }

        return $this;
    }

    private function getUploaderInstance()
    {
        switch(strtolower($this->upload))
        {
            case 'ftp': return $this->ftpFileUploader;
                break;

            case 's3': return $this->s3FileUploader;
                break;

            case 'dropbox': return $this->dropboxFileUploader;
                break;
        }
    }

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