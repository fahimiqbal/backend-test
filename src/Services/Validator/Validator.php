<?php
namespace Services\Validator;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Services\Validator\Validations\HasFileValidation;
use Services\Validator\Validations\HasParametersValidation;
use Services\Validator\Validations\UnknownServiceValidation;
use Services\Validator\Validations\UnsupportedFormatValidation;
use Services\Validator\Validations\CheckMethodValidation;
use Services\Validator\Validations\UnsupportedFileValidation;

class Validator
{
    protected $request;
    protected $response;

    protected $isValid;

    private $validations = [
        HasFileValidation::class,
        HasParametersValidation::class,
        UnknownServiceValidation::class,
        UnsupportedFormatValidation::class,
        CheckMethodValidation::class,
        UnsupportedFileValidation::class
    ];

    protected $availableServices = ['dropbox', 's3', 'ftp'];

    protected $availableFormats = ['jpg', 'webp', 'png'];

    function __construct(Request $request)
    {
        $this->request = $request;
        $this->response = new Response(
            '',
            Response::HTTP_OK,
            ['content-type' => 'application/json']
        );
        $this->isValid = true;
    }

    public function isValid()
    {
        return $this->isValid;
    }

    public function getResponse()
    {
        return $this->response;
    }

    public function validate()
    {
        foreach ($this->validations as $validation) {
            if(!$this->isValid) break;
            $validationClass = (new $validation($this->request))->check();
            $this->isValid = $validationClass->isValid();
            $this->response = $validationClass->getResponse();
        }

        return $this;
    }
}