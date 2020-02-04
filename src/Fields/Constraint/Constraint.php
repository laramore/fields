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

use Illuminate\Support\Facades\Event;
use Laramore\Exceptions\LockException;
use Laramore\Fields\AttributeField;
use Laramore\Observers\BaseObserver;

abstract class Constraint extends BaseObserver
{
    /**
     * Define the name of the constraint.
     *
     * @var string
     */
    protected $constraintName = 'constraint';

    /**
     * An observer needs at least a name.
     *
     * @param array<AttributeField> $attributes
     * @param string                $name
     * @param integer               $priority
     */
    protected function __construct(array $attributes, string $name=null, int $priority=self::MEDIUM_PRIORITY)
    {
        if (!\is_null($name)) {
            $this->setName($name);
        }

        if (\count($attributes) === 0) {
            throw new \LogicException('A constraints works on at least one field');
        }

        $this->on($attributes);
        $this->setPriority($priority);
    }

    public static function constraint(array $attributes, string $name=null, int $priority=self::MEDIUM_PRIORITY)
    {
        $creating = Event::until('constraints.creating', static::class, \func_get_args());

        if ($creating === false) {
            return null;
        }

        $field = $creating ?: new static($attributes, $name, $priority);

        Event::dispatch('constraints.created', $field);

        return $field;
    }

    public function getConstraintName(): string
    {
        return $this->constraintName;
    }

    public function getDefaultName(): string
    {
        $tableName = $this->getMainTableName();

        return $tableName.'_'.implode('_', \array_map(function (AttributeField $field) {
            return $field->getAttname();
        }, $this->getAttributes())).'_'.$this->constraintName;
    }

    public function hasName(): bool
    {
        return !\is_null($this->name);
    }

    public function getName(): string
    {
        if (!$this->hasName()) {
            return $this->getDefaultName();
        }

        return $this->name;
    }

    public function getTableNames()
    {
        return \array_unique(\array_map(function (AttributeField $field) {
            return $field->getMeta()->getTableName();
        }, $this->getAttributes()));
    }

    public function getMainTableName()
    {
        return $this->getAttributes()[0]->getMeta()->getTableName();
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
            throw new LockException("A `$this->constraintName` constraint cannot have fields from different tables.", 'observed');
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
