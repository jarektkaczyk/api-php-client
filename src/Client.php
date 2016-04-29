<?php

namespace BeMyGuest\SdkClient;

use ReflectionMethod;
use Illuminate\Support\Str;
use BeMyGuestAPIV1Lib\Controllers\ProductsController;

class Client implements ClientInterface
{
    /**
     * Determines whether result will be a raw Response object.
     * By default methods return ready-to-use model objects.
     *
     * @var boolean
     */
    protected $raw = false;

    public function __construct($key = null)
    {
        if ($key) {
            Configuration::$xAuthorization = $key;
        }
    }

    /**
     * @return Products collection / array
     */
    public function getProducts(array $params = [])
    {
        return $this->call(new ProductsController(''), 'getProductsList', [], $params);
    }

    /**
     * Get single product model.
     *
     * @return \BeMyGuestAPIV1Lib\Models\Product
     */
    public function getProduct($uuid, array $params = [])
    {
        return $this->call(new ProductsController(''), 'getProduct', [$uuid], $params);
    }

    /**
     * Client will return Response object instead of model/collection in the next call.
     *
     * @return $this
     */
    public function raw()
    {
        $this->raw = true;

        return $this;
    }

    /**
     * Call given SDK controller method to get API response.
     *
     * @param  string $controller SDK Controller to be used.
     * @param  string $method     Method on the controller.
     * @param  array  $required   Array of parameters required by the Method.
     * @param  array  $optional   Array of optional parameters.
     * @return \BeMyGuestAPIV1Lib\Response|\JsonSerializable
     */
    public function call($controller, $method, array $required = [], array $optional = [])
    {
        $optional = $this->mergeParams($controller, $method, $optional);

        $response = new Response(call_user_func_array([$controller, $method], $required + $optional));

        if ($this->raw) {
            $this->raw = false;

            return $response;
        }

        return Model::convert($response);
    }

    /**
     * Ensure correct param number and case for the SDK methods calls.
     * Make sure we support both snake_case (like in the API docs)
     * and camelCase (like in the SDK) params in optional array.
     *
     * @param  string $controller
     * @param  string $method
     * @param  array  $optional
     * @return array
     */
    protected function mergeParams($controller, $method, $optional)
    {
        return array_merge(
            $this->defaultParams($controller, $method),
            array_combine(array_map([Str::class, 'camel'], array_keys($optional)), $optional)
        );
    }

    /**
     * @return array
     */
    protected function defaultParams($class, $method = null)
    {
        $method = $method ? new ReflectionMethod($class, $method) : new ReflectionMethod($class);

        return collect($method->getParameters())
                ->filter(function ($param) {
                    return $param->isDefaultValueAvailable();
                })
                ->keyBy('name')
                ->map(function ($param) {
                    return $param->getDefaultValue();
                })
                ->all();
    }
}
