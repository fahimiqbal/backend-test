<?php

namespace FTPStub;

use SplFileInfo;

class FTPUploader
{
    /**
     * "Uploads" a file to an FTP.
     * 
     * @param  SplFileInfo $file        File to be uploaded.
     * @param  string      $hostname    
     * @param  string      $username    
     * @param  string      $password    
     * @param  string      $destination Destination dir.
     * @return bool True on success.
     */
    public function uploadFile(
        SplFileInfo $file,
        string $hostname,
        string $username,
        string $password,
        string $destination = '/'
    ) {
        // mock auth
        if ($password !== 'convertor') {
            throw new \InvalidArgumentException('Invalid password.');
        }

        return true;
    }
}