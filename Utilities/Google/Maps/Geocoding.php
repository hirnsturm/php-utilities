<?php

namespace Sle\Utilities\Google\Maps;

/**
 * Geocoding by Google Maps API V3
 *
 * https://developers.google.com/maps/documentation/javascript/tutorial
 *
 * @author Steve Lenz <kontakt@steve-lenz.de>
 */
class Geocoding
{

    /**
     * Base URL for Google Maps API
     *
     * @var string
     */
    private static $baseUrl = 'https://maps.googleapis.com/maps/api/geocode/json';

    /**
     * Geocoding with Google GeoCoding API V3
     *
     * https://developers.google.com/maps/documentation/geocoding/?hl=de
     *
     * @param string $city
     * @param string $postcode
     * @param string $street
     * @param string $nr
     * @return array
     */
    public static function getGeoCoding($city, $postcode, $street, $nr)
    {
        $urlData = '&address=' . urlencode($city) . ',' . urlencode($postcode)
            . ',' . urlencode($street) . urlencode(' ' . $nr);

        $jsonGoogle = file_get_contents(self::$baseUrl . '?' . $urlData);

        return json_decode($jsonGoogle, true);
    }

    /**
     * Reverse geocoding by coordinates
     *
     * https://developers.google.com/maps/documentation/geocoding/?hl=de#ReverseGeocoding
     *
     * @param float $lat
     * @param float $lng
     * @return array
     */
    public static function reverseGeoCoding($lat, $lng)
    {
        $requestUrl = self::$baseUrl . '?latlng=' . $lat . ',' . $lng;

        $jsonGoogle = file_get_contents($requestUrl);

        return json_decode($jsonGoogle, true);
    }

}
