<?php

namespace Shutterstock\CustomerIO\Exception;

use Presto\Response;
use Exception;

class ClientException extends Exception
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

        $data = $this->getResponseData();
        if (!empty($data) && isset($data->meta->error)) {
            parent::__construct("CUSTOMERIO CLIENT EXCEPTION: {$data->meta->error}");
        } else {
            parent::__construct('CUSTOMERIO CLIENT EXCEPTION: unknown response');
        }
    }

    /**
     * simple fetch to return the error list from Presto\Response
     *
     * @return  array  list of errors from response
     */
    public function getList()
    {
        return $this->response->errors;
    }

    /**
     * simple fetch to return the decoded response from customer.io
     * if there is nothing (or it is unreadable), returns empty array
     *
     * @return  object  decoded stdclass response from customer.io
     */
    public function getResponseData()
    {
        $data = $this->response->data;
        $data = json_decode($data);

        if (json_last_error() != JSON_ERROR_NONE) {
            return [];
        }

        return $data;
    }

}

