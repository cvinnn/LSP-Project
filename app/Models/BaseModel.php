<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * BaseModel - Abstract base model with validation rules interface
 */
abstract class BaseModel extends Model
{
    use HasFactory;

    /**
     * Define validation rules for model (implement in subclasses)
     */
    abstract protected function rules(): array;

    /**
     * Get validation rules
     */
    public function getValidationRules(): array
    {
        return $this->rules();
    }
}
