<?php

namespace Shutterstock\CustomerIO\Exception;

use Presto\Response;
use Exception;

class NetworkException extends Exception
{

    // holder for the error-laden response object
    protected $response;

    /**
     * basic construct for custom exceptions
     *
     * @param  object  $response  instance of Presto\Response
     */
    public function __construct(Response $response)
    {
        $this->response = $response;
        parent::__construct("CUSTOMERIO NETWORK EXCEPTION: {$response->error}");
    }

    /**
     * get the url that trigged the failure
     *
     * @return  string  url that failed to respond
     */
    public function getUrl()
    {
        return $this->response->url;
    }

    /**
     * get the CURL error number to describe the failure
     *
     * @return  integer  CURL error number
     */
    public function getCURLErrorNumber()
    {
        return $this->response->errorno;
    }

}

