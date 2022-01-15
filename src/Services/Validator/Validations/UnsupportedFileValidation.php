<?php
namespace Services\Validator\Validations;

use Services\Validator\Validator;
use Services\Validator\ValidatorInterface;
use Symfony\Component\HttpFoundation\Response;

class UnsupportedFileValidation extends Validator implements ValidatorInterface
{
    function __construct($request)
    {
        parent::__construct($request);
    }

    public function check()
    {
        if(isset($this->request->files) && !empty($this->request->files->all()['file']) && $this->request->files->all()['file']->getExtension() != 'pdf'){
            $this->isValid = 0;
            $this->response = $this->response->setStatusCode(Response::HTTP_BAD_REQUEST);
        }

        return $this;
    }
}