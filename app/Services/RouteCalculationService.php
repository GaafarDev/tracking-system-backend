<?php

namespace App\Services;

class RouteCalculationService
{
    public static function calculateDistance($stops)
    {
        if (count($stops) < 2) {
            return ['distance' => 0, 'duration' => 0];
        }

        $totalDistance = 0;
        
        for ($i = 0; $i < count($stops) - 1; $i++) {
            $lat1 = floatval($stops[$i]['lat']);
            $lng1 = floatval($stops[$i]['lng']);
            $lat2 = floatval($stops[$i + 1]['lat']);
            $lng2 = floatval($stops[$i + 1]['lng']);
            
            $totalDistance += self::haversineDistance($lat1, $lng1, $lat2, $lng2);
        }

        return [
            'distance' => round($totalDistance, 2),
            'duration' => round($totalDistance * 2) // Rough estimate: 2 min per km
        ];
    }

    private static function haversineDistance($lat1, $lng1, $lat2, $lng2)
    {
        $earthRadius = 6371; // Earth's radius in kilometers

        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);

        $a = sin($dLat/2) * sin($dLat/2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLng/2) * sin($dLng/2);

        $c = 2 * atan2(sqrt($a), sqrt(1-$a));

        return $earthRadius * $c;
    }
}