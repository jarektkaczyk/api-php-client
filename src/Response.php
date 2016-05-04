<?php

namespace BeMyGuest\SdkClient;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

/**
 * Response is a handy wrapper over the raw response from BMG SDK.
 * It leverages the goodness provided by Illuminate Collection
 * class when handling nested array structures from the API.
 *
 * @author Jarek Tkaczyk <jarek@bemyguest.com.sg>
 * @package bemyguest/api-php-client
 * @version 1.0
 */
class Response extends Collection
{
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
        return is_array($item = data_get($this->items, $key, $default))
                ? new static($item)
                : $item;
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
