<?php

namespace BeMyGuest\SdkClient;

abstract class Model
{
    /**
     * WIP
     *
     * @todo convert raw response object into the domain models generated by SDK.
     */
    public static function convert(Response $response)
    {
        return $response->get('data');
    }
}
