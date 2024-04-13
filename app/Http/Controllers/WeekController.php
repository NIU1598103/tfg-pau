<?php

namespace App\Http\Controllers;
use App\Models\User;
use App\Models\Month;
use App\Models\Day;
use Inertia\Inertia;
use Illuminate\Http\Request;

class WeekController extends Controller
{
    public function index(){
        $days = Day::all();
        $guardians = User::where('transplant', true)->get();
        $weeks = [];
    
        $firstWeek = [];
        foreach ($days as $day) {
            $firstWeek[] = $day;
            if ($day->week_day === 0) { // la setmana acaba quan és diumenge (0)
                $weeks[] = $firstWeek;
                break;
            }
        }
        // dd($firstWeek[0]);
        // la resta de setmanes, de 7 en 7
        $remainingDays = collect($days)->slice(count($firstWeek)); 
        $remainingDaysChunks = $remainingDays->chunk(7);
        foreach ($remainingDaysChunks as $week) {
            $weeks[] = $week->toArray();
        }

        return Inertia::render('Weeks/Index', [
            'weeks' => $weeks,
            'guardians' => $guardians,
        ]);
    }

    public function update_guard_transplant(Request $request){
        $decodedWeek = json_decode(urldecode($request->week), true);
        $guardian = User::find($request->guardian);
        $days = [];
        foreach($decodedWeek as $day){
            $days[] = $day;
            $dayObject = Day::find($day["id"]);
            if($guardian === null){
                $dayObject->id_guardTransplant = null;
                $dayObject->name_guardTransplant = null;
            }
            else{
                $dayObject->id_guardTransplant = $guardian->id;
                $dayObject->name_guardTransplant = $guardian->name;
            }
            $dayObject->save();
            // dd($dayObject);
        }

        // dd($days[0]["id"]);
    }
}
