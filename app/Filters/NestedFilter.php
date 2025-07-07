<?php

declare(strict_types=1);

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;

class NestedFilter
{
    public function __construct(private Builder $query)
    {

    }

    public function apply(array $filters): Builder
    {
        $this->applyFilters($this->query, $filters);
        return $this->query;
    }

    private function applyFilters(Builder $query, array $filters, string $condition = 'and'): void
    {
        foreach ($filters as $key => $filter) {

            if ($key === 'and' || $key === 'or') {
                foreach ($filter as $filterNextLevel) {
                    $this->applyFilters($query, $filterNextLevel, $key);
                }
                continue;
            }

            $dotNotation = explode('.', $key);
            $isDotNotation = count($dotNotation) > 1;

            if ($isDotNotation) {
                $this->setRelationCondition($query, $key, $filter, $condition);
            } else {
                $this->setCondition($query, $key, $filter, $condition);
            }
        }
    }

    private function setRelationCondition(Builder $query, string $relations, mixed $filter, string $condition): void
    {
        $method = $condition === 'and' ? 'whereHas' : 'orWhereHas';
        $relation = explode('.', $relations);
        $onlyRelation = array_splice($relation, 0, -1);
        $onlyRelationStr = count($onlyRelation) > 1
            ? implode('.', $onlyRelation)
            : implode($onlyRelation);
        $operator = '=';
        $value = $filter;

        if (is_array($filter)) {
            $operator = $filter['operator'];
            $value = $filter['value'];
        }

        // Only checks current model relations
        // if not exists set a simple condition with fallback to the current model
        if (!in_array($onlyRelation[0], $this->getRelations())) {
            $newFilter = ['operator' => $operator, 'value' => $value];
            $this->setCondition($query, $relation[0], $newFilter, $condition);
            return;
        }

        $query->$method($onlyRelationStr, function (Builder $q) use ($operator, $relation, $value, $onlyRelationStr) {
            $this->query->with($onlyRelationStr);
            $q->where($relation[0], $operator, $value);
        });
    }

    private function setCondition(Builder $query, string $column, mixed $filter, string $condition): void
    {
        $method = $condition === 'and' ? 'where' : 'orWhere';
        $operator = '=';
        $value = $filter;
        if (is_array($filter)) {
            $operator = $filter['operator'];
            $value = $filter['value'];
        }
        $query->$method($this->getDbAlias() . ".$column", $operator, $value);
    }

    private function getRelations(): array
    {
        try {
            $reflected = new ReflectionClass($this->query->getModel());
            $methods = collect($reflected->getMethods());
            return $methods
                ->filter(fn(ReflectionMethod $method) => $method->isPublic()
                    && $method->hasReturnType()
                    && $method->getReturnType()->getName() === BelongsTo::class)
                ->map(fn(ReflectionMethod $method) => $method->getName())->toArray();
        } catch (ReflectionException $e) {
            return [];
        }
    }

    private function getDbAlias(): string
    {
        return $this->query->getModel()->getTable();
    }

}
