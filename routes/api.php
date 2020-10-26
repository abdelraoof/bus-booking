<?php

use App\Models\Trip;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\ValidationException;

/*
|--------------------------------------------------------------------------
| Issuing API Tokens
|--------------------------------------------------------------------------
|
| API tokens that may be used to authenticate API requests.
| When making requests using API tokens,
| pass the token in the Authorization header as a Bearer token.
|
*/

Route::post('/token', function (Request $request) {
    $request->validate([
        'email' => 'required|string',
        'password' => 'required|string',
        'device_name' => 'required|string|max:255',
    ]);

    $user = User::where('email', $request->email)->first();

    if (! $user || ! Hash::check($request->password, $user->password)) {
        throw ValidationException::withMessages([
            'email' => ['The provided credentials are incorrect.'],
        ]);
    }

    $user->tokens()->where('name', $request->device_name)->delete();

    return $user->createToken($request->device_name)->plainTextToken;
});

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Ensure that incoming requests contain a valid API token header.
| With sanctum authentication guard.
|
*/

Route::middleware('auth:sanctum')->group(function () {

    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Get a list of available trips
    Route::get('/trips', function (Request $request) {
        // Abort if start station = end station
        if ($request->start_station == $request->end_station) {
            abort(400, 'Bad request.');
        }

        // Stations have to be valid
        $request->validate([
            'start_station' => 'required|exists:cities,slug',
            'end_station' => 'required|exists:cities,slug',
        ]);

        // Get all trips that cross over the two stations
        $trips = Trip::whereHas('line', function ($query) use ($request) {
                $query->whereHas('stations', function ($query) use ($request) {
                    $query->where('city_slug', $request->start_station);
                })->whereHas('stations', function ($query) use ($request) {
                    $query->where('city_slug', $request->end_station);
                });
            })
            // Exclude the already-left
            ->where('leaves_at', '>', now())
            // Get bus, captain, and stations with a proper order
            ->with(['line.stations' => function ($query) {
                $query->orderBy('order');
            }, 'bus', 'captain'])
            ->orderBy('leaves_at')
            ->get();

        // Make sure trips are in the right direction
        $trips = $trips->filter(function ($value, $key) use ($request) {
            $stationsOrder = $value->line->stations->pluck('order', 'city_slug');
            return $stationsOrder[$request->start_station] < $stationsOrder[$request->end_station];
        })->values();

        return $trips;
    });

    // Get a list of available seats to be booked for a trip
    Route::get('/trips/{trip}/available_seats', function (Trip $trip, Request $request) {
        // Abort if start station = end station
        if ($request->start_station == $request->end_station) {
            abort(400, 'Bad request.');
        }

        // Stations have to be valid
        $request->validate([
            'start_station' => 'required|exists:cities,slug',
            'end_station' => 'required|exists:cities,slug',
        ]);

        // Get bus, captain, rides, and stations with a proper order
        $trip->load(['line.stations' => function ($query) {
            $query->orderBy('order');
        }, 'bus', 'captain', 'rides']);

        // 1. Extract trip stations from line stations
        $tripStations = collect();
        $inTrip = false;
        $stationsExist = false;
        foreach ($trip->line->stations as $station) {
            if ($station->city_slug == $request->start_station) {
                $inTrip = true;
            }
            if ($inTrip) {
                $tripStations[] = $station;
            }
            if ($station->city_slug == $request->end_station) {
                $stationsExist = $inTrip;
                break;
            }
        }
        $tripStations = $tripStations->pluck('id');

        // Abort if this trip do not have the stations in the right direction
        if (! $stationsExist) {
            abort(400, 'Bad request.');
        }

        // 2. Get booked seats per station
        $bookedSeatsPerStation = $trip->rides->mapToGroups(function ($item, $key) {
            return [$item['station_id'] => $item['seat']];
        });

        // 3. Get seats that are not booked
        $availableSeats = collect(range(1, $trip->bus->capacity));
        foreach ($tripStations as $tripStation) {
            $availableSeats = $availableSeats->diff($bookedSeatsPerStation->get($tripStation));
        }

        return $availableSeats->values();
    });

    // Book one or more seats
    Route::post('/trips/{trip}/book', function (Trip $trip, Request $request) {
        // Abort if start station = end station
        if ($request->start_station == $request->end_station) {
            abort(400, 'Bad request.');
        }

        // Stations have to be valid
        $request->validate([
            'start_station' => 'required|exists:cities,slug',
            'end_station' => 'required|exists:cities,slug',
            'seats' => ['required', 'regex:/^\d+(,\d+)*$/'],
        ]);

        // Get bus, captain, rides, and stations with a proper order
        $trip->load(['line.stations' => function ($query) {
            $query->orderBy('order');
        }, 'bus', 'captain', 'rides']);

        // 1. Extract trip stations from line stations
        $tripStations = collect();
        $inTrip = false;
        $stationsExist = false;
        foreach ($trip->line->stations as $station) {
            if ($station->city_slug == $request->start_station) {
                $inTrip = true;
            }
            if ($inTrip) {
                $tripStations[] = $station;
            }
            if ($station->city_slug == $request->end_station) {
                $stationsExist = $inTrip;
                break;
            }
        }
        $tripStations = $tripStations->pluck('id');

        // Abort if this trip do not have the stations in the right direction
        if (! $stationsExist) {
            abort(400, 'Bad request.');
        }

        $seats = collect(explode(',', $request->seats))->unique()->sort()->values();
        // Abort if seat numbers are out of range
        if ($seats->first() < 1 || $seats->last() > $trip->bus->capacity) {
            abort(400, 'Bad request.');
        }

        // 2. Book requested seats
        $rides = [];
        foreach ($tripStations as $tripStation) {
            foreach ($seats as $seat) {
                $rides[] = [
                    'trip_id' => $trip->id,
                    'station_id' => $tripStation,
                    'seat' => $seat,
                ];
            }
        }
        DB::transaction(function () use ($rides) {
            auth()->user()->rides()->createMany($rides);
        });
        return response()->json(['message' => 'Success']);
    });

});
