<?php

namespace Services\Validator;


interface ValidatorInterface
{
    /**
     * All validation rule class must have this method which should return that instance
     *
     * @return object
     */
    public function check();
}
