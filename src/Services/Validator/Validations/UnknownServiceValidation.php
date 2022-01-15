<?php
namespace Services\Validator\Validations;

use Services\Validator\Validator;
use Services\Validator\ValidatorInterface;
use Symfony\Component\HttpFoundation\Response;

class UnknownServiceValidation extends Validator implements ValidatorInterface
{
    function __construct($request)
    {
        parent::__construct($request);
    }

    public function check()
    {
        if(isset($this->request->request) && !empty($this->request->request->all())){
            $upload = $this->request->request->all()['upload'];
            if(!in_array($upload, $this->availableServices)){
                $this->isValid = 0;
                $this->response = $this->response->setStatusCode(Response::HTTP_BAD_REQUEST);
            }
        }

        return $this;
    }
}