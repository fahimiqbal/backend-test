<?php
namespace Services\Validator\Validations;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Services\Validator\Validator;
use Services\Validator\ValidatorInterface;

class UnsupportedFileValidation extends Validator implements ValidatorInterface
{
    function __construct(Request $request)
    {
        parent::__construct($request);
    }

    /**
     * Validation rule logic
     *
     * @return object
     */
    public function check()
    {
        if(isset($this->request->files) && !empty($this->request->files->all()['file']) && $this->request->files->all()['file']->getExtension() != 'pdf'){
            $this->isValid = 0;
            $this->response = $this->response->setStatusCode(Response::HTTP_BAD_REQUEST);
        }

        return $this;
    }
}