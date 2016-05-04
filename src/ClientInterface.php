<?php

namespace BeMyGuest\SdkClient;

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
interface ClientInterface
{
    /**
     * Create a new booking.
     *
     * @link http://docs.bemyguest.apiary.io/#reference/bookings/create-a-booking
     *
     * @param  \BeMyGuestAPIV1Lib\Models\CreateABookingRequest|\JsonSerializable|array  $data
     * @return \BeMyGuest\SdkClient\Response
     */
    public function createBooking($data);

    /**
     * Get single booking details given UUID.
     *
     * @link http://docs.bemyguest.apiary.io/#reference/bookings/get-booking-status
     *
     * @return \BeMyGuest\SdkClient\Response
     */
    public function getBooking($uuid);

    /**
     * Alias of the getBooking method for consistency with SDK.
     *
     * @return \BeMyGuest\SdkClient\Response
     */
    public function getBookingStatus($uuid);

    /**
     * Check if provided data is valid for createBooking endpoint (dry-run).
     *
     * @link http://docs.bemyguest.apiary.io/#reference/bookings/check-if-booking-is-possible
     *
     * @param  \BeMyGuestAPIV1Lib\Models\CreateABookingRequest|\JsonSerializable|array  $data
     * @return \BeMyGuest\SdkClient\Response
     */
    public function checkBooking($data);

    /**
     * Cancel a booking given UUID.
     *
     * @link http://docs.bemyguest.apiary.io/#reference/bookings/update-booking-status
     *
     * @param  string $uuid
     * @return \BeMyGuest\SdkClient\Response
     */
    public function cancelBooking($uuid);

    /**
     * Confirm a booking given UUID. Changes status to 'waiting' for BeMyGuest approval.
     *
     * @link http://docs.bemyguest.apiary.io/#reference/bookings/update-booking-status
     *
     * @param  string $uuid
     * @return \BeMyGuest\SdkClient\Response
     */
    public function confirmBooking($uuid);

    /**
     * Resend confirmation email if it was already sent for given booking.
     *
     * @link http://docs.bemyguest.apiary.io/#reference/bookings/update-booking-status
     *
     * @param  string $uuid
     * @return \BeMyGuest\SdkClient\Response
     */
    public function resendConfirmation($uuid);

    /**
     * Update booking status given UUID.
     *
     * @link http://docs.bemyguest.apiary.io/#reference/bookings/update-booking-status
     *
     * @param  string $uuid
     * @param  string $status                One of statuses defined on \BeMyGuestAPIV1Lib\Models\BookingStatusEnum
     * @return \BeMyGuest\SdkClient\Response
     */
    public function updateBookingStatus($uuid, $status);

    /**
     * Get paginated bookings with status 'reserved'.
     *
     * @link http://docs.bemyguest.apiary.io/#reference/bookings/get-bookings
     *
     * @param  array  $params
     * @return \BeMyGuest\SdkClient\Response
     */
    public function getReservedbookings(array $params = []);

    /**
     * Get paginated bookings with status 'waiting'.
     *
     * @link http://docs.bemyguest.apiary.io/#reference/bookings/get-bookings
     *
     * @param  array  $params
     * @return \BeMyGuest\SdkClient\Response
     */
    public function getWaitingBookings(array $params = []);

    /**
     * Get paginated bookings with status 'cancelled'.
     *
     * @link http://docs.bemyguest.apiary.io/#reference/bookings/get-bookings
     *
     * @param  array  $params
     * @return \BeMyGuest\SdkClient\Response
     */
    public function getCancelledBookings(array $params = []);

    /**
     * Get paginated bookings with status 'approved'.
     *
     * @link http://docs.bemyguest.apiary.io/#reference/bookings/get-bookings
     *
     * @param  array  $params
     * @return \BeMyGuest\SdkClient\Response
     */
    public function getApprovedBookings(array $params = []);

    /**
     * Get paginated bookings with status 'expired'.
     *
     * @link http://docs.bemyguest.apiary.io/#reference/bookings/get-bookings
     *
     * @param  array  $params
     * @return \BeMyGuest\SdkClient\Response
     */
    public function getExpiredBookings(array $params = []);

    /**
     * Get paginated bookings with status 'rejected'.
     *
     * @link http://docs.bemyguest.apiary.io/#reference/bookings/get-bookings
     *
     * @param  array  $params
     * @return \BeMyGuest\SdkClient\Response
     */
    public function getRejectedBookings(array $params = []);

    /**
     * Get paginated bookings with status 'released'.
     *
     * @link http://docs.bemyguest.apiary.io/#reference/bookings/get-bookings
     *
     * @param  array  $params
     * @return \BeMyGuest\SdkClient\Response
     */
    public function getReleasedbookings(array $params = []);

    /**
     * Get paginated bookings with status 'refunded'.
     *
     * @link http://docs.bemyguest.apiary.io/#reference/bookings/get-bookings
     *
     * @param  array  $params
     * @return \BeMyGuest\SdkClient\Response
     */
    public function getRefundedbookings(array $params = []);

    /**
     * Get paginated bookings.
     *
     * @link http://docs.bemyguest.apiary.io/#reference/bookings/get-bookings
     *
     * @param  array  $params
     * @return \BeMyGuest\SdkClient\Response
     */
    public function getBookings(array $params = []);

    /**
     * Get paginated list of products.
     *
     * @link http://docs.bemyguest.apiary.io/#reference/products/products
     *
     * @return \BeMyGuest\SdkClient\Response
     */
    public function getProducts(array $params = []);

    /**
     * Get single product details given UUID.
     *
     * @link http://docs.bemyguest.apiary.io/#reference/products/product
     *
     * @return \BeMyGuest\SdkClient\Response
     */
    public function getProduct($uuid, array $params = []);

    /**
     * Get configuration settings for the API user.
     *
     * @link http://docs.bemyguest.apiary.io/#reference/0/config
     *
     * @return \BeMyGuest\SdkClient\Response
     */
    public function getConfig();

    /**
     * Alias of the getConfig method for consistency with SDK.
     *
     * @return \BeMyGuest\SdkClient\Response
     */
    public function retrieveConfig();
}
