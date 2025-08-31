<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class CalendarController extends Controller
{
    /**
     * Proxy Google Calendar ICS feed to avoid CORS issues
     */
    public function getCalendarFeed(Request $request)
    {
        try {
            // Get URL from request parameter or use default
            $calendarUrl = $request->query('url', 'https://calendar.google.com/calendar/ical/thilacoloma%40gmail.com/public/basic.ics');
            
            // Create a unique cache key based on the URL
            $cacheKey = 'google_calendar_feed_' . md5($calendarUrl);
            $cacheDuration = 30; // minutes
            
            $icsData = Cache::remember($cacheKey, $cacheDuration * 60, function () use ($calendarUrl) {
                $response = Http::timeout(10)->get($calendarUrl);
                
                if ($response->successful()) {
                    return $response->body();
                }
                
                throw new \Exception('Failed to fetch calendar data');
            });
            
            return response($icsData)
                ->header('Content-Type', 'text/calendar; charset=utf-8')
                ->header('Access-Control-Allow-Origin', '*')
                ->header('Access-Control-Allow-Methods', 'GET')
                ->header('Access-Control-Allow-Headers', 'Content-Type');
                
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to fetch calendar data',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
