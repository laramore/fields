<?php
/**
 * Define an uuid OneToMany field.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2019
 * @license MIT
 */

namespace Laramore\Fields;

use Illuminate\Support\Collection;
use Laramore\Contracts\{
    Eloquent\LaramoreModel, Eloquent\LaramoreBuilder, Field\Field, Field\ExtraField
};
use Laramore\Elements\OperatorElement;
use Laramore\Traits\Field\ModelExtra;

class Name extends BaseComposed implements ExtraField
{
    use ModelExtra {
        ModelExtra::set as protected setValue;
        ModelExtra::reset as protected resetValue;
    }

    protected $nameFirst = true;
    protected $maxLength;

    /**
     * Dry the value in a simple format.
     *
     * @param  mixed $value
     * @return mixed
     */
    public function dry($value)
    {
        return is_null($value) ? $value : (string) $value;
    }

    /**
     * Cast the value in the correct format.
     *
     * @param  mixed $value
     * @return mixed
     */
    public function cast($value)
    {
        return is_null($value) ? $value : (string) $value;
    }

    /**
     * Transform the value to correspond to the field desire.
     *
     * @param  mixed $value
     * @return mixed
     */
    public function transform($value)
    {
        return $value;
    }

    /**
     * Serialize the value for outputs.
     *
     * @param  mixed $value
     * @return mixed
     */
    public function serialize($value)
    {
        return $value;
    }

    /**
     * Add a where null condition from this field.
     *
     * @param  LaramoreBuilder $builder
     * @param  mixed           $value
     * @param  string          $boolean
     * @param  boolean         $not
     * @return LaramoreBuilder
     */
    public function whereNull(LaramoreBuilder $builder, $value=null, string $boolean='and', bool $not=false): LaramoreBuilder
    {
        $builder = $this->getField('firstname')->addBuilderOperation($builder, 'whereNull', null, $boolean, $not);
        return $this->getField('lastname')->addBuilderOperation($builder, 'whereNull', $boolean, $not);
    }

    /**
     * Add a where not null condition from this field.
     *
     * @param  LaramoreBuilder $builder
     * @param  mixed           $value
     * @param  string          $boolean
     * @return LaramoreBuilder
     */
    public function whereNotNull(LaramoreBuilder $builder, $value=null, string $boolean='and'): LaramoreBuilder
    {
        return $this->whereNull($builder, $value, $boolean, true);
    }

    /**
     * Add a where in condition from this field.
     *
     * @param  LaramoreBuilder $builder
     * @param  Collection      $value
     * @param  string          $boolean
     * @param  boolean         $notIn
     * @return LaramoreBuilder
     */
    public function whereIn(LaramoreBuilder $builder, Collection $value=null, string $boolean='and', bool $notIn=false): LaramoreBuilder
    {

    }

    /**
     * Add a where not in condition from this field.
     *
     * @param  LaramoreBuilder $builder
     * @param  Collection      $value
     * @param  string          $boolean
     * @return LaramoreBuilder
     */
    public function whereNotIn(LaramoreBuilder $builder, Collection $value=null, string $boolean='and'): LaramoreBuilder
    {

    }

    /**
     * Add a where condition from this field.
     *
     * @param  LaramoreBuilder $builder
     * @param  OperatorElement $operator
     * @param  mixed           $value
     * @param  string          $boolean
     * @return LaramoreBuilder
     */
    public function where(LaramoreBuilder $builder, OperatorElement $operator, $value=null, string $boolean='and'): LaramoreBuilder
    {

    }

    /**
     * Retrieve the value definied by the field.
     *
     * @param  LaramoreModel $model
     * @return mixed
     */
    public function retrieve(LaramoreModel $model)
    {
        return $this->join($this->getField('lastname')->get($model), $this->getField('firstname')->get($model));
    }

    /**
     * Use the relation to set the other field values.
     *
     * @param  LaramoreModel $model
     * @param  mixed         $value
     *
     * @return mixed
     */
    public function set(LaramoreModel $model, $value)
    {
        [$lastname, $firstname] = $this->split($value);

        $this->getField('lastname')->set($model, $lastname);
        $this->getField('firstname')->set($model, $firstname);

        return $this->setValue($model, $value);
    }

    /**
     * Reset the value for the field.
     *
     * @param  LaramoreModel $model
     * @return mixed
     */
    public function reset(LaramoreModel $model)
    {
        $this->getField('lastname')->reset($model);
        $this->getField('firstname')->reset($model);

        if ($this->hasDefault()) {
            $model->setExtraValue($this->getNative(), $value = $this->getDefault());

            return $value;
        }

        return $this->resetValue($model);
    }

    /**
     * Return the set value for a specific field.
     * z
     * @param Field         $field
     * @param LaramoreModel $model
     * @param mixed         $value
     * @return mixed
     */
    public function setFieldValue(Field $field, LaramoreModel $model, $value)
    {
        $result = parent::setFieldValue($field, $model, $value);

        $model->setExtraValue($this->getNative(), $this->retrieve($model));

        return $result;
    }

    public function split($value)
    {
        if ($this->nameFirst) {
            $matched = \preg_match('/([A-Z _-]+) (([A-Z][a-z_-]+ *)+)/', $value, $matches);
        } else {
            $matched = \preg_match('/(([A-Z][a-z_-]+ *)+) ([A-Z _-]+)/', $value, $matches);
        }

        if (!$matched) {
            throw new \Exception("The value `$value` is not a valid name");
        }

        return [$matches[1], $matches[2]];
    }

    public function join($lastname, $firstname): string
    {
        if ($this->nameFirst) {
            return $lastname.' '.$firstname;
        }

        return $firstname.' '.$lastname;

    }
}
