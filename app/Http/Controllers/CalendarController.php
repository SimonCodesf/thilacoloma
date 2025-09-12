<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Statamic\Entries\Entry;

class CalendarController extends Controller
{
    /**
     * Proxy Google Calendar ICS feed to avoid CORS issues
     */
    public function getCalendarFeed()
    {
        try {
            // Get the Google Calendar URL from the kalender page content
            $kalenderPage = Entry::query()
                ->where('collection', 'pages')
                ->where('slug', 'kalender')
                ->first();
            $icsUrl = null;
            
            if ($kalenderPage) {
                $googleCalendarUrl = $kalenderPage->get('google_calendar_url');
                if ($googleCalendarUrl) {
                    // Convert embed URL to ICS URL if needed
                    if (strpos($googleCalendarUrl, '/embed?') !== false) {
                        // Extract calendar ID from embed URL
                        preg_match('/src=([^&]+)/', $googleCalendarUrl, $matches);
                        if (isset($matches[1])) {
                            $calendarId = urldecode($matches[1]);
                            $icsUrl = "https://calendar.google.com/calendar/ical/{$calendarId}/public/basic.ics";
                        }
                    } else {
                        // Assume it's already an ICS URL
                        $icsUrl = $googleCalendarUrl;
                    }
                }
            }
            
            // Fallback to default URL if no URL found in content
            if (!$icsUrl) {
                $icsUrl = 'https://calendar.google.com/calendar/ical/thilacoloma%40gmail.com/public/basic.ics';
            }
            
            // Create unique cache key based on the URL to handle URL changes
            $cacheKey = 'google_calendar_ics_feed_' . md5($icsUrl);
            $cacheDuration = 30; // minutes
            
            $icsData = Cache::remember($cacheKey, $cacheDuration * 60, function () use ($icsUrl) {
                $response = Http::timeout(10)->get($icsUrl);
                
                if ($response->successful()) {
                    return $response->body();
                }
                
                throw new \Exception('Failed to fetch calendar data from: ' . $icsUrl);
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
