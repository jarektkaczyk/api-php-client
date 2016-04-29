<?php

namespace BeMyGuest\SdkClient;

interface ClientInterface
{
    public function getProducts(array $params = []);
    public function getProduct($uuid, array $params = []);
}
