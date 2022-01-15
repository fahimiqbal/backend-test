<?php
namespace Services\Validator;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class Validator
{
    private $request;
    private $response;

    private $isValid = true;

    private $validationMethods = [
        'hasFile'
    ];

    function __construct(Request $request)
    {
        $this->request = $request;
        $this->response = new Response(
            '',
            Response::HTTP_OK,
            ['content-type' => 'application/json']
        );
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
        foreach($this->validationMethods as $validationMethod){
            if(!$this->isValid) break;
            $this->{$validationMethod}();
        }

        return $this;
    }

    private function hasFile()
    {
        if(!empty($this->request->files->all())){
            $this->isValid = false;
            $this->response = $this->response->setStatusCode(Response::HTTP_BAD_REQUEST);
        }
    }

}