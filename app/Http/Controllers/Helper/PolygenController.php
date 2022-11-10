<?php

namespace App\Http\Controllers\Helper;


use App\Models\Configuration;
use App\Models\CountryArea;
use App\Models\GeofenceArea;
use App\Models\OutstationPackage;
use Illuminate\Http\Request;
use Schema;
use App\Http\Controllers\Controller;

class PolygenController extends Controller
{
    public $lat;
    public $long;

    public function __construct($lat = null, $long = null)
    {
        $this->lat = $lat;
        $this->long = $long;
    }

    function pointInPolygon($p, $polygon)
    {
        $c = 0;
        $p1 = $polygon[0];
        $n = count($polygon);

        for ($i = 1; $i <= $n; $i++) {
            $p2 = $polygon[$i % $n];
            if ($p->long > min($p1->long, $p2->long)
                && $p->long <= max($p1->long, $p2->long)
                && $p->lat <= max($p1->lat, $p2->lat)
                && $p1->long != $p2->long) {
                $xinters = ($p->long - $p1->long) * ($p2->lat - $p1->lat) / ($p2->long - $p1->long) + $p1->lat;
                if ($p1->lat == $p2->lat || $p->lat <= $xinters) {
                    $c++;
                }
            }
            $p1 = $p2;
        }
        return $c % 2 != 0;
    }

    public function CheckArea($lat, $long, $coordinates)
    {
        $polygon = array();
        $coordinates = json_decode($coordinates, true);
        foreach ($coordinates as $coordinate) {
            $latitude = $coordinate['latitude'];
            $longitude = $coordinate['longitude'];
            $polygon[] = new PolygenController($latitude, $longitude);
        }
        $check = (new self)->pointInPolygon(new PolygenController($lat, $long), $polygon);
        if (!empty($check)) {
            return true;
        }
        return false;
    }

    public static function OutstationArea($lat,$long,$merchant_id)
    {
        $areas = OutstationPackage::where([['merchant_id', '=', $merchant_id],['area_coordinates','!=',NULL],['status','=',1]])->get();
        if (!empty($areas->toArray())) {
            $areas = $areas->toArray();
            foreach ($areas as $area) {
                $polygon = array();
                $coordinates_JSON = $area['area_coordinates'];
                $coordinates = json_decode($coordinates_JSON, true);
                foreach ($coordinates as $coordinate) {
                    $latitude = $coordinate['latitude'];
                    $longitude = $coordinate['longitude'];
                    $polygon[] = new PolygenController($latitude, $longitude);
                }
                $check = (new self)->pointInPolygon(new PolygenController($lat, $long), $polygon);
                if (!empty($check)) {
                    return $area;
                }
            }
            return false;
        }
        return false;
    }

    public static function Area($lat, $long, $merchant_id)
    {
        $areas = CountryArea::where([['merchant_id', '=', $merchant_id],['is_geofence','=',2],['status','=',1]])->get();
        if (!empty($areas->toArray())) {
            $areas = $areas->toArray();
            foreach ($areas as $area) {
                $polygon = array();
                $coordinates_JSON = $area['AreaCoordinates'];
                $coordinates = json_decode($coordinates_JSON, true);
                foreach ($coordinates as $coordinate) {
                    $latitude = $coordinate['latitude'];
                    $longitude = $coordinate['longitude'];
                    $polygon[] = new PolygenController($latitude, $longitude);
                }
                $check = (new self)->pointInPolygon(new PolygenController($lat, $long), $polygon);
                if (!empty($check)) {
                    return $area;
                }
            }
            return false;
        }
        return false;
    }

