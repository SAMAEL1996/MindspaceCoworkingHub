<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUid;
use Filament\Forms\Components as FormComponents;

class Profile extends Model
{
    use HasFactory, HasUid;

    protected $fillable = [
        'sss',
        'pagibig',
        'philhealth',
        'tin',
        'psa',
        'nbi',
        'brgy_clearance',
        'diploma',
        'medical',
        'coe',
        'bir',
        'id_picture_1',
        'id_picture_2',
        'deadline',
        'status',
    ];

    protected $appends = ['total_submitted', 'requirements'];

    public function staff()
    {
        return $this->belongsTo(\App\Models\Staff::class, 'staff_id');
    }

    public function getTotalSubmittedAttribute()
    {
        $total = 0;
        if($this->sss) {
            $total += 1;
        }
        if($this->pagibig) {
            $total += 1;
        }
        if($this->philhealth) {
            $total += 1;
        }
        if($this->tin) {
            $total += 1;
        }
        if($this->psa) {
            $total += 1;
        }
        if($this->nbi) {
            $total += 1;
        }
        if($this->brgy_clearance) {
            $total += 1;
        }
        if($this->diploma) {
            $total += 1;
        }
        if($this->medical) {
            $total += 1;
        }
        if($this->coe) {
            $total += 1;
        }
        if($this->bir) {
            $total += 1;
        }
        if($this->id_picture_1) {
            $total += 1;
        }
        if($this->id_picture_2) {
            $total += 1;
        }

        return (int)$total;
    }

    public function getRequirementsAttribute()
    {
        return 13;
    }

    public static function getForm()
    {
        return [
            FormComponents\Grid::make(1)
                ->schema([
                    FormCOmponents\Section::make('Requirements')
                        ->schema([
                            FormCOmponents\Toggle::make('sss')
                                ->label('SSS E1/ID/Static Information'),
                            FormCOmponents\Toggle::make('pagibig')
                                ->label('Pagibig Member\'s Data Form/ Loyalty Card'),
                            FormCOmponents\Toggle::make('philhealth')
                                ->label('Philhealth Insurance ID/MDR'),
                            FormCOmponents\Toggle::make('tin')
                                ->label('TIN ID'),
                            FormCOmponents\Toggle::make('psa')
                                ->label('PSA Birth Certificate'),
                            FormCOmponents\Toggle::make('nbi')
                                ->label('NBI / Police Clearance'),
                            FormCOmponents\Toggle::make('brgy_clearance')
                                ->label('Brgy. Clearance'),
                            FormCOmponents\Toggle::make('diploma')
                                ->label('High School/College Credentials(TOR, Diploma etc.)'),
                            FormCOmponents\Toggle::make('medical')
                                ->label('Medical Result'),
                            FormCOmponents\Toggle::make('coe')
                                ->label('COE from previous employers'),
                            FormCOmponents\Toggle::make('bir')
                                ->label('BIR 2316 from previous employer'),
                            FormCOmponents\Toggle::make('id_picture_1')
                                ->label('1x1 ID Picture-Formal Attire'),
                            FormCOmponents\Toggle::make('id_picture_2')
                                ->label('2x2 ID Picture-Formal Attire'),
                        ])
                        ->columns(3)
                ])
        ];
    }
}
