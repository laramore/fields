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
use Laramore\Traits\Field\HasFieldConstraints;
use Laramore\Validations\Typed;
use Rules;

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
     * Define the name property.
     *
     * @param  string $name
     * @return self
     */
    protected function setName(string $name)
    {
        parent::setName($name);

        // The attribute name is by default the same as the field name.
        if (is_null($this->attname)) {
            $this->attname = Str::snake($name);
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
     * Define all validations.
     *
     * @return void
     */
    protected function setValidations()
    {
        parent::setValidations();

        // TODO.
        // $this->setValidation(Typed::class)->type($this->getType());.
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
            return $this->where($instance, Op::equal(), $instance->getAttribute($this->attname));
        }

        if ($instance instanceof Builder) {
            return $this->where($instance, Op::equal(), $instance->getModel()->getAttribute($this->attname));
        }
    }
}
