<?php

namespace BeMyGuest\SdkClient;

use JsonSerializable;
use ReflectionMethod;
use InvalidArgumentException;
use BeMyGuestAPIV1Lib\Configuration;
use BeMyGuestAPIV1Lib\Models\BookingStatusEnum;
use BeMyGuestAPIV1Lib\Models\BookingStatusesEnum;
use BeMyGuestAPIV1Lib\Controllers\ConfigController;
use BeMyGuestAPIV1Lib\Controllers\BookingsController;
use BeMyGuestAPIV1Lib\Controllers\ProductsController;

/**
 * This class provides clear interface for the BeMyGuestAPI SDK
 * in order to make working with the SDK as easy as possible.
 *
 * @link http://docs.bemyguest.apiary.io/
 * @link http://docs.bemyguestchinese.apiary.io/
 *
 * @author Jarek Tkaczyk <jarek@bemyguest.com.sg>
 * @package bemyguest/api-php-client
 * @version 1.0
 */
class Client implements ClientInterface
{
    const VERSION = '1.0';

    /**
     * Determines whether result will be a raw Response object.
     * By default methods return ready-to-use model objects.
     *
     * @var boolean
     */
    protected $raw = false;

    /** @var \BeMyGuestAPIV1Lib\Controllers\ConfigController */
    protected $config;

    /** @var \BeMyGuestAPIV1Lib\Controllers\BookingsController */
    protected $bookings;

    /** @var \BeMyGuestAPIV1Lib\Controllers\ProductsController */
    protected $products;

