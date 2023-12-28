<?php

namespace Sunmking\Think8Vite\collections;

use Closure;
use JsonSerializable;
use Stringable;
use Sunmking\Think8Vite\exception\InvalidArgumentException;
use Sunmking\Think8Vite\support\Enumerable;
use think\Collection;
use think\contract\Arrayable;
use think\contract\Jsonable;
use Think8Arr;
use Traversable;
use UnitEnum;
use WeakMap;

class Think8Collection extends Collection
{
    public function __construct($items = [])
    {
        parent::__construct($items);
    }

    /**
     * Results array of items from Collection or Arrayable.
     *
     * @param mixed $items
     * @return array
     * @throws InvalidArgumentException
     */
    protected function getArrayableItems($items): array
    {
        if (is_array($items)) {
            return $items;
        }

        return match (true) {
            $items instanceof WeakMap => throw new InvalidArgumentException('Collections can not be created using instances of WeakMap.'),
            $items instanceof Enumerable => $items->all(),
            $items instanceof Arrayable => $items->toArray(),
            $items instanceof Traversable => iterator_to_array($items),
            $items instanceof Jsonable => json_decode($items->toJson(), true),
            $items instanceof JsonSerializable => (array) $items->jsonSerialize(),
            $items instanceof UnitEnum => [$items],
            default => (array) $items,
        };
    }

    /**
     * Push an item onto the beginning of the collection.
     * @param $value
     * @param $key
     * @return $this
     */
    public function prepend($value, $key = null)
    {
        $this->items = Think8Arr::prepend($this->items, ...func_get_args());

        return $this;
    }

    /**
     * Join all items from the collection using a string. The final items can use a separate glue string.
     *
     * @param  string  $glue
     * @param  string  $finalGlue
     * @return string
     */
    public function join($glue, $finalGlue = '')
    {
        if ($finalGlue === '') {
            return $this->implode($glue);
        }

        $count = $this->count();

        if ($count === 0) {
            return '';
        }

        if ($count === 1) {
            return $this->last();
        }

        $collection = new static($this->items);

        $finalItem = $collection->pop();

        return $collection->implode($glue).$finalGlue.$finalItem;
    }

    /**
     * Concatenate values of a given key as a string.
     *
     * @param  callable|string  $value
     * @param  string|null  $glue
     * @return string
     */
    public function implode($value, $glue = null): string
    {
        if ($this->useAsCallable($value)) {
            return implode($glue ?? '', $this->map($value)->all());
        }

        $first = $this->first();

        if (is_array($first) || (is_object($first) && ! $first instanceof Stringable)) {
            return implode($glue ?? '', $this->pluck($value)->all());
        }

        return implode($value ?? '', $this->items);
    }

    /**
     * Get the values of a given key.
     *
     * @param  string|int|array<array-key, string>  $value
     * @param  string|null  $key
     * @return static<array-key, mixed>
     */
    public function pluck($value, $key = null): static
    {
        return new static(Think8Arr::pluck($this->items, $value, $key));
    }

    /**
     * Return only unique items from the collection array.
     *
     * @param $key
     * @param $strict
     * @return $this
     */
    public function unique($key = null, $strict = false): static
    {
        if (is_null($key) && $strict === false) {
            return new static(array_unique($this->items, SORT_REGULAR));
        }

        $callback = $this->valueRetriever($key);

        $exists = [];

        return $this->reject(function ($item, $key) use ($callback, $strict, &$exists) {
            if (in_array($id = $callback($item, $key), $exists, $strict)) {
                return true;
            }

            $exists[] = $id;
        });
    }

    /**
     * Create a collection of all elements that do not pass a given truth test.
     * @param $callback
     * @return $this
     */
    public function reject($callback = true): static
    {
        $useAsCallable = $this->useAsCallable($callback);

        return $this->filter(function ($value, $key) use ($callback, $useAsCallable) {
            return $useAsCallable
                ? ! $callback($value, $key)
                : $value != $callback;
        });
    }

    /**
     * Partition the collection into two arrays using the given callback or key.
     *
     */
    public function partition($key, $operator = null, $value = null)
    {
        $passed = [];
        $failed = [];

        $callback = func_num_args() === 1
            ? $this->valueRetriever($key)
            : $this->operatorForWhere(...func_get_args());

        foreach ($this as $key => $item) {
            if ($callback($item, $key)) {
                $passed[$key] = $item;
            } else {
                $failed[$key] = $item;
            }
        }

        return new static([new static($passed), new static($failed)]);
    }

    /**
     * @throws InvalidArgumentException
     */
    public function forget($keys): static
    {
        foreach ($this->getArrayableItems($keys) as $key) {
            $this->offsetUnset($key);
        }

        return $this;
    }

    /**
     * Get a value retrieving callback.
     *
     * @param callable|string|null $value
     * @return callable|string|null
     */
    protected function valueRetriever($value): callable|string|null
    {
        if ($this->useAsCallable($value)) {
            return $value;
        }

        return fn ($item) => data_get($item, $value);
    }

    /**
     * Sort the collection in descending order using the given callback.
     *
     * @param $callback
     * @param $options
     * @return $this
     */
    public function sortByDesc($callback, $options = SORT_REGULAR): static
    {
        return $this->sortBy($callback, $options, true);
    }

