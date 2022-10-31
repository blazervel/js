<?php

namespace Blazervel\Blazervel\Actions\Models;

use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

abstract class ModelAction
{
    protected mixed $model;

    protected string $modelClass;

    protected mixed $policy;

    protected array $rules;
    
    protected Request $request;

    protected string $action;
    
    protected Collection $only;

    public function __invoke(Request $request, string $model, int $id = null)
    {
        $this
            ->setRequest($request)
            ->setModel($model, $id)
            ->authorize()
            ->validate()
            ->handle();
    }

    protected function setRequest(Request $request)
    {
        $this->request = $request;
        $this->action = Str::lcfirst(get_called_class());

        return $this;
    }

    protected function setModel(string $model, int $id = null): self
    {
        $modelName = Str::ucfirst(Str::camel($model));
        $modelClass = "App\\Models\\{$modelName}";
        
        // Will throw "class does not exist" error if invalid
        $model = new $modelClass;

        if (!method_exists($modelClass, 'scopeActionsJS')) {
            throw new Exception(
                "[$modelClass] does not have WithActionsJS trait assigned yet"
            );
        }

        $this->modelClass = $modelClass;

        $this->model = $model;

        if ($id) {
            if (method_exists($model, 'scopeActionsJS')) {
                $this->model = $this->model->actionsJS()->findOrFail($id);
            } else {
                $this->model = $model->findOrFail($id);
            }
        }

        $this->except = new Collection(
            $this->model->getActionsHidden() ?: []
        );

        $this->policy = $this->model->getActionPolicy();

        $this->rules = $this->model->getActionRules();

        return $this;
    }

    protected function authorize(): self
    {
        if (!$policy = $this->policy) {
            return $this;
        }

        if (is_array($policy)) {
            
            $policyAction = $policy[$this->action] ?? false;

            if (!$policyAction || !is_callable($policyAction)) {
                throw new Exception(
                    "No policy action method provided for [{$this->action}] on [{$this->modelClass}]"
                );
            }

            $authorized = $policyAction(
                $this->request->user(),
                $this->model
            );

            if (!$authorized) {
                throw new AuthorizationException();
            }
        } else {
            
        }

        return $this;
    }

    protected function validate(): self
    {
        if ($this->rules) {
            $this->request->validate(
                $this->rules
            );
        }

        return $this;
    }

    public abstract function handle();
}