<?php
/**
 * User: mohsenjalalian
 * Date: 10/24/16
 * Time: 3:13 PM
 */

namespace AppBundle\Twig;

/**
 * Class GoogleMapExtension
 * @package AppBundle\Twig
 */
class GoogleMapExtension extends \Twig_Extension
{
    /**
     * @var string
     */
    private $googleMapApi;

    /**
     * GoogleMapExtension constructor.
     * @param $googleMapApi
     */
    public function __construct($googleMapApi)
    {
        $this->googleMapApi = $googleMapApi;
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return [
            'getGoogleMapInitLink' => new \Twig_Function_Method($this, 'getGoogleMapInitLink'),
            'getGoogleMapEditLink' => new \Twig_Function_Method($this, 'getGoogleMapEditLink'),

        ];
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'google_Map';
    }

    /**
     * @return string
     */
    public function getGoogleMapInitLink()
    {
        return "https://maps.googleapis.com/maps/api/js?key="
        .$this->googleMapApi.
        "&language=fa&region=IR&libraries=places&callback=initMap";
    }

    /**
     * @return string
     */
    public function getGoogleMapEditLink()
    {
        return "https://maps.googleapis.com/maps/api/js?key="
        .$this->googleMapApi.
        "&language=fa&region=IR&libraries=places&callback=editMap";
    }
}