<?php

namespace Blazervel\Blazervel;

use Blazervel\Blazervel\WithModelActions;
// use Blazervel\Blazervel\Events\ActionsJSWasCalled;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use InvalidArgumentException;

trait WithActionsJS
{
    use WithModelActions;

    /**
     * Scope to results that satisfy an ActionsJS query.
     *
     * @param Builder $query
     * @param string|null|callable $rules
     */
    public function scopeActionsJS(Builder $query, $rules = null)
    {
        //new ActionsJSWasCalled($query, $rules);
        
        return $query;
    }

    /**
     * Handle dynamic calls to scope methods.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $name
     * @param array $parameters
     */
    public function scopeScope($query, $name, $parameters = [])
    {
        $methodName = 'scope' . ucfirst($name);

        if ( ! method_exists($this, $methodName)) {
            throw new InvalidArgumentException("No such scope [$name]");
        }

        array_unshift($parameters, $query); // prepend $query to $parameters array
        call_user_func_array([$this, $methodName], $parameters);
    }

    /**
     * Return a timestamp as DateTime object.
     *
     * Accept dates from Javascript in any format that Carbon recognises.
     *
     * @param  string $value
     * @return Carbon
     */
    protected function asDateTime($value)
    {
        if (is_string($value)) {
            return Carbon::parse($value);
        }

        return parent::asDateTime($value);
    }
}
