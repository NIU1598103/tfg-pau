<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Inertia\Inertia;
// use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;



class UserController extends Controller
{
    public function index(){
        $users = User::all();
        return Inertia::render('Users/Index', [
            'users' => $users
        ]);
    }
    
    public function edit($id){
        $user = User::findOrFail($id);
        
        $actualUser = Auth::user();
        
        return Inertia::render('Users/Edit', [
            'user' => $user,
            'actualUser' => $actualUser
        ]);
    }

    public function update(Request $request)
    {
        $user = User::findOrFail($request->id);
        $user->name = $request->name;
        $user->email = $request->email;
        if($request->type === "Por determinar"){
            $user->type = null;
        }
        else{
            $user->type = $request->type;
            
        }
        $user->admin = $request->admin;
        $user->verified = $request->verified;
        $user->transplant = $request->transplant;
        $user->weekend_guards = $request->weekend_guards;
        $user->total_guards = $request->total_guards;
        $user->flexible = $request->flexible;
        $user->weekend_backing = $request->weekend_backing;
        $user->less_guards_if_transplant = $request->less_guards_if_transplant;
        $user->save();
        $action = "PerfilUpdate";
        // return redirect()->route('users.index');
        return redirect()->route('feedback.index');

    }

    public function update_blocked_days(Request $request){
        $user = Auth::user();
        
        $month = $request->month;
        $selectedDays = $request->days_selected;
        
        $blockedDays = json_decode($user->blocked_days, true) ?? [];
        $blockedDays[$month] = $selectedDays;

        $user->blocked_days = json_encode($blockedDays);

        $user->save();
        return redirect()->route('feedback.index');
        
    }
    
    public function delete($id){
        $user = User::find($id);
        return Inertia::render('Users/Delete', [
            'user' => $user
        ]);
        
    }
    
    public function delete_confirm(Request $request){
        $user = User::find($request->id);
        $name = $request->name;
        $user->delete();
        return redirect()->route('users.index');
        // return Inertia::render('Users/DeleteConfirm', [
        //     'name' => $name
        // ]);

    }
}
