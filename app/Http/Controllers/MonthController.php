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
    
    public function consult($id){
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
        $blocked_days_decode = [];
        if (!empty($user->blocked_days)) {
            $blocked_days_decode = json_decode($user->blocked_days, true);
            if (!is_array($blocked_days_decode)) {
                $blocked_days_decode = [];
            }
        }

        if(array_key_exists($month->name, $blocked_days_decode)){

            // dd($blocked_days_decode[$month->name]);
            $daysBlocked = $blocked_days_decode[$month->name];
        }else{
            $daysBlocked = [];
        }
        // dd($daysBlocked);
        return Inertia::render('Months/Consult', [
            'month' => $month,
            'days' => $days,
            'daysExtra' => $daysExtra,
            'user' => $user,
            'daysBlocked' => $daysBlocked,
        ]);
    }

    public function index(){
        $users = User::all();
        $months = Month::all();
        return Inertia::render('Months/Organize', [
            'users' => $users,
            'months' => $months,
        ]);
    }

    public function clean_guards(Request $request){
        $month = Month::find($request->month);
        $days = $month->days;
        foreach($days as $day){
            // dd($days);
            $day->user_id = null;
            $day->save();
        }
    }

    public function organize_guards(Request $request){
        $month = Month::find($request->month);
        $days = $month->days;
        $users = User::where('verified', true)->get();
         dd($users);
        //  dd($days);
        // foreach($days as $day){
        //     // dd($days);
        //     $day->user_id = null;
        //     $day->save();
        // }
        $residents4 = [];
        $residents3_2 = [];
        $residents1 = [];
        $adjunts = [];
        foreach($users as $user){
            if(strpos($user->type, 'Adjunto') === false) {
                switch($user->type){
                    case 'R4':
                        $residents4[] = $user;
                        break;
                    case 'R3':
                        $residents3_2[] = $user;
                        break;
                    case 'R2':
                        $residents3_2[] = $user;
                        break;
                    case 'R1':
                        $residents1[] = $user;
                        break;
                    default:
                    break;
                }
            }
            else{
                $adjunts[] = $user;
            }
        }
        // dd($residents3_2);

        //array que farem servir per als Adjunts Junior
        $guards_r1 = [];

        foreach($residents1 as $user)
        {
            $blocked_days_json = json_decode($user->blocked_days);
            if($blocked_days_json === null){
                dd($user);
            }
            $blocked_days = $blocked_days_json->{$month->name};
            $total_guards = $user->total_guards;
            $weekend_guards = $user->weekend_guards;
            $guards = [];

            foreach($days as $day)
            {
                if($total_guards === 0){
                    break; 
                }
                if($day->week_day === 6) //nomes guardia dissabtes
                {
                    if(!in_array($day->day, $blocked_days) && !in_array($day->day, $guards) && $day->user_id === null)
                    {
                        $day->user_id = $user->id;
                        $day->save();

                        $new_blocked_days = [];
                        $guards[] = $day->day;
                        $guards_r1[] = $day->day;
                        $total_guards -= 1;
                        if($day->day !== 1){
                            $new_blocked_days[] = $day->day-1;
                        }
                        if($day->day !== count($days)){
                            $new_blocked_days[] = $day->day+2;
                        }
                        $blocked_days = array_merge($blocked_days,$new_blocked_days);
                        // dd($guards);
                    }
                }
                
            }
            $guards_assigned = json_decode($user->guards_assigned, true) ?? [];
            $guards_assigned[$month->name] = $guards;
            
            $user->guards_assigned = json_encode($guards_assigned);
            $user->save();
        }
    
        // dd($guards_r1);
        foreach($residents4 as $user)
        {
            $blocked_days_json = json_decode($user->blocked_days);
            if($blocked_days_json === null){
                dd($user);
            }
            $blocked_days = $blocked_days_json->{$month->name};
            $total_guards = $user->total_guards;
            $weekend_guards = $user->weekend_guards;
            $friday_done = false;
            $guards = [];
            // dd($user);
            if($user->id === 14){
                // dd($days);
            }
            //primer assignem el divendres
            foreach($days as $day)
            {
                //divendres i dissabte
                // dd($day);
                if($total_guards === 0){
                    break; //TODO: guardar dades
                }
                if($day->name_guardTransplant === 'MCB'){
                    continue;
                }
                if($day->week_day === 5 && !$friday_done){ //divendres
                    if(in_array($day->day, $blocked_days) || in_array($day->day, $guards)){
                        //esta blocked, pasamos
                    }else{                 
                            if($user->id === 18){
                                // dd($day);
                            }                   
                            if($day->user_id === null){
                                $day->user_id = $user->id;
                                $day->save();

                                $new_blocked_days = [];
                                $guards[] = $day->day;
                                $total_guards -= 1;
                                $friday_done = true;
                                if($day->day !== 1){
                                    $new_blocked_days[] = $day->day-1;
                                }
                                if($day->day !== count($days)){
                                    $new_blocked_days[] = $day->day+1;
                                }
                                $blocked_days = array_merge($blocked_days,$new_blocked_days);
                                // dd($guards);
                                //TODO: guardar els blocked_days al $blocked_days_json->{$month->name}
                            }
                    }
                //  dd($days);
                } 
            }

            //despres assignem dissabte:
            foreach($days as $day)
            {
                if($day->week_day === 6 && $weekend_guards > 0)
                {
                    if(!in_array($day->day, $guards) && !in_array($day->day, $blocked_days)){

                        if($day->user_id === null){
                            $day->user_id = $user->id;
                            $day->save();
                            $new_blocked_days = [];
                            $guards[] = $day->day;
                            $total_guards -= 1;
                            $weekend_guards -= 1;
                            if($day->day !== 1){
                                $new_blocked_days[] = $day->day-1;
                            }
                            if($day->day !== count($days)){ //es bloqueja el dilluns
                                $new_blocked_days[] = $day->day+2;

                            }
                            $blocked_days = array_merge($blocked_days,$new_blocked_days);
                            // dd($blocked_days);
                            break;
                        }
                    }
                }
            }
            // dd($blocked_days);
            //despres, els que queden:
            if($total_guards > 0)
            {
                foreach($days as $day)
                {
                    if($day->week_day !== 6 && $day->week_day !== 0 && $day->week_day !== 5) //no es cap de setmana ni divendres
                    {
                        if(!in_array($day->day, $guards) && !in_array($day->day, $blocked_days) && $day->user_id === null)
                        {
                            $day->user_id = $user->id;
                            $day->save();

                            $new_blocked_days = [];
                            $guards[] = $day->day;
                            $total_guards -= 1;
                            // dd($guards);
                            if($day->day !== 1){
                                $new_blocked_days[] = $day->day-1;
                            }
                            if($day->day !== count($days)){ 
                                $new_blocked_days[] = $day->day+1;
                            }
                            $blocked_days = array_merge($blocked_days,$new_blocked_days);

                            // dd($blocked_days);
                            if($total_guards === 0){
                                break;
                            }
                        }
                    }
                }
            }
            // dd($days);
            
            // $user->guards_assigned->{$month->name} = json_encode($guards);
            $guards_assigned = json_decode($user->guards_assigned, true) ?? [];
            $guards_assigned[$month->name] = $guards;
            
            $user->guards_assigned = json_encode($guards_assigned);
            $user->save();

            // foreach($days as $day){
            //     if(in_array($day->day, $guards)){
            //         $day->user_id = $user->id;
            //         $day->save();
            //     }
            // }
            // dd($days);

        }

        //array que farem servir amb els Adjunts Senior
        $guards_r3_2 = [];
        foreach($residents3_2 as $user)
        {
            $blocked_days_json = json_decode($user->blocked_days);
            if($blocked_days_json === null){
                dd($user);
            }
            $blocked_days = $blocked_days_json->{$month->name};
            $total_guards = $user->total_guards;
            $weekend_guards = $user->weekend_guards;
            $guards = [];

            //primer el dia de cap de setmana
            foreach($days as $day)
            {
                if($total_guards === 0 || $weekend_guards === 0){
                    break; 
                }
                if($day->week_day === 6 || $day->week_day === 0) //dissabte o diumenge
                {
                    // if($day->day === 9){
                    //     dd($user);

                    // }
                    if(!in_array($day->day, $blocked_days) && !in_array($day->day, $guards) && $day->user_id === null)
                    {
                        $day->user_id = $user->id;
                        $day->save();

                        $new_blocked_days = [];
                        $guards[] = $day->day;
                        $guards_r3_2[] = $day->day;

                        $total_guards -= 1;
                        $weekend_guards -= 1;
                        if($day->day !== 1){
                            $new_blocked_days[] = $day->day-1;
                        }
                        if($day->day !== count($days)){
                            $new_blocked_days[] = $day->day+1;
                            if($day->day === 6){
                                $new_blocked_days[] = $day->day+2;
                            }
                        }
                        $blocked_days = array_merge($blocked_days,$new_blocked_days);
                        if($user->id === 16){
                            
                        }
                        // dd($guards);
                    }
                }
            }

            //després, els altres dies
            foreach($days as $day)
            {
                if($total_guards === 0)
                {
                    break; 
                }
                if($day->week_day === 5 || $day->week_day === 6 || $day->week_day === 0) //no fan guardies divendres
                {
                    continue;
                }
                if(!in_array($day->day, $blocked_days) && !in_array($day->day, $guards) && $day->user_id === null)
                {
                    $day->user_id = $user->id;
                    $day->save();

                    $new_blocked_days = [];
                    $guards[] = $day->day;
                    $guards_r3_2[] = $day->day;

                    $total_guards -= 1;
                    if($day->day !== 1){
                        $new_blocked_days[] = $day->day-1;
                    }
                    if($day->day !== count($days)){
                        $new_blocked_days[] = $day->day+1;
                    }
                    $blocked_days = array_merge($blocked_days,$new_blocked_days);
                    // dd($guards);
                }

            }
            $guards_assigned = json_decode($user->guards_assigned, true) ?? [];
            $guards_assigned[$month->name] = $guards;
            
            $user->guards_assigned = json_encode($guards_assigned);
            $user->save();
        }

    //     foreach($residents as $user)
    //     {
    //         $blocked_days_json = json_decode($user->blocked_days);
    //         if($blocked_days_json === null){
    //             dd($user);
    //         }
    //         $blocked_days = $blocked_days_json->{$month->name};
            
    //         switch ($user->type) {
    //             case 'R4':
    //                 $total_guards = $user->total_guards;
    //                 $weekend_guards = $user->weekend_guards;
    //                 $friday_done = false;
    //                 $guards = [];
    //                 // dd($user);
    //                 if($user->id === 14){
    //                     // dd($days);
    //                 }
    //                 //primer assignem el divendres
    //                 foreach($days as $day)
    //                 {
    //                     //divendres i dissabte
    //                     // dd($day);
    //                     if($total_guards === 0){
    //                         break; //TODO: guardar dades
    //                     }
    //                     if($day->name_guardTransplant === 'MCB'){
    //                         continue;
    //                     }
    //                     if($day->week_day === 5 && !$friday_done){ //divendres
    //                         if(in_array($day->day, $blocked_days) || in_array($day->day, $guards)){
    //                             //esta blocked, pasamos
    //                         }else{                 
    //                                 if($user->id === 18){
    //                                     // dd($day);
    //                                 }                   
    //                                 if($day->user_id === null){
    //                                     $day->user_id = $user->id;
    //                                     $day->save();
    
    //                                     $new_blocked_days = [];
    //                                     $guards[] = $day->day;
    //                                     $total_guards -= 1;
    //                                     $friday_done = true;
    //                                     if($day->day !== 1){
    //                                         $new_blocked_days[] = $day->day-1;
    //                                     }
    //                                     if($day->day !== count($days)){
    //                                         $new_blocked_days[] = $day->day+1;
    //                                     }
    //                                     $blocked_days = array_merge($blocked_days,$new_blocked_days);
    //                                     // dd($guards);
    //                                     //TODO: guardar els blocked_days al $blocked_days_json->{$month->name}
    //                                 }
    //                         }
    //                     //  dd($days);
    //                     } 
    //                 }
    
    //                 //despres assignem dissabte:
    //                 foreach($days as $day)
    //                 {
    //                     if($day->week_day === 6 && $weekend_guards > 0)
    //                     {
    //                         if(!in_array($day->day, $guards) && !in_array($day->day, $blocked_days)){
    
    //                             if($day->user_id === null){
    //                                 $day->user_id = $user->id;
    //                                 $day->save();
    //                                 $new_blocked_days = [];
    //                                 $guards[] = $day->day;
    //                                 $total_guards -= 1;
    //                                 $weekend_guards -= 1;
    //                                 if($day->day !== 1){
    //                                     $new_blocked_days[] = $day->day-1;
    //                                 }
    //                                 if($day->day !== count($days)){ //es bloqueja el dilluns
    //                                     $new_blocked_days[] = $day->day+2;
    
    //                                 }
    //                                 $blocked_days = array_merge($blocked_days,$new_blocked_days);
    //                                 // dd($blocked_days);
    //                                 break;
    //                             }
    //                         }
    //                     }
    //                 }
    //                 // dd($blocked_days);
    //                 //despres, els que queden:
    //                 if($total_guards > 0)
    //                 {
    //                     foreach($days as $day)
    //                     {
    //                         if($day->week_day !== 6 && $day->week_day !== 0 && $day->week_day !== 5) //no es cap de setmana ni divendres
    //                         {
    //                             if(!in_array($day->day, $guards) && !in_array($day->day, $blocked_days) && $day->user_id === null)
    //                             {
    //                                 $day->user_id = $user->id;
    //                                 $day->save();
    
    //                                 $new_blocked_days = [];
    //                                 $guards[] = $day->day;
    //                                 $total_guards -= 1;
    //                                 // dd($guards);
    //                                 if($day->day !== 1){
    //                                     $new_blocked_days[] = $day->day-1;
    //                                 }
    //                                 if($day->day !== count($days)){ 
    //                                     $new_blocked_days[] = $day->day+1;
    //                                 }
    //                                 $blocked_days = array_merge($blocked_days,$new_blocked_days);
    
    //                                 // dd($blocked_days);
    //                                 if($total_guards === 0){
    //                                     break;
    //                                 }
    //                             }
    //                         }
    //                     }
    //                 }
    //                 // dd($days);
                    
    //                 // $user->guards_assigned->{$month->name} = json_encode($guards);
    //                 $guards_assigned = json_decode($user->guards_assigned, true) ?? [];
    //                 $guards_assigned[$month->name] = $guards;
                    
    //                 $user->guards_assigned = json_encode($guards_assigned);
    //                 $user->save();
    
    //                 foreach($days as $day){
    //                     if(in_array($day->day, $guards)){
    //                         $day->user_id = $user->id;
    //                         $day->save();
    //                     }
    //                 }
    //                 // dd($days);
    
    
    //                 break;
    // ////////////////////////////////// Case-R1 /////////////////////////////////////////////                        
    //                 case 'R1':
    //                     # code...
    //                 break;
    
    // ////////////////////////////////// Case-R1 /////////////////////////////////////////////                        
    //                 case null:
    //                 # code...
    //                 break;
                    
    //                 default:
    //                 # code...
    //                 break;
    //             }
    //     }
     ////////////////////////////////// ADJUNTS /////////////////////////////////////////////                        
       
        foreach($adjunts as $user)
        {
            if($user->name === 'MCB'){
                continue;
            }
            $blocked_days_json = json_decode($user->blocked_days);
            if($blocked_days_json === null){
                dd($user);
            }
            $blocked_days = $blocked_days_json->{$month->name};

                
                // dd($days);
        }
    }

    
}
