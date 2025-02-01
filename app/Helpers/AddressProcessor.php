<?php

namespace App\Helpers;

use App\Data\GooglePlacesAddress;
use Exception;
use Illuminate\Support\Facades\Http;

class AddressProcessor
{
    /**
     * @throws Exception
     */
    public static function getAddressFromGps(string $lat, string $lng): GooglePlacesAddress
    {
        $response = Http::googlePlaces(['latlng' => "{$lat}, {$lng}"]);

        return self::processAddressData($response->json());
    }

    /**
     * @throws Exception
     */
    public static function getCoordinatesFromAddress(string $address): GooglePlacesAddress
    {
        $response = Http::googlePlaces(['address' => $address]);

        return self::processAddressData($response->json());
    }

    /**
     * @throws Exception
     */
    public static function getTimezone(string $lat, string $lng): string
    {
        $response = Http::timeZone($lat, $lng);

        if ($response->successful()) {
            return $response->json()['timeZoneId'];
        }
        throw new Exception($response->body());
    }

    /**
     * @throws Exception
     */
    private static function processAddressData(array $data): GooglePlacesAddress
    {
        return !empty($data['results'])
            ? new GooglePlacesAddress(
                address: $data['results'][0]['formatted_address'],
                lat: $data['results'][0]['geometry']['location']['lat'],
                lng: $data['results'][0]['geometry']['location']['lng']
            )
            : throw new Exception($data['error_message']);
    }
}
