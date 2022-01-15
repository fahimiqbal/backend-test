<?php
namespace Services\Validator\Validations;

use Services\Validator\Validator;
use Services\Validator\ValidatorInterface;
use Symfony\Component\HttpFoundation\Response;

class CheckMethodValidation extends Validator implements ValidatorInterface
{
    function __construct($request)
    {
        parent::__construct($request);
    }

    public function check()
    {
        if(!$this->request->isMethod('POST')){
            $this->isValid = 0;
            $this->response = $this->response->setStatusCode(Response::HTTP_METHOD_NOT_ALLOWED);
        }

        return $this;
    }
}