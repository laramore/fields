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
use Types;

class Increment extends Integer
{
    public function getType(): Type
    {
        return Types::increment();
    }

    public function getPropertyKeys(): array
    {
        $keys = parent::getPropertyKeys();

        if (!\is_null($index = \array_search('unsigned', $keys))) {
            unset($keys[$index]);
        }

        return $keys;
    }

    protected function setProxies()
    {
        parent::setProxies();

        $this->setProxy('increment', ['instance', 'value']);
        $this->setProxy('descrement', ['instance', 'value']);
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