    public function __construct(
        $key = null,
        ConfigController $config = null,
        BookingsController $bookings = null,
        ProductsController $products = null
    ) {
        $key = $key ?: Configuration::$xAuthorization;

        $this->config = $config ?: new ConfigController($key);
        $this->bookings = $bookings ?: new BookingsController($key);
        $this->products = $products ?: new ProductsController($key);
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


    /*
    |--------------------------------------------------------------------------
    | Wrappers for the API endpoints
    |
    | CONFIG
    |--------------------------------------------------------------------------
    */

    /**
     * Get configuration settings for the API user.
     *
     * @link http://docs.bemyguest.apiary.io/#reference/0/config
     *
     * @return \BeMyGuest\SdkClient\Response
     */
    public function getConfig()
    {
        return $this->call($this->config, 'retrieveConfig');
    }

    /**
     * Alias of the getConfig method for consistency with SDK.
     *
     * @return \BeMyGuest\SdkClient\Response
     */
    public function retrieveConfig()
    {
        return $this->getConfig();
    }


    /*
    |--------------------------------------------------------------------------
    | Wrappers for the API endpoints
    |
    | BOOKINGS
    |--------------------------------------------------------------------------
    */

    /**
     * Create a new booking.
     *
     * @link http://docs.bemyguest.apiary.io/#reference/bookings/create-a-booking
     *
     * @param  \BeMyGuestAPIV1Lib\Models\CreateABookingRequest|\JsonSerializable|array  $data
     * @return \BeMyGuest\SdkClient\Response
     */
    public function createBooking($data)
    {
        $data = $this->extractJson($data);

        return $this->call($this->bookings, 'createABooking', compact('data'));
    }

    /**
     * Get single booking details given UUID.
     *
     * @link http://docs.bemyguest.apiary.io/#reference/bookings/get-booking-status
     *
     * @return \BeMyGuest\SdkClient\Response
     */
    public function getBooking($uuid)
    {
        return $this->call($this->bookings, 'getBookingStatus', compact('uuid'));
    }

    /**
     * Alias of the getBooking method for consistency with SDK.
     *
     * @return \BeMyGuest\SdkClient\Response
     */
    public function getBookingStatus($uuid)
    {
        return $this->getBooking($uuid);
    }

    /**
     * Check if provided data is valid for createBooking endpoint (dry-run).
     *
     * @link http://docs.bemyguest.apiary.io/#reference/bookings/check-if-booking-is-possible
     *
     * @param  \BeMyGuestAPIV1Lib\Models\CreateABookingRequest|\JsonSerializable|array  $data
     * @return \BeMyGuest\SdkClient\Response
     */
    public function checkBooking($data)
    {
        $data = $this->extractJson($data);

        return $this->call($this->bookings, 'checkABooking', compact('data'));
    }

    /**
     * Cancel a booking given UUID.
     *
     * @link http://docs.bemyguest.apiary.io/#reference/bookings/update-booking-status
     *
     * @param  string $uuid
     * @return \BeMyGuest\SdkClient\Response
     */
    public function cancelBooking($uuid)
    {
        return $this->updateBookingStatus($uuid, BookingStatusEnum::CANCEL);
    }

    /**
     * Confirm a booking given UUID. Changes status to 'waiting' for BeMyGuest approval.
     *
     * @link http://docs.bemyguest.apiary.io/#reference/bookings/update-booking-status
     *
     * @param  string $uuid
     * @return \BeMyGuest\SdkClient\Response
     */
    public function confirmBooking($uuid)
    {
        return $this->updateBookingStatus($uuid, BookingStatusEnum::CONFIRM);
    }

    /**
     * Resend confirmation email if it was already sent for given booking.
     *
     * @link http://docs.bemyguest.apiary.io/#reference/bookings/update-booking-status
     *
     * @param  string $uuid
     * @return \BeMyGuest\SdkClient\Response
     */
    public function resendConfirmation($uuid)
    {
        return $this->updateBookingStatus($uuid, BookingStatusEnum::RESEND);
    }

    /**
     * Update booking status given UUID.
     *
     * @link http://docs.bemyguest.apiary.io/#reference/bookings/update-booking-status
     *
     * @param  string $uuid
     * @param  string $status                One of statuses defined on \BeMyGuestAPIV1Lib\Models\BookingStatusEnum
     * @return \BeMyGuest\SdkClient\Response
     */
    public function updateBookingStatus($uuid, $status)
    {
        return $this->call($this->bookings, 'updateBookingStatus', compact('status', 'uuid'));
    }

    /**
     * Get paginated bookings with status 'reserved'.
     *
     * @link http://docs.bemyguest.apiary.io/#reference/bookings/get-bookings
     *
     * @param  array  $params
     * @return \BeMyGuest\SdkClient\Response
     */
    public function getReservedbookings(array $params = [])
    {
        return $this->getbookings(['status' => BookingStatusesEnum::RESERVED] + $params);
    }

    /**
     * Get paginated bookings with status 'waiting'.
     *
     * @link http://docs.bemyguest.apiary.io/#reference/bookings/get-bookings
     *
     * @param  array  $params
     * @return \BeMyGuest\SdkClient\Response
     */
    public function getWaitingBookings(array $params = [])
    {
        return $this->getBookings(['status' => BookingStatusesEnum::WAITING] + $params);
    }

    /**
     * Get paginated bookings with status 'cancelled'.
     *
     * @link http://docs.bemyguest.apiary.io/#reference/bookings/get-bookings
     *
     * @param  array  $params
     * @return \BeMyGuest\SdkClient\Response
     */
    public function getCancelledBookings(array $params = [])
    {
        return $this->getBookings(['status' => BookingStatusesEnum::CANCELLED] + $params);
    }

    /**
     * Get paginated bookings with status 'approved'.
     *
     * @link http://docs.bemyguest.apiary.io/#reference/bookings/get-bookings
     *
     * @param  array  $params
     * @return \BeMyGuest\SdkClient\Response
     */
    public function getApprovedBookings(array $params = [])
    {
        return $this->getBookings(['status' => BookingStatusesEnum::APPROVED] + $params);
    }

    /**
     * Get paginated bookings with status 'expired'.
     *
     * @link http://docs.bemyguest.apiary.io/#reference/bookings/get-bookings
     *
     * @param  array  $params
     * @return \BeMyGuest\SdkClient\Response
     */
    public function getExpiredBookings(array $params = [])
    {
        return $this->getBookings(['status' => BookingStatusesEnum::EXPIRED] + $params);
    }

    /**
     * Get paginated bookings with status 'rejected'.
     *
     * @link http://docs.bemyguest.apiary.io/#reference/bookings/get-bookings
     *
     * @param  array  $params
     * @return \BeMyGuest\SdkClient\Response
     */
    public function getRejectedBookings(array $params = [])
    {
        return $this->getBookings(['status' => BookingStatusesEnum::REJECTED] + $params);
    }

    /**
     * Get paginated bookings with status 'released'.
     *
     * @link http://docs.bemyguest.apiary.io/#reference/bookings/get-bookings
     *
     * @param  array  $params
     * @return \BeMyGuest\SdkClient\Response
     */
    public function getReleasedbookings(array $params = [])
    {
        return $this->getbookings(['status' => BookingStatusesEnum::RELEASED] + $params);
    }

    /**
     * Get paginated bookings with status 'refunded'.
     *
     * @link http://docs.bemyguest.apiary.io/#reference/bookings/get-bookings
     *
     * @param  array  $params
     * @return \BeMyGuest\SdkClient\Response
     */
    public function getRefundedbookings(array $params = [])
    {
        return $this->getbookings(['status' => BookingStatusesEnum::REFUNDED] + $params);
    }

    /**
     * Get paginated bookings.
     *
     * @link http://docs.bemyguest.apiary.io/#reference/bookings/get-bookings
     *
     * @param  array  $params
     * @return \BeMyGuest\SdkClient\Response
     */
    public function getBookings(array $params = [])
    {
        return $this->call($this->bookings, 'getBookings', [], $params);
    }


    /*
    |--------------------------------------------------------------------------
    | Wrappers for the API endpoints
    |
    | PRODUCTS
    |--------------------------------------------------------------------------
    */

    /**
     * Get paginated list of products.
     *
     * @link http://docs.bemyguest.apiary.io/#reference/products/products
     *
     * @return \BeMyGuest\SdkClient\Response
     */
    public function getProducts(array $params = [])
    {
        return $this->call($this->products, 'getProductsList', [], $params);
    }

    /**
     * Get single product details given UUID.
     *
     * @link http://docs.bemyguest.apiary.io/#reference/products/product
     *
     * @return \BeMyGuest\SdkClient\Response
     */
    public function getProduct($uuid, array $params = [])
    {
        return $this->call($this->products, 'getProduct', compact('uuid'), $params);
    }


    /*
    |--------------------------------------------------------------------------
    | Generic method for calling any of the SDK Controller::method()
    |--------------------------------------------------------------------------
    */

    /**
     * @param  \BeMyGuestAPIV1Lib\Models\CreateABookingRequest|\JsonSerializable|array  $data
     * @return array
     */
    protected function extractJson($data)
    {
        if ($data instanceof JsonSerializable) {
            $data = $data->jsonSerialize();
        } elseif (!is_array($data)) {
            $given = gettype($data);

            throw new InvalidArgumentException(
                "createBooking endpoint requires array or CreateABookingRequest instance, [{$given}] given."
            );
        }

        return (array) $data;
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

        $sdkResponse = call_user_func_array([$controller, $method], $required + $optional);

        $response = new Response(json_decode(json_encode($sdkResponse), true));

        if ($this->raw) {
            $this->raw = false;

            return $response;
        }

        return $response->get('data');
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
        $camelize = function ($word) {
            return lcfirst(str_replace(' ', '', ucwords(str_replace(['-', '_'], ' ', $word))));
        };

        return array_merge(
            $this->defaultParams($controller, $method),
            array_combine(array_map($camelize, array_keys($optional)), $optional)
        );
    }

    /**
     * Get default params for given SDK Controller::method along with default values.
     *
     * @param  string $class
     * @param  string $method
     * @return array
     */
    protected function defaultParams($class, $method = null)
    {
        $methodReflection = $method
                    ? new ReflectionMethod($class, $method)
                    : new ReflectionMethod($class);

        return collect($methodReflection->getParameters())
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
