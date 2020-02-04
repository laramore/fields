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

use Laramore\Interfaces\{
    IsALaramoreModel, IsAnIncrementingField
};
use Laramore\Elements\TypeElement;

class Increment extends Integer implements IsAnIncrementingField
{
    /**
     * Return the type object of the field.
     *
     * @return TypeElement
     */
    public function getType(): TypeElement
    {
        return $this->resolveType();
    }

    public function increment(IsALaramoreModel $model, $value, $increment=1)
    {
        return $model->setAttribute($this->attname, ($value + $increment));
    }

    public function decrement(IsALaramoreModel $model, $value, $decrement=1)
    {
        return $this->increment($model, $value, - $decrement);
    }
}
