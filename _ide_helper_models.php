<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $user_id
 * @property string $license_number
 * @property string $phone_number
 * @property string|null $address
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Incident> $incidents
 * @property-read int|null $incidents_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Location> $locations
 * @property-read int|null $locations_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Schedule> $schedules
 * @property-read int|null $schedules_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\SosAlert> $sosAlerts
 * @property-read int|null $sos_alerts_count
 * @property-read \App\Models\User $user
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Vehicle> $vehicles
 * @property-read int|null $vehicles_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Driver newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Driver newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Driver query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Driver whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Driver whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Driver whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Driver whereLicenseNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Driver wherePhoneNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Driver whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Driver whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Driver whereUserId($value)
 */
	class Driver extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $driver_id
 * @property string $type
 * @property string $description
 * @property string|null $latitude
 * @property string|null $longitude
 * @property string|null $photo_path
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $resolved_at
 * @property string|null $resolution_notes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Driver $driver
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Incident newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Incident newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Incident query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Incident whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Incident whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Incident whereDriverId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Incident whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Incident whereLatitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Incident whereLongitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Incident wherePhotoPath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Incident whereResolutionNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Incident whereResolvedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Incident whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Incident whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Incident whereUpdatedAt($value)
 */
	class Incident extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $driver_id
 * @property int $vehicle_id
 * @property string $latitude
 * @property string $longitude
 * @property string|null $speed
 * @property string|null $heading
 * @property \Illuminate\Support\Carbon $recorded_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Driver $driver
 * @property-read \App\Models\Vehicle $vehicle
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Location newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Location newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Location query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Location whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Location whereDriverId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Location whereHeading($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Location whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Location whereLatitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Location whereLongitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Location whereRecordedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Location whereSpeed($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Location whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Location whereVehicleId($value)
 */
	class Location extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $user_id
 * @property string $title
 * @property string $message
 * @property string $type
 * @property bool $is_read
 * @property \Illuminate\Support\Carbon|null $read_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification whereIsRead($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification whereMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification whereReadAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification whereUserId($value)
 */
	class Notification extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property array<array-key, mixed>|null $waypoints
 * @property array<array-key, mixed>|null $stops
 * @property float|null $distance_km
 * @property int|null $estimated_duration_minutes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Schedule> $schedules
 * @property-read int|null $schedules_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Route newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Route newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Route query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Route whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Route whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Route whereDistanceKm($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Route whereEstimatedDurationMinutes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Route whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Route whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Route whereStops($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Route whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Route whereWaypoints($value)
 */
	class Route extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $route_id
 * @property int $driver_id
 * @property int $vehicle_id
 * @property string $departure_time
 * @property string $arrival_time
 * @property string $day_of_week
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Driver $driver
 * @property-read \App\Models\Route $route
 * @property-read \App\Models\Vehicle $vehicle
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Schedule newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Schedule newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Schedule query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Schedule whereArrivalTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Schedule whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Schedule whereDayOfWeek($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Schedule whereDepartureTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Schedule whereDriverId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Schedule whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Schedule whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Schedule whereRouteId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Schedule whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Schedule whereVehicleId($value)
 */
	class Schedule extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $driver_id
 * @property string $latitude
 * @property string $longitude
 * @property string|null $message
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $responded_at
 * @property \Illuminate\Support\Carbon|null $resolved_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Driver $driver
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SosAlert newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SosAlert newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SosAlert query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SosAlert whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SosAlert whereDriverId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SosAlert whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SosAlert whereLatitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SosAlert whereLongitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SosAlert whereMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SosAlert whereResolvedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SosAlert whereRespondedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SosAlert whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SosAlert whereUpdatedAt($value)
 */
	class SosAlert extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $two_factor_secret
 * @property string|null $two_factor_recovery_codes
 * @property string|null $two_factor_confirmed_at
 * @property string|null $remember_token
 * @property int|null $current_team_id
 * @property string|null $profile_photo_path
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Driver|null $driver
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read string $profile_photo_url
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCurrentTeamId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereProfilePhotoPath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereTwoFactorConfirmedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereTwoFactorRecoveryCodes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereTwoFactorSecret($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 */
	class User extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $plate_number
 * @property string $model
 * @property int $capacity
 * @property string $type
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Driver> $drivers
 * @property-read int|null $drivers_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Location> $locations
 * @property-read int|null $locations_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Schedule> $schedules
 * @property-read int|null $schedules_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehicle newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehicle newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehicle query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehicle whereCapacity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehicle whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehicle whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehicle whereModel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehicle wherePlateNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehicle whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehicle whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehicle whereUpdatedAt($value)
 */
	class Vehicle extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $location
 * @property string $condition
 * @property string $description
 * @property string|null $latitude
 * @property string|null $longitude
 * @property string|null $temperature
 * @property bool $affects_routes
 * @property \Illuminate\Support\Carbon|null $valid_until
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WeatherUpdate newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WeatherUpdate newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WeatherUpdate query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WeatherUpdate whereAffectsRoutes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WeatherUpdate whereCondition($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WeatherUpdate whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WeatherUpdate whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WeatherUpdate whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WeatherUpdate whereLatitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WeatherUpdate whereLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WeatherUpdate whereLongitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WeatherUpdate whereTemperature($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WeatherUpdate whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WeatherUpdate whereValidUntil($value)
 */
	class WeatherUpdate extends \Eloquent {}
}

