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

use Illuminate\Container\Container;
use Laramore\Observers\{
    BaseObserver, BaseHandler
};
use Laramore\Fields\Constraint\{
    Primary, BaseConstraint
};
use Laramore\Contracts\Configured;
use Laramore\Contracts\Field\Constraint\{
    SourceConstraint, TargetConstraint
};

abstract class BaseConstraintHandler extends BaseHandler implements Configured
{
    /**
     * List of all observers to apply on the observable class.
     *
     * @var array
     */
    protected $observers = [
        BaseConstraint::PRIMARY => [],
        BaseConstraint::INDEX => [],
        BaseConstraint::UNIQUE => [],
        BaseConstraint::FOREIGN => [],
    ];

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
        return 'field.constraints'.(\is_null($path) ? '' : '.'.$path);
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
     * Return the list of constraints.
     *
     * @param  string $type
     * @return array<BaseObserver>
     */
    public function all(string $type=null): array
    {
        if (\is_null($type)) {
            return $this->observers;
        } else {
            return $this->observers[$type];
        }
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
        $type = $constraint->getConstraintType();

        if (!isset($constraints[$type])) {
            throw new \Exception("The constraint type `$type` does not exist");
        }

        \array_push($constraints[$type], $constraint);

        return $this;
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
     * Return all source constraints.
     *
     * @return array<SourceConstraint>
     */
    public function getSources(): array
    {
        $sources = [];

        foreach ($this->all() as $constraints) {
            foreach ($constraints as $constraint) {
                if ($constraint instanceof SourceConstraint) {
                    $sources[] = $constraint;
                }
            }
        }

        return $sources;
    }

    /**
     * Return all target constraints.
     *
     * @return array<TargetConstraint>
     */
    public function getTargets(): array
    {
        $targets = [];

        foreach ($this->all() as $constraints) {
            foreach ($constraints as $constraint) {
                if ($constraint instanceof TargetConstraint) {
                    $targets[] = $constraint;
                }
            }
        }

        return $targets;
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
