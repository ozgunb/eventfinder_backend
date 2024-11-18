<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Facades\JWTAuth;

class EventsController extends Controller
{
    public function index(Request $request)
    {
        Request::validate([
            'token' => ['required'],
            'latitude' => ['required'],
            'longitude' => ['required'],
            'distance' => ['required'],
        ]);

        $user = JWTAuth::authenticate(Request::get("token"));

        $lat = Request::get("latitude");
        $lng = Request::get("longitude");
        $distance = 550; //Request::get("distance");;

        //$results = DB::select(DB::raw('SELECT id, ( 3959 * acos( cos( radians(' . $lat . ') ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(' . $lng . ') ) + sin( radians(' . $lat .') ) * sin( radians(latitude) ) ) ) AS distance, name, location, description, event_date, event_time  FROM events HAVING distance < ' . $distance . ' ORDER BY distance') );

        $results = Event::select("id", "name", "location", "description", "event_date", "event_time", "latitude", "longitude")->get();

        return response()->json(['success' => true, 'events' => $results]);
    }

    public function search(Request $request)
    {
        Request::validate([
            'token' => ['required'],
            'key' => ['nullable'],
        ]);

        $user = JWTAuth::authenticate(Request::get("token"));

        $key = Request::get("key");

        if($key !== null && $key !== "") {
            $results = Event::select("id", "name", "location", "description", "event_date", "event_time", "latitude", "longitude")->where("name", 'like', '%'. Request::get("key"). '%')->get();
        } else {
            $results = Event::select("id", "name", "location", "description", "event_date", "event_time", "latitude", "longitude")->get();
        }

        return response()->json(['success' => true, 'events' => $results]);
    }

    public function store(Request $request) {
        Request::validate([
            'token' => ['required'],
            'user_id' => ['required'],
            'name' => ['required'],
            'location' => ['required'],
            'description' => ['nullable'],
            'latitude' => ['required'],
            'longitude' => ['required'],
            'event_date' => ['required'],
            'event_time' => ['required'],
        ]);

        $user = JWTAuth::authenticate(Request::get("token"));

        $lat = Request::get("latitude");
        $latStr = strval($lat);

        $event = Event::create([
            'user_id' => Request::get("user_id"),
            'name' => Request::get("name"),
            'location' => Request::get("location"),
            'description' => Request::get("description"),
            'latitude' => $lat,
            'longitude' => strval(Request::get("longitude")),
            'event_date' => Request::get("event_date"),
            'event_time' => Request::get("event_time"),
        ]);

        return response()->json(['success' => true, 'event' => $event]);
    }
}
