<?php

namespace App\Http\Controllers; 

use Illuminate\Http\Request;
use App\Models\Personnel; // Import the Personnel model
use App\Helpers\RomanNumerals;
use App\Http\Controllers\Controller;

class ReportController extends Controller
{
    public function show()
    {
        $personnels = Personnel::all(); // Fetch all personnel data
        return view('pages.plantilla.generateReport', compact('personnels'));
    }
}

$romanNumber = RomanNumerals::toRoman(5);
echo $romanNumber; // Output: V