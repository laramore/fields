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

use Laramore\Exceptions\LockException;
use Laramore\Fields\Field;
use Laramore\Observers\BaseObserver;
use Event;

abstract class Constraint extends BaseObserver
{
    /**
     * Define the name of the constraint.
     *
     * @var string
     */
    protected $constraintName;

    /**
     * An observer needs at least a name and a Closure.
     *
     * @param array   $fields
     * @param string  $name
     * @param integer $priority
     * @param Closure $callback
     */
    protected function __construct(array $fields, string $name=null, int $priority=self::MEDIUM_PRIORITY)
    {
        if (!\is_null($name)) {
            $this->setName($name);
        }

        if (\count($fields) === 0) {
            throw new \LogicException('A constraints works on at least one field');
        }

        $this->on($fields);
        $this->setPriority($priority);
    }

    public static function constraint(array $fields, string $name=null, int $priority=self::MEDIUM_PRIORITY)
    {
        $creating = Event::until('constraints.creating', static::class, \func_get_args());

        if ($creating === false) {
            return null;
        }

        $field = $creating ?: new static($fields, $name, $priority);

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

        return $tableName.'_'.implode('_', \array_map(function (Field $field) {
            return $field->getAttname();
        }, $this->getFields())).'_'.$this->constraintName;
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
        return \array_unique(\array_map(function (Field $field) {
            return $field->getMeta()->getTableName();
        }, $this->all()));
    }

    public function getMainTableName()
    {
        return $this->all()[0]->getMeta()->getTableName();
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