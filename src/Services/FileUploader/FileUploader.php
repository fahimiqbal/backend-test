<?php
namespace Services\FileUploader;

use DropboxStub\DropboxClient;
use FTPStub\FTPUploader;
use PDFStub\Client;
use S3Stub\Client as S3StubClient;

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

    function __construct(array $config, SplFileInfo $file, string $upload, array $converts)
    {
        $this->config = $config;

        $this->file = $file;

        $this->upload = $upload;

        $this->converts = $converts;
    }


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

    public function upload()
    {
        switch(strtolower($this->upload))
        {
            case 'ftp': $this->ftpUpload();
                break;

            case 's3': $this->s3Upload();
                break;

            case 'dropbox': $this->dropboxUpload();
                break;
        }

        return $this;
    }

    private function ftpUpload()
    {
        $ftpUploader = new FTPUploader();
        if($ftpUploader->uploadFile($this->file, $this->config['ftp']['hostname'], $this->config['ftp']['username'],  $this->config['ftp']['password'], $this->config['ftp']['destination'])){
            $this->data['url'] = "ftp://{$this->config['ftp']['hostname']}/{$this->config['ftp']['destination']}/{$this->file->getFilename()}";
        }

        if(!empty($this->convertedFiles)){
            foreach($this->convertedFiles as $format=>$convertedFile){
                if($ftpUploader->uploadFile($convertedFile, $this->config['ftp']['hostname'], $this->config['ftp']['username'],  $this->config['ftp']['password'], $this->config['ftp']['destination'])){
                    $this->data['formats'][$format] = "ftp://{$this->config['ftp']['hostname']}/{$this->config['ftp']['destination']}/{$convertedFile->getFilename()}";
                }
            }
        }
    }

    private function s3Upload()
    {
        $s3Client = new S3StubClient($this->config['s3']['access_key_id'], $this->config['s3']['secret_access_key']);
        $this->data['url'] = $s3Client->send($this->file, $this->config['s3']['bucketname'])->getPublicUrl();

        if(!empty($this->convertedFiles)){
            foreach($this->convertedFiles as $format=>$convertedFile){
                $this->data['formats'][$format] = $s3Client->send($convertedFile, $this->config['s3']['bucketname'])->getPublicUrl();
            }
        }
    }

    private function dropboxUpload()
    {
        $dropboxClient = new DropboxClient($this->config['dropbox']['access_key'], $this->config['dropbox']['secret_token'], $this->config['dropbox']['container']);
        $this->data['url'] = $dropboxClient->upload($this->file);

        if(!empty($this->convertedFiles)){
            foreach($this->convertedFiles as $format=>$convertedFile){
                $this->data['formats'][$format] =  $dropboxClient->upload(new SplFileInfo($convertedFile));
            }
        }
    }

    public function getData()
    {
        return $this->data;
    }
}