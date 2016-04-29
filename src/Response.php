<?php

namespace BeMyGuest\SdkClient;

class Response
{
    /**
     * Raw response from the API.
     *
     * @var \StdClass
     */
    protected $raw;

    /**
     * @param \StdClass $raw
     */
    public function __construct($raw)
    {
        $this->raw = $raw;
    }

    /**
     * Get nested data from the API response using dot notation.
     *
     * Working example (https://apidemo.bemyguest.com.sg):
     *
     *     > $client = new BeMyGuest\SdkClient\Client('   YOUR_API_KEY_HERE   ');
     *     > $response = $client->raw()->getProduct('7e7f3757-f065-5ae0-a286-e7e5be1db181');
     *     > $response->get('data.currency.code');
     *     ==> 'SGD'
     *
     *     > $response = $client->raw()->getProducts(['date_start' => '2016-04-28', 'date_end' => '2016-04-28', 'per_page' => 1]);
     *     > $response->get('meta.pagination')
     *     ==> {#2478
     *             "total": 6844,
     *             "count": 1,
     *             "per_page": 1,
     *             "current_page": 1,
     *             "total_pages": 6844,
     *             "links": {#2477
     *               "next": "https://apidemo.bemyguest.com.sg/v1/products?date_end=2016-04-28&date_start=2016-04-28&per_page=1&page=2",
     *             },
     *         }
     *
     *
     * @param  string $key     Name of the nested element using dot notation.
     * @param  mixed  $default Default value returned if key was not found.
     * @return mixed
     */
    public function get($key, $default = null)
    {
        return data_get($this->raw, $key, $default);
    }

    public function toArray()
    {
        return json_decode(json_encode($this->raw), true);
    }

    /**
     * Allows dynamic property calls `$response->data`.
     *
     * @param  string $key
     * @return mixed
     */
    public function __get($key)
    {
        return $this->get($key);
    }
}
