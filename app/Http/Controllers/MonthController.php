<?php

namespace App\Http\Controllers;
use App\Models\User;
use App\Models\Month;
use App\Models\Day;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;



class MonthController extends Controller
{
    public function edit($id){
        $month = Month::findOrFail($id);
        $days = $month->days;
        $firstDay = Day::where('month_id', $month->id)->orderBy('day')->first();
        if($firstDay->week_day != 1){
            
            $numberOfSpaces = ($firstDay->week_day + 6) % 7;
            $daysArray = $month->days->toArray();
            $emptyDay = new Day();
            $emptyDay->day = 44;
            $daysExtra = array_fill(0, $numberOfSpaces, $emptyDay);
            $daysExtra = array_merge($daysExtra, $daysArray);
        }else{
            $daysExtra = $month->days;
        }
        $user = Auth::user();
        return Inertia::render('Months/Edit', [
            'month' => $month,
            'days' => $days,
            'daysExtra' => $daysExtra,
            'user' => $user,
        ]);
    }
}
