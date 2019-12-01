<?php
/**
 * Define a basic field.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2019
 * @license MIT
 */

namespace Laramore\Fields;

use Illuminate\Support\{
    Str, Collection
};
use Laramore\Eloquent\{
    Builder, Model
};
use Laramore\Meta;
use Laramore\Interfaces\IsProxied;
use Laramore\Elements\{
    Type, Operator
};
use Laramore\Facades\{
    Rules, Operators
};
use Laramore\Traits\Field\HasFieldConstraints;

abstract class Field extends BaseField
{
    use HasFieldConstraints;

    /**
     * Attribute name of this field.
     *
     * @var string
     */
    protected $attname;

    /**
     * Parse the attribute attname.
     *
     * @param  string $attname
     * @return string
     */
    public static function parseAttname(string $attname): string
    {
        return Str::snake($attname);
    }

    /**
     * Define the name property.
     *
     * @param  string $name
     * @return self
     */
    protected function setName(string $name, string $attname=null)
    {
        parent::setName($name);

        // If no attribute name have been set by the user, define ours based on the name.
        if (\is_null($this->attname)) {
            $this->setAttname(static::parseAttname($name));
        }

        return $this;
    }

    /**
     * Each class locks in a specific way.
     *
     * @return void
     */
    protected function locking()
    {
        parent::locking();

        $this->setConstraints();
    }

    /**
     * Add a where condition from this field.
     *
     * @param  Builder $builder
     * @param  mixed   $value
     * @param  string  $boolean
     * @param  boolean $not
     * @return Builder|void
     */
    public function whereNull(Builder $builder, $value=null, string $boolean='and', bool $not=false)
    {
        $builder->getQuery()->whereNull($this->attname, $boolean, $not);

        return $builder;
    }

    /**
     * Add a where condition from this field.
     *
     * @param  Builder $builder
     * @param  mixed   $value
     * @param  string  $boolean
     * @return Builder|void
     */
    public function whereNotNull(Builder $builder, $value=null, string $boolean='and')
    {
        return $this->whereNull($builder, $value, $boolean, true);
    }

    /**
     * Add a where condition from this field.
     *
     * @param  Builder    $builder
     * @param  Collection $value
     * @param  string     $boolean
     * @param  boolean    $notIn
     * @return Builder|void
     */
    public function whereIn(Builder $builder, Collection $value=null, string $boolean='and', bool $notIn=false)
    {
        $builder->whereIn($this->attname, $value, $boolean, $notIn);
    }

    /**
     * Add a where condition from this field.
     *
     * @param  Builder    $builder
     * @param  Collection $value
     * @param  string     $boolean
     * @return Builder|void
     */
    public function whereNotIn(Builder $builder, Collection $value=null, string $boolean='and')
    {
        return $this->whereIn($builder, $value, $boolean, true);

        return $builder;
    }

    /**
     * Add a where condition from this field.
     *
     * @param  Builder  $builder
     * @param  Operator $operator
     * @param  mixed    $value
     * @param  string   $boolean
     * @return Builder|void
     */
    public function where(Builder $builder, Operator $operator, $value=null, string $boolean='and')
    {
        $builder->getQuery()->where($this->attname, $operator, $value, $boolean);

        return $builder;
    }

    /**
     * Return the query with this field as condition.
     *
     * @param  IsProxied $instance
     * @return Builder
     */
    public function relate(IsProxied $instance)
    {
        if ($instance instanceof Model) {
            return $this->where($instance, Operators::equal(), $instance->getAttribute($this->attname));
        }

        if ($instance instanceof Builder) {
            return $this->where($instance, Operators::equal(), $instance->getModel()->getAttribute($this->attname));
        }
    }
}
