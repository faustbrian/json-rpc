<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\JsonRpc\QueryBuilders;

use Cline\JsonRpc\Exceptions\InvalidFieldsException;
use Cline\JsonRpc\Exceptions\InvalidFiltersException;
use Cline\JsonRpc\Exceptions\InvalidRelationshipsException;
use Cline\JsonRpc\Exceptions\InvalidSortsException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Traits\ForwardsCalls;

use function array_key_exists;
use function in_array;

/**
 * @author Brian Faust <brian@cline.sh>
 *
 * @mixin Builder
 */
final class QueryBuilder
{
    use ForwardsCalls;

    private array $queryFields = [];

    private array $queryFilters = [];

    private array $queryRelationships = [];

    private array $querySorts = [];

    private readonly string $model;

    private readonly string $modelTable;

    private readonly string $modelType;

    private readonly Builder $subject;

    private function __construct(
        private readonly string $resource,
        private readonly array $requestFields,
        private readonly array $allowedFields,
        private readonly array $requestFilters,
        private readonly array $allowedFilters,
        private readonly array $requestRelationships,
        private readonly array $allowedRelationships,
        private readonly array $requestSorts,
        private readonly array $allowedSorts,
    ) {
        $this->model = $resource::getModel();
        $this->modelTable = $resource::getModelTable();
        $this->modelType = $resource::getModelType();
        $this->subject = $resource::getModel()::query();

        $this->collectFields();
        $this->collectFilters();
        $this->collectRelationships();
        $this->collectSorts();

        $this->applyToQuery();
    }

    public function __call($name, $arguments)
    {
        $result = $this->forwardCallTo($this->subject, $name, $arguments);

        /*
         * If the forwarded method call is part of a chain we can return $this
         * instead of the actual $result to keep the chain going.
         */
        if ($result === $this->subject) {
            return $this;
        }

        return $result;
    }

    public static function for(
        string $resource,
        array $requestFields,
        array $allowedFields,
        array $requestFilters,
        array $allowedFilters,
        array $requestRelationships,
        array $allowedRelationships,
        array $requestSorts,
        array $allowedSorts,
    ): static {
        return new self(
            $resource,
            $requestFields,
            $allowedFields,
            $requestFilters,
            $allowedFilters,
            $requestRelationships,
            $allowedRelationships,
            $requestSorts,
            $allowedSorts,
        );
    }

    private function collectFields(): void
    {
        foreach ($this->requestFields as $resourceName => $resourceFields) {
            foreach ($resourceFields as $resourceField) {
                $allowedFields = $this->allowedFields[$resourceName] ?? [];

                if (!in_array($resourceField, $allowedFields, true)) {
                    throw InvalidFieldsException::create($resourceFields, $allowedFields);
                }
            }

            $this->queryFields[$resourceName] = $resourceFields;
        }
    }

    private function collectFilters(): void
    {
        foreach ($this->requestFilters as $resourceName => $resourceFilters) {
            foreach ($resourceFilters as $resourceFilter) {
                $attribute = $resourceFilter['attribute'] ?? null;
                $allowedFilters = $this->allowedFilters[$resourceName] ?? [];

                if (!in_array($attribute, $allowedFilters, true)) {
                    throw InvalidFiltersException::create([$attribute], $allowedFilters);
                }

                $this->queryFilters[$resourceName][] = $resourceFilter;
            }
        }
    }

    private function collectRelationships(): void
    {
        foreach ($this->requestRelationships as $resourceName => $relationships) {
            foreach ($relationships as $relationship) {
                $allowedRelationships = $this->allowedRelationships[$resourceName] ?? [];

                if (!in_array($relationship, $allowedRelationships, true)) {
                    throw InvalidRelationshipsException::create(
                        $this->requestRelationships[$resourceName],
                        $allowedRelationships,
                    );
                }
            }
        }

        $this->queryRelationships = $this->requestRelationships;
    }

    private function collectSorts(): void
    {
        foreach ($this->requestSorts as $resourceName => $resourceSorts) {
            foreach ($resourceSorts as $resourceSort) {
                $allowedSorts = $this->allowedSorts[$resourceName] ?? [];

                if (!in_array($resourceSort['attribute'], $allowedSorts, true)) {
                    throw InvalidSortsException::create($resourceSorts, $allowedSorts);
                }

                $this->querySorts[$resourceName][] = $resourceSort;
            }
        }
    }

