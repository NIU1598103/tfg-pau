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
    public function create(Request $request)
    {
        // Verificar si el mes ya existe
        $existingMonth = Month::where('name', $request->date)->first();
        if ($existingMonth) {
            return redirect()->back()->with('error', 'El mes ya existe');
        }

        // Crear el nuevo mes
        Month::create([
            'name' => $request->date,
        ]);

        // Redirigir de vuelta con un mensaje de éxito
        return redirect()->back()->with('success', 'Nuevo mes creado con éxito');
    }
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

    public function see_guards($id)
    {
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
        //dd($daysExtra);
        return Inertia::render('Months/SeeGuards', [
            'month' => $month,
            'daysExtra' => $daysExtra,
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
            $day->id_ref = null;
            $day->ref_name = null;
            $day->user_name = null;
            $day->save();
        }
        $month->handled = null;
        $month->save();
    }

    public function error_organize($id){
        $month = Month::findOrFail($id);
        $user_ids = json_decode($month->users_no_blocked_days);
        // dd($user_ids);
        $users = [];
        foreach($user_ids as $id){
            $users[] = User::find($id);
        }
        return Inertia::render('Months/ErrorOrganize', [
            'users' => $users,
            'month' => $month,
        ]);
    }



    public function organize_guards(Request $request){
        $month = Month::find($request->month);
        $days = $month->days;
        $users = User::where('verified', true)->get();
        //  dd($users);
        //  dd($days);
        // foreach($days as $day){
        //     // dd($days);
        //     $day->user_id = null;
        //     $day->save();
        // }

        $users_no_days_blocked = [];
        foreach($users as $user)
        {
            if($user->name === 'MCB')
            {
                continue;
            }
            $blocked_days_json = json_decode($user->blocked_days);
            if(isset($blocked_days_json->{$month->name}))
            {
                $blocked_days = $blocked_days_json->{$month->name};
            }else{

                $users_no_days_blocked[] = $user->id;
            }

        
            
        }
        // dd($users_no_days_blocked);
        if(count($users_no_days_blocked) > 0)
        {
            $month->users_no_blocked_days = json_encode($users_no_days_blocked); // Convertir los IDs a JSON
            $month->handled = "BLOCK_DAYS";
            $month->save();
            dd($month);
            return;
        }
        $residents4 = [];
        $residents3_2 = [];
        $residents1 = [];
        $adjuntsJunior = [];
        $adjuntsSenior = [];
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
            else if($user->type === 'Adjunto Junior'){
                $adjuntsJunior[] = $user;
            }
            else if($user->type === 'Adjunto Senior'){
                $adjuntsSenior[] = $user;
            }
        }
        // dd($adjuntsSenior);
        // dd($residents3_2);

        //array que farem servir per als Adjunts Junior
        $guards_r1 = [];
        $days = $days->shuffle();
        // dd($days);
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
                        $day->user_name = $user->name;

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
        $guards_r4 = [];
        $days = $days->shuffle();

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
                                $day->user_name = $user->name;

                                $day->save();

                                $new_blocked_days = [];
                                $guards[] = $day->day;
                                $guards_r4[] = $day->day;

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
                            $day->user_name = $user->name;

                            $day->save();
                            $new_blocked_days = [];
                            $guards[] = $day->day;
                            $guards_r4[] = $day->day;
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
                            $day->user_name = $user->name;

                            $day->save();

                            $new_blocked_days = [];
                            $guards[] = $day->day;
                            $guards_r4[] = $day->day;
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

        
        $days = $days->shuffle();

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
                        $day->user_name = $user->name;

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
                            if($day->week_day === 6){
                                $new_blocked_days[] = $day->day+2;
                            }
                        }
                        $blocked_days = array_merge($blocked_days,$new_blocked_days);
                        
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
                    $day->user_name = $user->name;

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
        // dd($days);



        //primer posem els REF + LOC
        foreach ($adjuntsJunior as $user) 
        {
            $less_guards_if_transplant_restada = false;

            foreach($days as $day)
            {
                if($day->id_guardTransplant === $user->id)
                {
                    if(in_array($day->day, $guards_r4) && $day->id_ref === null)
                    {
                        $refloc = "(REF+LOC)";
                        $day->id_ref = $user->id;
                        $day->ref_name = $user->name . $refloc;
                        $day->save();
                        if($user->less_guards_if_transplant === 1 && $less_guards_if_transplant_restada === false)
                        {
                            $user->aux_total_guards = $user->total_guards - 1;
                            $user->save();
                            $less_guards_if_transplant_restada = true;
                        }
                    }
                    continue;
                }
            }
        }
        //posem els REF+LOC dels senior tambe
        foreach ($adjuntsSenior as $user) 
        {
            if($user->name === 'MCB'){
                continue;
            }
            $less_guards_if_transplant_restada = false;
            foreach($days as $day)
            {
                if($day->id_guardTransplant === $user->id)
                {
                    if(in_array($day->day, $guards_r4) && $day->id_ref === null)
                    {
                        $refloc = "(REF+LOC)";
                        $day->id_ref = $user->id;
                        $day->ref_name = $user->name . $refloc;
                        $day->save();
                        if($user->less_guards_if_transplant === 1 && $less_guards_if_transplant_restada === false)
                        {
                            $user->aux_total_guards = $user->total_guards - 1;
                            $user->save();
                            $less_guards_if_transplant_restada = true;
                        }
                    }
                    continue;
                }
            }
        }

        $days = $days->shuffle();

        foreach($adjuntsSenior as $user)
        {
            if($user->name === 'MCB'){
                continue;
            }
            $blocked_days_json = json_decode($user->blocked_days);
            if($blocked_days_json === null){
                dd($user);
            }
            $blocked_days = $blocked_days_json->{$month->name};
            $total_guards = $user->total_guards;
            if($user->aux_total_guards !== null)
            {
                $total_guards = $user->aux_total_guards;
            }
            $weekend_guards = $user->weekend_guards;
            $guards = [];
                
            //primer findes
            // dd($guards_r3_2);
            // dd($user);

            foreach($days as $day)
            {
                if($total_guards === 0 || $weekend_guards === 0){
                    break; 
                }
                if($day->id_guardTransplant === $user->id)
                {
                    continue;
                }
                if($day->week_day === 6 || $day->week_day === 0) //dissabte o diumenge
                {
                    if($day->day === 2){

                        // dd($day, $blocked_days, $guards, $guards_r3_2);
                    }
                    if(!in_array($day->day, $blocked_days) && !in_array($day->day, $guards) && in_array($day->day, $guards_r3_2) && $day->id_ref === null)
                    {
                        $day->id_ref = $user->id;
                        $day->ref_name = $user->name;
                        $day->save();
                        // dd($day);

                        $new_blocked_days = [];
                        $guards[] = $day->day;

                        $total_guards -= 1;
                        $weekend_guards -= 1;
                        if($day->day !== 1){
                            $new_blocked_days[] = $day->day-1;
                        }
                        if($day->day !== count($days)){
                            $new_blocked_days[] = $day->day+1;
                            if($day->week_day === 6){
                                $new_blocked_days[] = $day->day+2;
                            }
                        }
                        $blocked_days = array_merge($blocked_days,$new_blocked_days);
                        
                    }
                }
            }

            //la resta de dies
            foreach($days as $day)
            {
                if($total_guards === 0){
                    break; 
                }
                if($day->id_guardTransplant === $user->id)
                {
                    continue;
                }
                if($day->week_day !== 6 && $day->week_day !== 0 && $day->week_day !== 5) //ni finde ni divendres
                {
                    if(!in_array($day->day, $blocked_days) && !in_array($day->day, $guards) && in_array($day->day, $guards_r3_2) && $day->id_ref === null)
                    {
                        $day->id_ref = $user->id;
                        $day->ref_name = $user->name;
                        $day->save();

                        $new_blocked_days = [];
                        $guards[] = $day->day;

                        $total_guards -= 1;
                        $weekend_guards -= 1;
                        if($day->day !== 1){
                            $new_blocked_days[] = $day->day-1;
                        }
                        if($day->day !== count($days)){
                            $new_blocked_days[] = $day->day+1;
                            if($day->week_day === 6){
                                $new_blocked_days[] = $day->day+2;
                            }
                        }
                        $blocked_days = array_merge($blocked_days,$new_blocked_days);
                        
                    }
                }
            }
            // dd($guards);
            $guards_assigned = json_decode($user->guards_assigned, true) ?? [];
            $guards_assigned[$month->name] = $guards;
            
            $user->guards_assigned = json_encode($guards_assigned);
            $user->save();
            // dd($user->guards_assigned);
        }
        // dd($guards_r4);
        
        $adjuntsJuniorSorted = [];
        $adjuntsJuniorNoTr = [];
        $adjuntsJuniorTr = [];
        foreach($adjuntsJunior as $user)
        {
            if($user->transplant === 1)
            {
                $adjuntsJuniorTr[] = $user;
            }
            else{
                $adjuntsJuniorNoTr[] = $user;
            }
        }
        $adjuntsJuniorSorted = array_merge($adjuntsJuniorNoTr, $adjuntsJuniorTr);
      
        //donem preferència als adjunts que no fan guardies de trasplantament
        foreach($adjuntsJuniorSorted as $user)
        {            
            $blocked_days_json = json_decode($user->blocked_days);
            if($blocked_days_json === null){
                dd($user);
            }
            $blocked_days = $blocked_days_json->{$month->name};
            $total_guards = $user->total_guards;
            if($user->aux_total_guards !== null)
            {
                $total_guards = $user->aux_total_guards;
            }
            $weekend_guards = $user->weekend_guards;
            $guards = [];

            //primer findes
            //es pot provar agafant-los al reves?
            // shuffle($days);
            $days_a = $days->shuffle();
            // dd($days_a);
            foreach($days_a as $day)
            {
                if($total_guards === 0 || $weekend_guards === 0){
                    break; 
                }
                if($day->id_guardTransplant === $user->id)
                {
                    continue;
                }
                if($day->week_day === 6 || $day->week_day === 0) //dissabte o diumenge
                {
                    //mirem si hi ha reforços disponibles 
                    if(!in_array($day->day, $blocked_days) && !in_array($day->day, $guards))
                    {
                        if($day->user_id === null)
                        {
                            $day->user_id = $user->id;
                            $day->user_name = $user->name;
                            $day->save();
                            // dd($day);
    
                            $new_blocked_days = [];
                            $guards[] = $day->day;
    
                            $total_guards -= 1;
                            $weekend_guards -= 1;
                            if($day->day !== 1){
                                $new_blocked_days[] = $day->day-1;
                            }
                            if($day->day !== count($days)){
                                $new_blocked_days[] = $day->day+1;
                                if($day->week_day === 6){
                                    $new_blocked_days[] = $day->day+2;
                                }
                            }
                            $blocked_days = array_merge($blocked_days,$new_blocked_days);
                            
                        }
                        else if($day->id_ref === null)
                        {
                            if(in_array($day->day, $guards_r3_2) || in_array($day->day, $guards_r1))
                            {
                                $day->id_ref = $user->id;
                                $day->ref_name = $user->name;
                                $day->save();
                                // dd($day);
        
                                $new_blocked_days = [];
                                $guards[] = $day->day;
        
                                $total_guards -= 1;
                                $weekend_guards -= 1;
                                if($day->day !== 1){
                                    $new_blocked_days[] = $day->day-1;
                                }
                                if($day->day !== count($days)){
                                    $new_blocked_days[] = $day->day+1;
                                    if($day->week_day === 6){
                                        $new_blocked_days[] = $day->day+2;
                                    }
                                }
                                $blocked_days = array_merge($blocked_days,$new_blocked_days);
                                
                            }
                        }
                        
                    }
                }
            }
                // dd($guards);

            //mirem els altres:
            foreach($days_a as $day)
            {
                if($total_guards === 0){
                    break; 
                }
                if($day->id_guardTransplant === $user->id)
                {
                    continue;
                }
                if($day->week_day !== 6 && $day->week_day !== 0) //no finde
                {
                    if(!in_array($day->day, $blocked_days) && !in_array($day->day, $guards) && $day->user_id === null)
                    {
                        $day->user_id = $user->id;
                        $day->user_name = $user->name;
                        $day->save();

                        $new_blocked_days = [];
                        $guards[] = $day->day;

                        $total_guards -= 1;
                        $weekend_guards -= 1;
                        if($day->day !== 1){
                            $new_blocked_days[] = $day->day-1;
                        }
                        if($day->day !== count($days)){
                            $new_blocked_days[] = $day->day+1;
                        }
                        $blocked_days = array_merge($blocked_days,$new_blocked_days);
                        
                    }
                    else if(!in_array($day->day, $blocked_days) && !in_array($day->day, $guards) && $day->id_ref === null)
                    {
                        $user_assigned_this_day = User::find($day->user_id);
                        if($user_assigned_this_day->type !== 'Adjunto Junior')
                        {
                            $day->id_ref = $user->id;
                            $day->ref_name = $user->name;
                            $day->save();
    
                            $new_blocked_days = [];
                            $guards[] = $day->day;
    
                            $total_guards -= 1;
                            $weekend_guards -= 1;
                            if($day->day !== 1){
                                $new_blocked_days[] = $day->day-1;
                            }
                            if($day->day !== count($days)){
                                $new_blocked_days[] = $day->day+1;
                            }
                            $blocked_days = array_merge($blocked_days,$new_blocked_days);
                            
                        }
                        
                    }
                }
            }
            $guards_assigned = json_decode($user->guards_assigned, true) ?? [];
            $guards_assigned[$month->name] = $guards;
            
            $user->guards_assigned = json_encode($guards_assigned);
            $user->save();
            //ara falta omplir els dies buits amb els adjunts "flexibles"
        }

        $users_flexibles = User::where('verified', true)
                        ->where('flexible', true)->get();
        // dd($month);
        $empty_days = [];
        $count = 0;
        $users_flexibles = $users_flexibles->shuffle();
        foreach($month->days as $day)
        {
            if($day->week_day !== 6 && $day->week_day !== 0) //NO FINDE
            {
                if($day->id_ref === null){
    
                    $empty_days[] = $day;
                }

            }
        }
        $empty_days_weekend = [];    
        foreach($month->days as $day)
        {
            if($day->week_day === 6 || $day->week_day === 0)
            {
                if($day->id_ref === null){
                    $empty_days_weekend[] = $day;
                }
            }
        }
        
        $users_disponibles = []; //users amb guardies per assignar encara
        $users_disponibles_weekend = []; // """ en findes
        foreach($adjuntsJuniorSorted as $user)
        {
            
            $guards_json = json_decode($user->guards_assigned);
            $guards = $guards_json->{$month->name};
            $total_guards = $user->total_guards;
            if($user->aux_total_guards !== null)
            {
                $total_guards = $user->aux_total_guards;
            }
            $weekend_guards = $user->weekend_guards;

            if(count($guards) < $total_guards)
            {
                $users_disponibles[] = $user;
            }
            $count_weekend_guards = 0;
            foreach($days as $day)
            {
                if($day->week_day === 6 || $day->week_day === 0)
                {
                    if(in_array($day->day, $guards)){
                        $count_weekend_guards += 1;
                    }
                }
            }
            if($count_weekend_guards < $weekend_guards)
            {
                $users_disponibles_weekend[] = $user;
            }

        }
        
        //primer hauriem de mirar els usuaris q no han completat les guardies
        foreach($users_disponibles_weekend as $user)
        {
            $blocked_days_json = json_decode($user->blocked_days);
            if($blocked_days_json === null){
                dd($user);
            }
            $blocked_days = $blocked_days_json->{$month->name};
            $guards_json = json_decode($user->guards_assigned);
            $guards = $guards_json->{$month->name};

            $assigned = false;
            $assigned_emptys_weekend = [];
            foreach($empty_days_weekend as $day)
            {
                $day_prev = $day->day - 1;
                $day_after = $day->day + 1;
                if(!in_array($day->day, $blocked_days) && !in_array($day->day, $guards) && !in_array($day_after, $guards) && !in_array($day_prev, $guards))
                {
                    $day->id_ref = $user->id;
                    $day->ref_name = $user->name;
                    $guards[] = $day->day;
                    $day->save();
                    $assigned = true;
                    $assigned_emptys_weekend[] = $day;
                    $users_disponibles = array_diff($users_disponibles, array($user));
                    $empty_days_weekend = array_diff($empty_days_weekend, array($day));
                    $guards_assigned = json_decode($user->guards_assigned, true) ?? [];
                    $guards_assigned[$month->name] = $guards;
                    
                    $user->guards_assigned = json_encode($guards_assigned);
                    $user->save();
                    break;
                }
                else if($day->id_guardTransplant === $user->id)
                {
                    $refloc = "(REF+LOC)";
                    $day->id_ref = $user->id;
                    $day->ref_name = $user->name . $refloc;
                    $guards[] = $day->day;
                    $day->save();
                    $assigned = true;
                    $assigned_emptys_weekend[] = $day;
                    $users_disponibles = array_diff($users_disponibles, array($user)); //ELIMINEM DE USERS DISPONIBLES
                    $empty_days_weekend = array_diff($empty_days_weekend, array($day));
                    break;

                }
            }
            // foreach($assigned_emptys_weekend as $day)
            // {
            //     $empty_days_weekend = array_diff($empty_days_weekend, array($day));
            // }
            
            
        }
        // dd($users_disponibles, $users_disponibles_weekend);

        
        //SI QUEDEN DIES DE FINDE PER COMPLETAR, ELS COMPLETA EL GUARDIA TRANSPLANT
        $month = Month::find($request->month);
        $days = $month->days;
        foreach($days as $day)
        {
            if($day->week_day === 6 || $day->week_day === 0)
            {
                if($day->id_ref === null)
                {
                    $day->id_ref = $day->id_guardTransplant;
                    $refloc = "(REF+LOC)";
                    $day->ref_name = $day->name_guardTransplant . $refloc;
                    $day->save();
                }
            }
        }
        

        // dd($empty_days_weekend, $assigned_emptys_weekend);

        // omplim guardies entre setmana
        foreach($users_disponibles as $user)
        {
            $blocked_days_json = json_decode($user->blocked_days);
            if($blocked_days_json === null){
                dd($user);
            }
            $blocked_days = $blocked_days_json->{$month->name};
            $guards_json = json_decode($user->guards_assigned);
            $guards = $guards_json->{$month->name};

            $assigned = false;
            $assigned_emptys = [];
            foreach($empty_days as $day)
            {
                $day_prev = $day->day - 1;
                $day_after = $day->day + 1;
                if(!in_array($day->day, $blocked_days) && !in_array($day->day, $guards) && !in_array($day_after, $guards) && !in_array($day_prev, $guards))
                {
                    $day->id_ref = $user->id;
                    $day->ref_name = $user->name;
                    $guards[] = $day->day;
                    $day->save();
                    $assigned = true;
                    $assigned_emptys[] = $day;
                    $users_disponibles = array_diff($users_disponibles, array($user));
                    $empty_days = array_diff($empty_days, array($day));
                    break;
                }
            }
            
            $guards_assigned = json_decode($user->guards_assigned, true) ?? [];
            $guards_assigned[$month->name] = $guards;
            
            $user->guards_assigned = json_encode($guards_assigned);
            $user->save();
        }
        // dd($users_disponibles, $empty_days, $assigned_emptys);

        $month = Month::find($request->month);
        $days = $month->days;
        $days_emptys_restantes = []; //dies entre setmana que un resident està sol o no hi ha ningu fent guardia
        foreach($days as $day)
        {
            if($day->week_day !== 6 && $day->week_day !== 0)
            {
                if($day->id_ref === null)
                {
                    if($day->user_id === null)
                    {
                        $days_emptys_restantes[] = $day;
                    }
                    else
                    {
                        $user_assigned_lonely = User::find($day->user_id);
                        if($user_assigned_lonely->type !== 'Adjunto Junior' && $user_assigned_lonely->type !== 'Adjunto Senior')
                        {
                            // dd($user_assigned_lonely, $day);
                            $days_emptys_restantes[] = $day;
                        }

                    }
                }
            }
        }

        foreach($users_flexibles as $user)
        {
            $blocked_days_json = json_decode($user->blocked_days);
            if($blocked_days_json === null){
                dd($user);
            }
            $blocked_days = $blocked_days_json->{$month->name};
            $guards_json = json_decode($user->guards_assigned);
            $guards = $guards_json->{$month->name};
            // dd($user);
            //VEURE SI POT FER LA GUARDIA AQUELL DIA
            foreach($days_emptys_restantes as $day)
            { 
                $day_prev = $day->day - 1;
                $day_after = $day->day + 1;
                
                if(!in_array($day->day, $blocked_days) && !in_array($day->day, $guards) && !in_array($day_after, $guards) && !in_array($day_prev, $guards))
                {
                    if($day->id_guardTransplant !== $user->id)
                    {
                        $day->id_ref = $user->id;
                        $day->ref_name = $user->name;
                        $guards[] = $day->day;
                        $day->save();
                        break;
                    }
                }
            }
            $guards_assigned = json_decode($user->guards_assigned, true) ?? [];
            $guards_assigned[$month->name] = $guards;
            
            $user->guards_assigned = json_encode($guards_assigned);
            $user->save();
        }

        foreach($users as $user)
        {
            $user->aux_total_guards = null;
            $user->save();
        }

        $month->handled = "ORGANIZED";
        $month->save();
        // dd($users_disponibles, $days);
        // dd($days_a);
    }

    
}
