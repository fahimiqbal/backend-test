<?php
namespace Services\Validator\Validations;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Services\Validator\Validator;
use Services\Validator\ValidatorInterface;

class HasParametersValidation extends Validator implements ValidatorInterface
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
        if($this->request->isMethod('POST') && empty($this->request->request->all())){
            $this->isValid = 0;
            $this->response = $this->response->setStatusCode(Response::HTTP_BAD_REQUEST);
        }

        return $this;
    }
}