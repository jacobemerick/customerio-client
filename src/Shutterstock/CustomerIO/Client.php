<?php

namespace Shutterstock\CustomerIO;

use Presto\Presto;
use Presto\Response;

class Client
{

    // main endpoint for REST API
    protected $api_endpoint = 'https://track.customer.io/api';

    // holders for authentication params
    protected $site_id;
    protected $secret_key;

    // holder for optional CURL options
    protected $options;

    /**
     * basic construct
     * params are used for authenticating requests
     *
     * @param  string  $site_id     identifying site id from customer.io
     * @param  string  $secret_key  secret api key from customer.io
     * @param  array   $options     optional array of CURL options for request
     */
    public function __construct($site_id, $secret_key, array $options = [])
    {
        $this->site_id     = $site_id;
        $this->secret_key  = $secret_key;
        $this->options     = $options;
    }

    /**
     * create a new customer in customer.io
     * note: this action is identical to updateCustomer
     * on failure throws exception
     *
     * @param   string   $customer_id  unique identifier for the customer
     * @param   string   $email        email address of the customer
     * @param   array    $attributes   optional set of key -> values for customer
     * @return  boolean                whether or not the request was successful
     */
    public function createCustomer(
        $customer_id,
        $email,
        array $attributes = []
    ) {
        $options = $this->options + [
            CURLOPT_HTTPHEADER => [
                'Content-Type' => 'application/x-www-form-urlencoded',
            ],
        ];

        $request = $this->getRequest(
            $options,
            $this->site_id,
            $this->secret_key
        );

        $endpoint = "{$this->api_endpoint}/v1/customers/{$customer_id}";
        $params = array_merge($attributes, ['email' => $email]);
        $params = http_build_query($params);

        $response = $request->put($endpoint, $params);
        return $this->processResponse($response);
    }

    /**
     * update an existing customer in customer.io
     * note: this action is identical to createCustomer
     * on failure throws exception
     *
     * @param   string   $customer_id  unique identifier for the customer
     * @param   string   $email        email address of the customer
     * @param   array    $attributes   optional set of key -> values for customer
     * @return  boolean                whether or not the request was successful
     */
    public function updateCustomer(
        $customer_id,
        $email,
        array $attributes = []
    ) {
        $options = $this->options + [
            CURLOPT_HTTPHEADER => [
                'Content-Type' => 'application/json',
            ],
        ];

        $request = $this->getRequest(
            $options,
            $this->site_id,
            $this->secret_key
        );

        $endpoint = "{$this->api_endpoint}/v1/customers/{$customer_id}";
        $params = array_merge($attributes, ['email' => $email]);
        $params = json_encode($params);

        $response = $request->put($endpoint, $params);
        return $this->processResponse($response);
    }

    /**
     * remote an existing customer from customer.io
     * this will remove the record and all attached data from customer.io
     * on failure throws exception
     *
     * @param   string   $customer_id  unique identifier for the customer
     * @return  boolean                whether or not the request was successful
     */
    public function deleteCustomer($customer_id)
    {
        $request = $this->getRequest(
            $this->options,
            $this->site_id,
            $this->secret_key
        );

        $endpoint = "{$this->api_endpoint}/v1/customers/{$customer_id}";

        $response = $request->delete($endpoint, []);
        return $this->processResponse($response);
    }

    /**
     * create a new event for a given user for tracking purposes
     *
     * @param   string   $customer_id  unique identifier for the customer
     * @param   string   $event_name   name of the event to track
     * @param   array    $data         related information to attach to this event
     * @return  boolean                whether or not the request was successful
     */
    public function trackEvent(
        $customer_id,
        $event_name,
        array $data = []
    ) {
        $options = $this->options + [
            CURLOPT_HTTPHEADER => [
                'Content-Type' => 'application/x-www-form-urlencoded',
            ],
        ];

        $request = $this->getRequest(
            $options,
            $this->site_id,
            $this->secret_key
        );

        $endpoint = "{$this->api_endpoint}/v1/customers/{$customer_id}/events";
        $params = ['name' => $event_name];
        if (!empty($data)) {
            $params['data'] = $data;
        }
        $params = http_build_query($params);

        $response = $request->post($endpoint, $params);
        return $this->processResponse($response);
    }

    protected function getRequest(
        array $options,
        $site_id,
        $secret_key
    ) {
        // use + instead of array_merge to preserve numeric CURLOPT keys
        $options = $options + [
            CURLOPT_USERPWD => "{$site_id}:{$secret_key}",
        ];

        return new Presto($options);
    }

    protected function processResponse(Response $response)
    {
        if ($response->is_success != true) {
            throw new Exception\NetworkException($response);
        }
        if ($response->http_code != 200) {
            throw new Exception\ClientException($response);
        }

        return true;
    }

}

