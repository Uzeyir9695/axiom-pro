<?php

namespace App\Traits;

use App\Models\AircraftAd;
use App\Models\Boat;
use App\Models\BuySell;
use App\Models\CarAd;
use App\Models\HomeGarden;
use App\Models\Motor;
use App\Models\MotorcycleAd;
use App\Models\Property;
use App\Models\Rent;
use App\Models\Service;
use App\Models\Ship;
use App\Models\Tech;
use InvalidArgumentException;

trait ModelResolver
{
    /**
     * Get the model class based on the route parameter.
     *
     * @param string $modelRoute
     */
    private static function getModel(string $modelRoute)
    {
        switch ($modelRoute) {
            case 'aircraft':
                return AircraftAd::class;
            case 'ships':
                return Ship::class;
            case 'boats':
                return Boat::class;
            case 'cars':
                return CarAd::class;
            case 'motorcycles':
                return MotorcycleAd::class;
            case 'buy-sell':
                return BuySell::class;
            case 'rents':
                return Rent::class;
            case 'motors':
                return Motor::class;
            case 'properties':
                return Property::class;
            case 'techs':
                return Tech::class;
            case 'home-gardens':
                return HomeGarden::class;
            case 'services':
                return Service::class;
            default:
                throw new InvalidArgumentException("Invalid route: {$modelRoute}");
        }
    }

    private static function getModelsClasses(): array
    {
        $models = [
            'aircraft' => AircraftAd::class,
            'ships' => Ship::class,
            'boats' => Boat::class,
            'cars' => CarAd::class,
            'motorcycles' => MotorcycleAd::class,
            'buy_sell' => BuySell::class,
            'motors' => Motor::class,
            'teches' => Tech::class,
            'home_gardens' => HomeGarden::class,
            'properties' => Property::class,
            'rent' => Rent::class,
            'services' => Service::class,
        ];

        return $models;
    }
}
