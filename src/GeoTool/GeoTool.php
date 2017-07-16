<?php
namespace GeoTool;

use \GoogleMapsGeocoder;
use Location\Coordinate;
use Location\Distance\Vincenty;

class GeoTool {
    private $geocoder;
    private $distance_helper;

    function __construct()
    {
        $this->geocoder = new GoogleMapsGeocoder();
        $this->distance_helper = new Vincenty();
        return $this;
    }
    public function setAddress($address)
    {
        $this->geocoder->setAddress($address);
        return $this;
    }
    public function getCoordinates()
    {
        $result =  $this->geocoder->geocode();
        return @$result['results'][0]['geometry']['location'];
    }
    public function createCoordinate($a)
    {
        return new Coordinate($a['lat'], $a['lng']);
    }
    public function getDistance(array $a, array $b)
    {
        $a_coord = $this->createCoordinate($a);
        $b_coord = $this->createCoordinate($b);
       return $this->distance_helper->getDistance($a_coord, $b_coord);
    }

    private function calculateDistance(Coordinate $a_coord, Coordinate $b_coord)
    {
       return $this->distance_helper->getDistance($a, $b);
    }

    public function getClosestFromPool(array $a, array $abc)
    {
        $distances = array();
        foreach($abc as $key => $coordinate){
            $distances[$key] = $this->getDistance($a, $coordinate);
        }
        $closest = array_keys($distances, min($distances));
        
        return $closest[0];
    }
    public function getOrderedByClosestFromPool(array $a, array $abc)
    {
        foreach($abc as $key => $b){
            $abc[$key]['distance'] = $this->getDistance($a, $b);
        }
        usort($abc, function($a, $b) {
            return $a['distance'] - $b['distance'];
        });
        return $abc;
    }
}