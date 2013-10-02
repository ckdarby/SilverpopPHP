<?php

namespace Silverpop;

class TransferPod
{

    private $username, $password, $handle;
    public $port = 21;
    public $timeout = 600;
    public $uploadDir = 'upload';

    public function __construct($config)
    {
        if(empty($config['host']) || empty($config['username']) || empty($config['password'])) {
            throw new \Exception("Missing config setting");
        }

        $this->login($config['host'], $config['username'], $config['password']);
    }

    private function login($host, $username, $password)
    {

        $this->handle = ftp_connect($host, $this->port, $this->timeout);

        //Suppress because PHP throws a warning on failure
        $login = @ftp_login($this->handle, $username, $password);

        if ($this->handle === FALSE || $login === FALSE) {
            throw new \Exception("Couldn't connect as: ".$username."@".$this->host." using password: ".$password);
        }

        return true;
    }

    public function uploadFiles($files, $keepLocal = false)
    {

        if(!ftp_chdir($this->handle, $this->uploadDir)) {
            throw new \Exception("Couldn't change to upload directory: ".$this->uploadDir);
        }

        foreach($files as $file) {

            $fileName = basename($file);

            //Uploading to remote from local
            if(!ftp_pasv($this->handle, true) || !ftp_put($this->handle, $fileName, $file, FTP_BINARY)) {
                throw new \Exception("Couldn't upload file: ".$file);
            }

            // Remove file & Not worried of E_WARNING was just used to upload
            if(!$keepLocal){
                unlink($file);
            }
        }

        return true;
    }

}