<?php
/**
 * Define a field constraint.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2019
 * @license MIT
 */

namespace Laramore\Fields\Constraint;

use Illuminate\Support\{
    Str, Facades\Event
};
use Illuminate\Container\Container;
use Laramore\Contracts\Configured;
use Laramore\Exceptions\LockException;
use Laramore\Contracts\Field\{
    AttributeField, Constraint\ConstraintedField, Constraint\Constraint
};
use Laramore\Observers\BaseObserver;

abstract class BaseConstraint extends BaseObserver implements Constraint, Configured
{
    /**
     * All possible constraint types.
     */
    const FOREIGN = 'foreign';
    const INDEX = 'index';
    const PRIMARY = 'primary';
    const UNIQUE = 'unique';

    /**
     * An observer needs at least a name.
     *
     * @param ConstraintedField|array<ConstraintedField> $attributes
     * @param string                                     $name
     * @param integer                                    $priority
     */
    protected function __construct($attributes, string $name=null, int $priority=self::MEDIUM_PRIORITY)
    {
        if (!\is_null($name)) {
            $this->setName($name);
        }

        $this->on($attributes);
        $this->setPriority($priority);

        if ($this->count() === 0) {
            throw new \LogicException('A constraints works on at least one field');
        }
    }

    /**
     * Define a new constraint.
     *
     * @param ConstraintedField|array<ConstraintedField> $attributes
     * @param string                                     $name
     * @param integer                                    $priority
     * @return self|null
     */
    public static function constraint($attributes, string $name=null,
                                      int $priority=self::MEDIUM_PRIORITY)
    {
        $creating = Event::until('constraints.creating', static::class, \func_get_args());

        if ($creating === false) {
            return null;
        }

        $constraint = $creating ?: new static($attributes, $name, $priority);

        Event::dispatch('constraints.created', $constraint);

        return $constraint;
    }

    /**
     * Return the configuration path for this field.
     *
     * @param string $path
     * @return mixed
     */
    public function getConfigPath(string $path=null)
    {
        $name = Str::snake((new \ReflectionClass($this))->getShortName());

        return 'field.constraint.configurations.'.$name.(\is_null($path) ? '' : '.'.$path);
    }

    /**
     * Return the configuration for this field.
     *
     * @param string $path
     * @param mixed  $default
     * @return mixed
     */
    public function getConfig(string $path=null, $default=null)
    {
        return Container::getInstance()->config->get($this->getConfigPath($path), $default);
    }

    /**
     * Return the constraint name.
     *
     * @return string
     */
    public function getConstraintType(): string
    {
        return $this->getConfig('type');
    }

    /**
     * Return the default name for this constraint.
     *
     * @return string
     */
    public function getDefaultName(): string
    {
        $tableName = $this->getMainTableName();

        return $tableName.'_'.implode('_', \array_map(function (AttributeField $field) {
            return $field->getAttname();
        }, $this->getAttributes())).'_'.$this->constraintType;
    }

    /**
     * Indicate if it has a name.
     *
     * @return boolean
     */
    public function hasName(): bool
    {
        return !\is_null($this->name);
    }

    /**
     * Return the name of this constraint.
     *
     * @return string
     */
    public function getName(): string
    {
        if (!$this->hasName()) {
            return $this->getDefaultName();
        }

        return $this->name;
    }

    /**
     * Return all table names related to this constraint.
     *
     * @return array<string>
     */
    public function getTableNames(): array
    {
        return \array_unique(\array_map(function (AttributeField $field) {
            return $field->getMeta()->getTableName();
        }, $this->getAttributes()));
    }

    /**
     * Return the main table.
     *
     * @return string
     */
    public function getMainTableName(): string
    {
        return $this->getMainAttribute()->getMeta()->getTableName();
    }

    /**
     * Return the main attribute
     *
     * @return AttributeField
     */
    public function getMainAttribute(): AttributeField
    {
        return $this->getAttributes()[0];
    }

    /**
     * Return all concerned attribute fields.
     *
     * @return array
     */
    public function getAttributes(): array
    {
        return $this->all();
    }

    /**
     * Return the main attribute names.
     *
     * @return string
     */
    public function getMainNative(): string
    {
        return $this->getAttributes()[0]->getNative();
    }

    /**
     * Return all concerned attribute names.
     *
     * @return array<string>
     */
    public function getNatives(): array
    {
        return \array_map(function ($attribute) {
            return $attribute->getNative();
        }, $this->all());
    }

    /**
     * Indicate if this constraint is composed of multiple fields.
     *
     * @return boolean
     */
    public function isComposed(): bool
    {
        return $this->count() > 1;
    }

    /**
     * Disallow any modifications after locking the instance.
     *
     * @return self
     */
    public function lock()
    {
        $locking = Event::until('constraints.locking', $this);

        if ($locking === false) {
            return $this;
        }

        parent::lock();

        Event::dispatch('constraints.locked', $this);

        return $this;
    }

    /**
     * Actions during locking.
     *
     * @return void
     */
    protected function locking()
    {
        if ($this->getTableNames() !== [$this->getMainTableName()]) {
            throw new LockException(
                "A `$this->constraintType` constraint cannot have fields from different tables.",
                'observed'
            );
        }
    }

    /**
     * Call the observer.
     *
     * @param  mixed ...$args
     * @return mixed
     */
    public function __invoke(...$args)
    {
    }
}
