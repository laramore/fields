<?php
/**
 * Handle all observers for a specific class.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2019
 * @license MIT
 */

namespace Laramore\Fields\Constraint;

use Laramore\Observers\{
    BaseObserver, BaseHandler
};
use Laramore\Fields\Constraint\{
    Primary, BaseConstraint
};
use Laramore\Contracts\Configured;
use Laramore\Exceptions\LockException;

abstract class BaseConstraintHandler extends BaseHandler implements Configured
{
    /**
     * The observer class to use to generate.
     *
     * @var string
     */
    protected $observerClass = BaseConstraint::class;

    /**
     * Return the configuration path for this field.
     *
     * @param string $path
     * @return mixed
     */
    public function getConfigPath(string $path=null)
    {
        return 'field.constraint'.(\is_null($path) ? '' : '.'.$path);
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
        return config($this->getConfigPath($path), $default);
    }

    /**
     * Return the list of constraints.
     *
     * @param  string $type
     * @return array<string,array<BaseObserver>>|array<BaseObserver>
     */
    public function all(string $type=null): array
    {
        if (\is_null($type)) {
            return $this->observers;
        } else {
            return ($this->observers[$type] ?? []);
        }
    }

    /**
     * Return the list of all constraints.
     *
     * @return array<BaseObserver>
     */
    public function getConstraints(): array
    {
        return \array_merge(...\array_values($this->all()));
    }

    /**
     * Push a constraint to a list of constraints.
     *
     * @param BaseObserver        $constraint
     * @param array<BaseObserver> $constraints
     * @return self
     */
    protected function push(BaseObserver $constraint, array &$constraints)
    {
        // @var BaseConstraint $constraint
        $type = $constraint->getConstraintType();

        if (!isset($constraints[$type])) {
            $constraints[$type] = [];
        }

        if (!\in_array($constraint, $constraints[$type])) {
            \array_push($constraints[$type], $constraint);
        }

        return $this;
    }

    /**
     * Actions during locking.
     *
     * @return void
     */
    protected function locking()
    {
        parent::locking();

        if ($this->count(BaseConstraint::PRIMARY) > 1) {
            throw new LockException('A field cannot have multiple primary constraints', 'primary');
        }
    }

    /**
     * Return the primary constraint.
     *
     * @return Primary|null
     */
    public function getPrimary()
    {
        $primaries = $this->all(BaseConstraint::PRIMARY);

        if (\count($primaries)) {
            return $primaries[0];
        }

        return null;
    }

    /**
     * Return all indexes.
     *
     * @return array<BaseConstraint>
     */
    public function getIndexes(): array
    {
        return $this->all(BaseConstraint::INDEX);
    }

    /**
     * Return all unique constraints.
     *
     * @return array<BaseConstraint>
     */
    public function getUniques(): array
    {
        return $this->all(BaseConstraint::UNIQUE);
    }

    /**
     * Return all foreign constraints.
     *
     * @return array<BaseConstraint>
     */
    public function getForeigns(): array
    {
        return $this->all(BaseConstraint::FOREIGN);
    }

    /**
     * Return the sourced constraint.
     *
     * @param array $attributes
     * @return void
     */
    abstract public function getSource(array $attributes);

    /**
     * Return the targeted constraint.
     *
     * @param array $attributes
     * @return void
     */
    abstract public function getTarget(array $attributes);
}