    /**
     * @param $callback
     * @param $options
     * @param $descending
     * @return $this
     */
    public function sortBy($callback, $options = SORT_REGULAR, $descending = false): static
    {
        if (is_array($callback) && ! is_callable($callback)) {
            return $this->sortByMany($callback);
        }

        $results = [];

        $callback = $this->valueRetriever($callback);

        // First we will loop through the items and get the comparator from a callback
        // function which we were given. Then, we will sort the returned values and
        // grab all the corresponding values for the sorted keys from this array.
        foreach ($this->items as $key => $value) {
            $results[$key] = $callback($value, $key);
        }

        $descending ? arsort($results, $options)
            : asort($results, $options);

        // Once we have sorted all of the keys in the array, we will loop through them
        // and grab the corresponding model so we can set the underlying items list
        // to the sorted version. Then we'll just return the collection instance.
        foreach (array_keys($results) as $key) {
            $results[$key] = $this->items[$key];
        }

        return new static($results);
    }

    /**
     * Sort the collection using multiple comparisons.
     *
     * @param array $comparisons
     * @return $this
     */
    protected function sortByMany(array $comparisons = []): static
    {
        $items = $this->items;

        uasort($items, function ($a, $b) use ($comparisons) {
            foreach ($comparisons as $comparison) {
                $comparison = Think8Arr::wrap($comparison);

                $prop = $comparison[0];

                $ascending = Think8Arr::get($comparison, 1, true) === true ||
                    Think8Arr::get($comparison, 1, true) === 'asc';

                if (! is_string($prop) && is_callable($prop)) {
                    $result = $prop($a, $b);
                } else {
                    $values = [data_get($a, $prop), data_get($b, $prop)];

                    if (! $ascending) {
                        $values = array_reverse($values);
                    }

                    $result = $values[0] <=> $values[1];
                }

                if ($result === 0) {
                    continue;
                }

                return $result;
            }
        });

        return new static($items);
    }


    /**
     * Sort the collection keys.
     *
     * @param  int  $options
     * @param  bool  $descending
     * @return static
     */
    public function sortKeys($options = SORT_REGULAR, $descending = false)
    {
        $items = $this->items;

        $descending ? krsort($items, $options) : ksort($items, $options);

        return new static($items);
    }

    /**
     * Sort the collection keys in descending order.
     *
     * @param  int  $options
     * @return static
     */
    public function sortKeysDesc($options = SORT_REGULAR)
    {
        return $this->sortKeys($options, true);
    }

    /**
     * Sort the collection keys using a callback.
     * @param callable $callback
     * @return $this
     */
    public function sortKeysUsing(callable $callback): static
    {
        $items = $this->items;

        uksort($items, $callback);

        return new static($items);
    }

    /**
     * Map a collection and flatten the result by a single level.
     *
     * @param callable $callback
     * @return Think8Collection
     */
    public function flatMap(callable $callback): static
    {
        return $this->map($callback)->collapse();
    }

    /**
     * Run a map over each of the items.
     *
     * @param callable $callback
     * @return $this|Think8Collection
     */
    public function map(callable $callback): Think8Collection|static
    {
        return new static(Think8Arr::map($this->items, $callback));
    }
    /**
     * Map a collection and group the result by a given key.
     *
     * @param callable|string $keyBy
     * @return static
     */
    public function keyBy($keyBy): static
    {
        $keyBy = $this->valueRetriever($keyBy);

        $results = [];

        foreach ($this->items as $key => $item) {
            $resolvedKey = $keyBy($item, $key);

            if (is_object($resolvedKey)) {
                $resolvedKey = (string) $resolvedKey;
            }

            $results[$resolvedKey] = $item;
        }

        return new static($results);
    }

    public function mapWithKeys(callable $callback): static
    {
        return new static(Think8Arr::mapWithKeys($this->items, $callback));
    }

    /**
     * Collapse the collection of items into a single array.
     *
     * @return $this
     */
    public function collapse(): static
    {
        return new static(Think8Arr::collapse($this->items));
    }

    /**
     * Get an operator checker callback.
     *
     * @param callable|string $key
     * @param null $operator
     * @param mixed $value
     * @return callable|Closure|string
     */
    protected function operatorForWhere($key, $operator = null, $value = null): callable|Closure|string
    {
        if ($this->useAsCallable($key)) {
            return $key;
        }

        if (func_num_args() === 1) {
            $value = true;

            $operator = '=';
        }

        if (func_num_args() === 2) {
            $value = $operator;

            $operator = '=';
        }

        return function ($item) use ($key, $operator, $value) {
            $retrieved = data_get($item, $key);

            $strings = array_filter([$retrieved, $value], function ($value) {
                return is_string($value) || (is_object($value) && method_exists($value, '__toString'));
            });

            if (count($strings) < 2 && count(array_filter([$retrieved, $value], 'is_object')) == 1) {
                return in_array($operator, ['!=', '<>', '!==']);
            }

            switch ($operator) {
                default:
                case '=':
                case '==':  return $retrieved == $value;
                case '!=':
                case '<>':  return $retrieved != $value;
                case '<':   return $retrieved < $value;
                case '>':   return $retrieved > $value;
                case '<=':  return $retrieved <= $value;
                case '>=':  return $retrieved >= $value;
                case '===': return $retrieved === $value;
                case '!==': return $retrieved !== $value;
                case '<=>': return $retrieved <=> $value;
            }
        };
    }

    /**
     * Determine if the given value is callable, but not a string.
     *
     * @param  mixed  $value
     * @return bool
     */
    protected function useAsCallable($value): bool
    {
        return ! is_string($value) && is_callable($value);
    }
}