    private function applyToQuery(): void
    {
        // Arrange...
        $withs = [];

        // Relationships...
        foreach ($this->queryRelationships as $relationshipResource => $relationships) {
            foreach ($relationships as $relationship) {
                if ($relationshipResource === 'self') {
                    $withs[$relationship] = fn (Builder|Relation $query): Builder|\Illuminate\Database\Eloquent\Relations\Relation => $query;
                } elseif (array_key_exists($relationshipResource, $withs)) {
                    $withs[$relationship] = $withs[$relationship]->with($relationship);
                } else {
                    $withs[$relationship] = fn (Builder|Relation $query) => $query->with($relationship);
                }
            }
        }

        // Fields...
        foreach ($this->queryFields as $fieldResource => $fields) {
            if ($fieldResource === 'self') {
                $this->select($fields);
            } elseif (array_key_exists($fieldResource, $withs)) {
                $withs[$fieldResource] = $withs[$fieldResource]->select($fields);
            } else {
                $withs[$fieldResource] = fn (Builder|Relation $query) => $query->select($fields);
            }
        }

        // Filters...
        foreach ($this->queryFilters as $filterResource => $filters) {
            $filterRelationships = [];

            foreach ($filters as $filter) {
                if ($filterResource === 'self') {
                    $this->applyFilter($this, $filter);
                } elseif (array_key_exists($filterResource, $filterRelationships)) {
                    $filterRelationships[$filterResource] = $this->applyFilter($filterRelationships[$filterResource], $filter);
                } else {
                    $filterRelationships[$filterResource] = fn (Builder|Relation $query): Builder|\Illuminate\Database\Eloquent\Relations\Relation|\Cline\JsonRpc\QueryBuilders\QueryBuilder => $this->applyFilter($query, $filter);
                }
            }

            foreach ($filterRelationships as $filterRelationshipName => $filterRelationshipQuery) {
                $this->whereHas($filterRelationshipName, $filterRelationshipQuery);
            }
        }

        // Sorts...
        foreach ($this->querySorts as $sortResource => $sorts) {
            foreach ($sorts as $sort) {
                if ($sortResource === 'self') {
                    $this->orderBy($sort['attribute'], $sort['direction']);
                } elseif (array_key_exists($sortResource, $withs)) {
                    $withs[$sortResource] = $withs[$sortResource]->orderBy($sort['attribute'], $sort['direction']);
                } else {
                    $withs[$sortResource] = fn (Builder|Relation $query) => $query->orderBy($sort['attribute'], $sort['direction']);
                }
            }
        }

        // Act...
        $this->with($withs);
    }

    private function applyFilter(Builder|Relation|self $query, array $filter): Builder|Relation|self
    {
        $attribute = $filter['attribute'] ?? null;
        $value = $filter['value'] ?? null;
        $boolean = $filter['boolean'] ?? 'and';

        match ($filter['operator'] ?? null) {
            'equals' => $query->where($attribute, '=', $value, $boolean),
            'not_equals' => $query->where($attribute, '!=', $value, $boolean),
            'greater_than' => $query->where($attribute, '>', $value, $boolean),
            'greater_than_or_equal_to' => $query->where($attribute, '>=', $value, $boolean),
            'less_than' => $query->where($attribute, '<', $value, $boolean),
            'less_than_or_equal_to' => $query->where($attribute, '<=', $value, $boolean),
            'like' => $query->where($attribute, 'like', $value, $boolean),
            'not_like' => $query->where($attribute, 'not like', $value, $boolean),
            'in' => $query->whereIn($attribute, $value, $boolean),
            'not_in' => $query->whereNotIn($attribute, $value, $boolean),
            'between' => $query->whereBetween($attribute, $value, $boolean),
            'not_between' => $query->whereNotBetween($attribute, $value, $boolean),
            'is_null' => $query->whereNull($attribute, $boolean),
            'is_not_null' => $query->whereNotNull($attribute, $boolean),
            default => throw InvalidFiltersException::create([$attribute], $this->allowedFilters),
        };

        return $query;
    }
}
