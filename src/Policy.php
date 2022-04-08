<?php

namespace Blazervel\Blazervel;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Policy
{
  protected string $action;
  protected User $user;
  protected Model $model;

  private $actionPolicyMap = [
    'index'   => 'viewAny',
    'show'    => 'view',
    'create'  => 'create',
    'store'   => 'create',
    'edit'    => 'update',
    'update'  => 'update',
    'destroy' => 'delete',
  ];

  protected function __construct(string $action, User $user, Model $model)
  {
    $this->user = $user;

    $modelName = Str::camel(class_basename($model::class));

    $this->modelName = $modelName;
    $this->$modelName = $model;
  }

  protected static function make(string $action, User $user, Model $model)
  {
    $calledClassNamespace = Concept::conceptNamespace(get_called_class());
    $policyClass = "{$calledClassNamespace}\\Policy";
    
    return (
      new $policyClass(
        action: $action, 
        user: $user, 
        model: $model
      )
    );
  }

  protected function authorize(): void
  {
    $actionPolicyMethod = $this->actionPolicyMap[$this->action] ?? $this->action;
    $modelName = $this->modelName;

    if (
      $this->$actionPolicyMethod(
        user: $this->user,
        model: $this->$modelName
      ) === false
    ) :

      throw new AuthorizationException(
        'This action is unauthorized.'
      );

    endif;
  }
}