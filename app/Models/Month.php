<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;


class Month extends Model
{
    use HasFactory;
    protected $fillable = ['name'];

    public function days(){
        return $this->hasMany(Day::class);
    }

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        
        if (isset($attributes['name'])) {

            $date = Carbon::createFromFormat('Y-m', $attributes['name']);
    
            if (!$date) {
                throw new \Exception('Formato de fecha no válido.');
            }
    
            $this->name = $date->format('Y-m');
    
            $this->createDaysForMonth($date);
        }

    }

    private function createDaysForMonth($date)
    {
        $daysInMonth = $date->daysInMonth;
        $month = $this->save(); 
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $this->days()->create([
                'day' => $day,
                'month' => $date->format('m'),
                'year' => $date->format('Y'),
                'week_day' => $date->setDay($day)->dayOfWeek,
                'month_id' => $this->id,
            ]);
        }
    }
}
