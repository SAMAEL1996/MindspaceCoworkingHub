<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashLogMoneyCalculator extends Model
{
    use HasFactory;

    protected const FORM_GROUPS = [
        'Notes' => [
            'note_20' => ['label' => 'PHP 20', 'value' => 20],
            'note_50' => ['label' => 'PHP 50', 'value' => 50],
            'note_100' => ['label' => 'PHP 100', 'value' => 100],
            'note_200' => ['label' => 'PHP 200', 'value' => 200],
            'note_500' => ['label' => 'PHP 500', 'value' => 500],
            'note_1000' => ['label' => 'PHP 1000', 'value' => 1000],
        ],
        'Coins' => [
            'coin_1' => ['label' => 'PHP 1', 'value' => 1],
            'coin_5' => ['label' => 'PHP 5', 'value' => 5],
            'coin_10' => ['label' => 'PHP 10', 'value' => 10],
            'coin_20' => ['label' => 'PHP 20', 'value' => 20],
        ],
    ];

    protected $fillable = [
        'cash_log_id',
        'type',
        'note_20',
        'note_50',
        'note_100',
        'note_200',
        'note_500',
        'note_1000',
        'coin_1',
        'coin_5',
        'coin_10',
        'coin_20',
    ];

    protected $casts = [
        'cash_log_id' => 'integer',
        'note_20' => 'integer',
        'note_50' => 'integer',
        'note_100' => 'integer',
        'note_200' => 'integer',
        'note_500' => 'integer',
        'note_1000' => 'integer',
        'coin_1' => 'integer',
        'coin_5' => 'integer',
        'coin_10' => 'integer',
        'coin_20' => 'integer',
    ];

    public function cashLog()
    {
        return $this->belongsTo(CashLog::class, 'cash_log_id');
    }

    public static function formGroups(): array
    {
        return self::FORM_GROUPS;
    }

    public static function calculateAmount(array $data): float
    {
        $total = 0;

        foreach (self::FORM_GROUPS as $fields) {
            foreach ($fields as $field => $definition) {
                $total += ((int) ($data[$field] ?? 0)) * $definition['value'];
            }
        }

        return (float) $total;
    }

    public static function storeForCashLog(CashLog $cashLog, string $type, array $data): self
    {
        return self::query()->updateOrCreate(
            [
                'cash_log_id' => $cashLog->id,
                'type' => $type,
            ],
            self::normalizeInput($data),
        );
    }

    protected static function normalizeInput(array $data): array
    {
        $normalized = [];

        foreach (self::FORM_GROUPS as $fields) {
            foreach (array_keys($fields) as $field) {
                $normalized[$field] = (int) ($data[$field] ?? 0);
            }
        }

        return $normalized;
    }
}
