<?php

use BeMyGuest\SdkClient\Client;
use BeMyGuest\SdkClient\Response;
use Illuminate\Support\Collection;
use BeMyGuestAPIV1Lib\Configuration;

describe('BeMyGuest API Client package - integration', function () {

    given('client', function () {
        Configuration::$BASEURI = 'https://apidemo.bemyguest.com.sg';
        return new Client('2c9971eac77c0b7b1a43b5263c3eff15aae58284');
    });

    after(function () {
        foreach ($this->bookings as $uuid) {
            $this->client->cancelBooking($uuid);
        }
    });

    $this->bookings = [];

    describe('Client', function () {

        it('filters bookings by status', function () {
            $first = $this->client->createBooking(sampleBookingData());
            $this->client->confirmBooking($first->uuid);
            $second = $this->client->createBooking(sampleBookingData());
            $this->client->confirmBooking($second->uuid);
            $response = $this->client->getWaitingBookings(['per_page' => 1000])->keyBy('uuid');
            expect($response->has($first->uuid))->toBeTruthy();
            expect($response->has($second->uuid))->toBeTruthy();

            $this->bookings[] = $first->uuid;
            $this->bookings[] = $second->uuid;
        });

        it('confirms a booking given uuid and sets its state to "waiting for approval"', function () {
            $booking = $this->client->createBooking(sampleBookingData());
            $response = $this->client->confirmBooking($uuid = $booking->get('uuid'));
            expect($response->get('status'))->toBe('waiting');
        });

        it('creates a booking given array of params', function () {
            $response = $this->client->createBooking(sampleBookingData());
            expect($uuid = $response->get('uuid'))->not->toBeNull();
            expect($response->get('status'))->toBe('reserved');
            $this->bookings[] = $uuid;
        });

        it('cancels the booking given uuid', function () {
            $response = $this->client->cancelBooking(array_shift($this->bookings));
            expect($response->get('status'))->toBe('cancelled');
        });

        it('fetches collection of Bookings', function () {
            $bookings = $this->client->getBookings();
            expect(count($bookings))->toBeGreaterThan(0);
        });

        it('fetches single Product', function () {
            $product = $this->client->getProduct('626ee399-1340-51c6-bd67-21183fed711f');
            expect($product)->toBeAnInstanceOf(Collection::class);
        });

        it('fetches collection of Products', function () {
            $products = $this->client->getProducts(['per_page' => 2]);
            expect(count($products))->toBe(2);
        });

        it('accepts snake_case and camelCase params to support API docs and SDK code notation', function () {
            expect(count($this->client->getProducts(['per_page' => 3])))->toBe(3);
            expect(count($this->client->getProducts(['perPage' => 1])))->toBe(1);
        });

        it('fetches API user config', function () {
            expect($config = $this->client->retrieveConfig())->toBeAnInstanceOf(Response::class);
            expect($config->get('timezone'))->toBe('Asia/Singapore');
            expect($config->get('user.email'))->toBe('apiclient@bemyguest.com.sg');
        });
    });

    describe('Response', function () {
        it('wraps SDK response as easy-to-use collection', function () {
            expect(new Response(new StdClass))->toBeAnInstanceOf(Collection::class);
        });
    });

});


function sampleBookingData()
{
    return [
        "salutation" => "Mr.",
        "firstName" => "Dale",
        "lastName" => "Da Silva",
        "email" => md5(rand(1,100)*time())."+dale@example.com",
        "phone" => "+6512345678",
        "message" => "Hello",
        "productTypeUuid" => "ff240586-fd51-5d5f-9eae-b7ce80fbe711",
        "pax" => 2,
        "children" => 0,
        "timeSlotUuid" => "862ef6e5-9cdb-508d-9ecc-b93401799d9c",
        "addons" => [],
        "arrivalDate" => "2016-06-05",
        "partnerReference"  => md5(rand(1,100)*time()),
        "usePromotion" => false
    ];
}
