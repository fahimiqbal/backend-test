<?php

namespace App;

//use Services\Validator\Validations\HasFileValidation;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Services\Validator\Validator;

/**
 * You should implement this class however you want.
 * 
 * The only requirement is existence of public function `handleRequest()`
 * as this is what is tested. The constructor's signature must not be changed.
 */
class Application
{

  /**
   * By default the constructor takes a single argument which is a config array.
   * You can handle it however you want.
   * 
   * @param array $config Application config.
   */
  public function __construct(array $config)
  { }

  /**
   * This method should handle a Request that comes pre-filled with various data.
   *
   * You should implement it however you want and it should return a Response
   * that passes all tests found in ConverterTest.
   * 
   * @param  Request $request The request.
   *
   * @return Response
   */
  public function handleRequest(Request $request): Response
  {
    $validator = (new Validator($request))->validate();
    if(!$validator->isValid()) return $validator->getResponse();

    /* $validator = (new HasFileValidation($request))->check();
    if(!$validator->isValid()) return $validator->getResponse(); */

    return new Response(
      'Content',
      Response::HTTP_OK,
      ['content-type' => 'text/html']
    );
  }
}
