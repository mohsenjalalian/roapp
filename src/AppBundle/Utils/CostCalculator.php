<?php

namespace AppBundle\Utils;
use AppBundle\Entity\Address;
use GuzzleHttp\Client;

/**
 *class CostCalculator
 */
class CostCalculator
{
    const TOP_LEVEL_STATUS_OK = 'OK';
    const TOP_LEVEL_STATUS_INVALID_REQUEST = 'INVALID_REQUEST';
    const TOP_LEVEL_STATUS_MAX_ELEMENTS_EXCEEDED = 'MAX_ELEMENTS_EXCEEDED';
    const TOP_LEVEL_STATUS_OVER_QUERY_LIMIT = 'OVER_QUERY_LIMIT';
    const TOP_LEVEL_STATUS_REQUEST_DENIED = 'REQUEST_DENIED';
    const TOP_LEVEL_STATUS_UNKNOWN_ERROR = 'UNKNOWN_ERROR';

    const ELEMENT_LEVEL_STATUS_OK = 'OK';
    const ELEMENT_LEVEL_STATUS_NOT_FOUND = 'NOT_FOUND';
    const ELEMENT_LEVEL_STATUS_ZERO_RESULTS = 'ZERO_RESULTS';

    /**
     * @var array
     */
    private $timeMapping;

    /**
     * @var array
     */
    private $valueMapping;

    /**
     * @var array
     */
    private $googleMapSettings;
    /**
     * CostCalculator constructor.
     *
     * @param array $timeMapping
     * @param array $valueMapping
     * @param array $googleMapSettings
     */
    public function __construct($timeMapping, $valueMapping, $googleMapSettings)
    {
        $this->timeMapping = $timeMapping;
        $this->valueMapping = $valueMapping;
        $this->googleMapSettings = $googleMapSettings;
    }

    /**
     * calculates the cost for shipment
     * 
     * @param Address   $origin        Source address
     * @param Address   $destination   Destination address
     * @param int       $shipmentValue Value of shipment
     * @param \DateTime $orderDateTime Order date and time
     *
     * @throws \Exception
     *
     * @return int
     */
    public function getCost(Address $origin, Address $destination, $shipmentValue, \DateTime $orderDateTime) {
        $client = new Client(['base_uri' => $this->googleMapSettings['distance_matrix']['url']]);
        $result = $client->request(
            'GET',
            $this->googleMapSettings['distance_matrix']['output_format'],
            [
                'query' => [
                    'origins' => $this->formatLatLong($origin),
                    'destinations' => $this->formatLatLong($destination),
                    'key' => $this->googleMapSettings['distance_matrix']['api_key']
                ]
            ]
        )->getBody()
            ->getContents();
        $result = json_encode($result);

        $topLevelStatus = $result['status'];
        $elementLevelStatus = $result['rows'][0]['elements'][0]['status'];
        $distance = null;
        $duration = null;
        if ($topLevelStatus == self::TOP_LEVEL_STATUS_OK & $elementLevelStatus == self::ELEMENT_LEVEL_STATUS_OK) {
            $distance = $result['rows'][0]['elements'][0]['distance']['value']; // In meter
            $duration = $result['rows'][0]['elements'][0]['duration']['value']; // In second
        } else {
            throw new \Exception('Invalid map response.');
        }

        // @todo Write cost calculation algorithm right here and return

        return 1000;
    }

    /**
     * format address string
     *
     * @param Address $address Address
     *
     * @throws \Exception
     *
     * @return string
     */
    private function formatLatLong(Address $address) {
        $lat = $address->getLatitude();
        $long = $address->getLongitude();

        if ($lat != null & $long!= null) {
            return sprintf('%s,%s', $lat, $long);
        }

        throw new \Exception('Null value for Latitude/Longitude');
    }
    
    

}