    public static function DuplicateArea($coordinates, $merchant_id, $is_geofence = 2)
    {
        $areas = CountryArea::where([['merchant_id', '=', $merchant_id],['is_geofence','=',$is_geofence]])->get();
        if (!empty($areas->toArray())) {
            $coordinatesArray = json_decode($coordinates, true);
            $areas = $areas->toArray();
            foreach ($areas as $area) {
                $polygon = array();
                $coordinates_JSON = $area['AreaCoordinates'];
                $area_coordinates = json_decode($coordinates_JSON, true);
                foreach ($area_coordinates as $coordinate) {
                    $latitude = $coordinate['latitude'];
                    $longitude = $coordinate['longitude'];
                    $polygon[] = new PolygenController($latitude, $longitude);
                }
                foreach ($coordinatesArray as $coordinate) {
                    $lat = $coordinate['latitude'];
                    $long = $coordinate['longitude'];
                    $check = (new self)->pointInPolygon(new PolygenController($lat, $long), $polygon);
                    if (!empty($check)) {
                        return $area;
                    }
                }
                $polygon = array();
                $area_coordinates = $coordinatesArray;
                foreach ($area_coordinates as $coordinate) {
                    $latitude = $coordinate['latitude'];
                    $longitude = $coordinate['longitude'];
                    $polygon[] = new PolygenController($latitude, $longitude);
                }
                $coordinates = json_decode($area['AreaCoordinates'], true);
                foreach ($coordinates as $coordinate) {
                    $lat = $coordinate['latitude'];
                    $long = $coordinate['longitude'];
                    $check = (new self)->pointInPolygon(new PolygenController($lat, $long), $polygon);
                    if (!empty($check)) {
                        return $area;
                    }
                }
            }
        }
        return false;
    }

    public static function DuplicateAreaEdit($coordinates, $merchant_id, $id, $is_geofence = 2)
    {
        $areas = CountryArea::where([['merchant_id', '=', $merchant_id], ['id', '!=', $id], ['is_geofence','=',$is_geofence]])->get();
        if (!empty($areas->toArray())) {
            $coordinatesArray = json_decode($coordinates, true);
            $areas = $areas->toArray();
            foreach ($areas as $area) {
                $polygon = array();
                $coordinates_JSON = $area['AreaCoordinates'];
                $area_coordinates = json_decode($coordinates_JSON, true);
                foreach ($area_coordinates as $coordinate) {
                    $latitude = $coordinate['latitude'];
                    $longitude = $coordinate['longitude'];
                    $polygon[] = new PolygenController($latitude, $longitude);
                }
                foreach ($coordinatesArray as $coordinate) {
                    $lat = $coordinate['latitude'];
                    $long = $coordinate['longitude'];
                    $check = (new self)->pointInPolygon(new PolygenController($lat, $long), $polygon);
                    if (!empty($check)) {
                        return $area;
                    }
                }
                $polygon = array();
                $area_coordinates = $coordinatesArray;
                foreach ($area_coordinates as $coordinate) {
                    $latitude = $coordinate['latitude'];
                    $longitude = $coordinate['longitude'];
                    $polygon[] = new PolygenController($latitude, $longitude);
                }
                $coordinates = json_decode($area['AreaCoordinates'], true);
                foreach ($coordinates as $coordinate) {
                    $lat = $coordinate['latitude'];
                    $long = $coordinate['longitude'];
                    $check = (new self)->pointInPolygon(new PolygenController($lat, $long), $polygon);
                    if (!empty($check)) {
                        return $area;
                    }
                }
            }
        }
        return false;
    }

//    public static function CentroidOfPolygon($coordinates)
//    {
//        $coordinates = json_decode($coordinates, true);
//        $cx = 0;
//        $cy = 0;
//        $area = 0;
//        for ($i=0; $i<sizeof($coordinates); $i++) {
//            $lat = $coordinates[$i]['latitude'];
//            $long = $coordinates[$i]['longitude'];
//            $next_lat = $coordinates[($i+1) % sizeof($coordinates)]['latitude'];
//            $next_long = $coordinates[($i+1) % sizeof($coordinates)]['longitude'];
//
//            $area += ($lat x $next_long) - ($long x $next_lat);
//
//            $p = ($lat x $next_long) - ($long x $next_lat);
//            $cx += ($lat + $next_lat) * $p;
//            $cy += ($long + $next_long) * $p;
//        }
//        $area = $area / 2;
//        $lat = $cx / ( 6 * $area);
//        $long = $cy / ( 6 * $area);
//
//        return ['latitude' => $lat, 'longitude' => $long];
//    }
}