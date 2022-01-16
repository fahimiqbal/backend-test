<?php
namespace Services\Validator\Validations;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Services\Validator\Validator;
use Services\Validator\ValidatorInterface;

class UnknownServiceValidation extends Validator implements ValidatorInterface
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