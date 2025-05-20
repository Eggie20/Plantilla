<?php

namespace App\Helpers;

use App\Models\Office;

class OfficeFormatter
{
    /**
     * Cache of office mappings
     */
    private static $officeCache = null;

    /**
     * Format office name to ensure it's properly displayed using the full name from the database
     * 
     * @param string $office The office code or JSON string to format
     * @return string The properly formatted office name
     */
    public static function format($office)
    {
        if (empty($office)) {
            return '';
        }
        
        // Initialize cache if needed
        if (self::$officeCache === null) {
            self::$officeCache = [];
            
            // Load all offices from database
            $offices = Office::all();
            foreach ($offices as $officeObj) {
                // Store office names in uppercase
                self::$officeCache[strtolower($officeObj->code)] = strtoupper($officeObj->name);
            }
        }
        
        // If office is a JSON string, try to parse it
        if (is_string($office)) {
            try {
                $officeData = json_decode($office, true);
                if (isset($officeData['code'])) {
                    $office = $officeData['code'];
                }
            } catch (\Exception $e) {
                // If JSON parsing fails, treat it as a regular office code
            }
        }
        
        // Look up the office name in our cache
        $lowerCaseCode = strtolower(trim($office));
        if (isset(self::$officeCache[$lowerCaseCode])) {
            return self::$officeCache[$lowerCaseCode];
        }
        
        // If not found in cache, return the office code in uppercase
        return strtoupper($office);
    }

    /**
     * Format an office name to uppercase
     *
     * @param string $officeName The office name to format
     * @return string The formatted office name
     */
    public static function formatOfficeName($officeName)
    {
        if (empty($officeName)) {
            return '';
        }
        
        return strtoupper($officeName);
    }

    public static function formatJson($officeJson)
    {
        try {
            $office = json_decode($officeJson);
            
            // If we have an office object, use its name in uppercase
            if (isset($office->name)) {
                return strtoupper($office->name);
            }
            
            // If we have a code, format it in uppercase
            if (isset($office->code)) {
                return self::format($office->code);
            }
            
            // If we have an id, get the office from database and return name in uppercase
            if (isset($office->id)) {
                $officeRecord = \App\Models\Office::find($office->id);
                return $officeRecord ? strtoupper($officeRecord->name) : '';
            }
            
            // If no valid data, return empty string
            return '';
        } catch (\Exception $e) {
            return '';
        }
    }
}