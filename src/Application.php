<?php

namespace App;

//use Services\Validator\Validations\HasFileValidation;

use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Services\Validator\Validator;
use Services\FileUploader\FileUploader;

/**
 * You should implement this class however you want.
 * 
 * The only requirement is existence of public function `handleRequest()`
 * as this is what is tested. The constructor's signature must not be changed.
 */
class Application
{

  protected $config;

  /**
   * By default the constructor takes a single argument which is a config array.
   * You can handle it however you want.
   * 
   * @param array $config Application config.
   */
  public function __construct(array $config)
  {
    $this->config = $config;
  }

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
    try{
      $validator = (new Validator($request))->validate();
      if(!$validator->isValid()) return $validator->getResponse();
  
      $allRequests = $request->request->all();
      $allFiles = $request->files->all();
      
      $fileUploader = new FileUploader($this->config, $allFiles['file'], $allRequests['upload'], $allRequests['formats']);
      $data = $fileUploader->convert()->upload()->getData();
  
      /* $validator = (new HasFileValidation($request))->check();
      if(!$validator->isValid()) return $validator->getResponse(); */
  
      $response = new Response(
        json_encode($data),
        Response::HTTP_OK,
        ['content-type' => 'application/json']
      );
    }
    catch(Exception $e){
      $response = new Response(
        '',
        Response::HTTP_NOT_IMPLEMENTED,
        ['content-type' => 'application/json']
      );

      // Should log error and notify
    }

    $response->setCharset('UTF-8');

    return $response;
  }
}
