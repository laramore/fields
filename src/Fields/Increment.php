<?php
/**
 * Define an increment field.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2019
 * @license MIT
 */

namespace Laramore\Fields;

use Laramore\Interfaces\IsALaramoreModel;
use Laramore\Elements\Type;

class Increment extends Integer
{
    /**
     * Return the type object of the field.
     *
     * @return Type
     */
    public function getType(): Type
    {
        return $this->resolveType();
    }

    public function getPropertyKeys(): array
    {
        $keys = parent::getPropertyKeys();

        if (!\is_null($index = \array_search('unsigned', $keys))) {
            unset($keys[$index]);
        }

        return $keys;
    }

    public function increment(IsALaramoreModel $model, int $value, int $increment=1)
    {
        return $model->setAttribute($this->attname, ($value + $increment));
    }

    public function decrement(IsALaramoreModel $model, int $value, int $decrement=1)
    {
        return $this->increment($model, $value, - $decrement);
    }
}
