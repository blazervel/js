<?php

namespace Blazervel\BlazervelJS\Actions;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use lluminate\Database\Eloquent\Builder;

class HandleModel
{
    protected Request $request;

    public function __invoke(Request $request, string $model, int $id = null)
    {
        $this->request = $request;

        $modelSlug = $model;
        $modelClass = $this->modelClass($modelSlug);
        $data = $this->data();
        
        $model = null;
        
        if (! $id && $request->method === 'GET') {

            $queryBuilder = $modelClass::whereRaw('1=1');
            $queryBuilder = $this->handleQueries($queryBuilder);
            $queryBuilder = $this->handleScopes($queryBuilder);

            return [
                Str::plural($modelSlug) => $queryBuilder->get()
            ];

        } elseif ($id) {

            $model = $modelClass::findOrFail($id);

            switch ($request->method) {
                case 'PUT':
                    $model->update($data);
                    break;

                case '\DELETE':
                    $model->delete();
                    break;
            }

        } elseif ($request->method === 'POST') {

            $model = $modelClass::create($data);

        }

        return [
            $modelSlug => $model
        ];
    }

    private function modelClass(string $modelSlug)
    {
        $modelName = Str::ucfirst(Str::camel($modelSlug));

        return "\\App\\Models\\{$modelName}";
    }

    private function data()
    {
        return collect($this->request->all())->except([
            'model', 
            'methods',
            'queries',
            'id',
        ]);
    }

    private function handleQueries(Builder $queryBuilder): Builder
    {
        if (! $queries = $this->request->queries) {
            return $queryBuilder;
        }

        // Array<Array<[string $field, string $operator, string $value]>
        $query = collect($queries)->map(fn ($q) => [
          $q[0], // Field
          $q[1], // Operator
          $q[2]  // Value
        ]);

        $queryBuilder = $queryBuilder->where(
            $query->all()
        );

        return $queryBuilder;
    }

    private function handleScopes(Builder $queryBuilder): Builder
    {
        if (! $scopes = $this->request->scopes) {
            return $queryBuilder;
        }

        // Array<[string $name, array $params]>
        foreach ($scopes as $scope) {
            $name = $scope['name'];
            $params = $scope['params'];

            $queryBuilder = $queryBuilder->$name(...$params);
        }
      
        return $queryBuilder;
    }
}