<?php
/**
 * Define an attribute field.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2019
 * @license MIT
 */

namespace Laramore\Fields;

use Illuminate\Support\Collection;
use Laramore\Eloquent\{
    Builder, Model
};
use Laramore\Interfaces\IsProxied;
use Laramore\Elements\OperatorElement;
use Laramore\Facades\Operator;
use Laramore\Traits\Field\HasFieldConstraints;

abstract class AttributeField extends BaseField
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
        return static::replaceInTemplate(config('field.attname_template'), compact('attname'));
    }

    /**
     * Define the name property.
     *
     * @param  string $name
     * @param  string $attname
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
     * Return the native value of this field.
     * Commonly, its attname.
     *
     * @return string
     */
    public function getNative(): string
    {
        return $this->attname;
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
     * Add an operation to a query builder.
     *
     * @param Builder $builder
     * @param string  $operation
     * @param mixed   ...$params
     * @return Builder
     */
    public function addBuilderOperation(Builder $builder, string $operation, ...$params): Builder
    {
        \call_user_func([$builder->getQuery(), $operation], $this->getFullName(), ...$params);

        return $builder;
    }

    /**
     * Add a where null condition from this field.
     *
     * @param  Builder $builder
     * @param  mixed   $value
     * @param  string  $boolean
     * @param  boolean $not
     * @return Builder
     */
    public function whereNull(Builder $builder, $value=null, string $boolean='and', bool $not=false): Builder
    {
        return $this->addBuilderOperation($builder, 'whereNull', $boolean, $not);
    }

    /**
     * Add a where not null condition from this field.
     *
     * @param  Builder $builder
     * @param  mixed   $value
     * @param  string  $boolean
     * @return Builder
     */
    public function whereNotNull(Builder $builder, $value=null, string $boolean='and'): Builder
    {
        return $this->whereNull($builder, $value, $boolean, true);
    }

    /**
     * Add a where in condition from this field.
     *
     * @param  Builder    $builder
     * @param  Collection $value
     * @param  string     $boolean
     * @param  boolean    $notIn
     * @return Builder
     */
    public function whereIn(Builder $builder, Collection $value=null, string $boolean='and', bool $notIn=false): Builder
    {
        return $this->addBuilderOperation($builder, 'whereIn', $value, $boolean, $notIn);
    }

    /**
     * Add a where not in condition from this field.
     *
     * @param  Builder    $builder
     * @param  Collection $value
     * @param  string     $boolean
     * @return Builder
     */
    public function whereNotIn(Builder $builder, Collection $value=null, string $boolean='and'): Builder
    {
        return $this->whereIn($builder, $value, $boolean, true);
    }

    /**
     * Add a where condition from this field.
     *
     * @param  Builder         $builder
     * @param  OperatorElement $operator
     * @param  mixed           $value
     * @param  string          $boolean
     * @return Builder
     */
    public function where(Builder $builder, OperatorElement $operator, $value=null, string $boolean='and'): Builder
    {
        return $this->addBuilderOperation($builder, 'where', $operator, $value, $boolean);
    }

    /**
     * Return the query with this field as condition.
     *
     * @param  IsProxied $instance
     * @return Builder
     */
    public function relate(IsProxied $instance): Builder
    {
        if ($instance instanceof Model) {
            return $this->where($instance, Operator::equal(), $instance->getAttribute($this->attname));
        }

        if ($instance instanceof Builder) {
            return $this->where($instance, Operator::equal(), $instance->getModel()->getAttribute($this->attname));
        }
    }
}
