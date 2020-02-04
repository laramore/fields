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

use Laramore\Observers\BaseHandler;
use Laramore\Exceptions\LockException;
use Laramore\Fields\Constraint\{
    Primary, Index, Unique, Foreign
};

class ConstraintHandler extends BaseHandler
{
    /**
     * The observer class to use to generate.
     *
     * @var string
     */
    protected $observerClass = Constraint::class;

    /**
     * Return the primary constraint.
     *
     * @return Primary|null
     */
    public function getPrimary(): ?Primary
    {
        $primaries = $this->allFromClass(Primary::class);

        if (\count($primaries)) {
            return $primaries[0];
        }

        return null;
    }

    /**
     * Return all indexes.
     *
     * @return array<Index>
     */
    public function getIndexes(): array
    {
        return $this->allFromClass(Index::class);
    }

    /**
     * Return all unique constraints.
     *
     * @return array<Unique>
     */
    public function getUniques(): array
    {
        return $this->allFromClass(Unique::class);
    }

    /**
     * Return all foreign constraints.
     *
     * @return array<Foreign>
     */
    public function getForeigns(): array
    {
        return $this->allFromClass(Foreign::class);
    }

    /**
     * Actions during locking.
     *
     * @return void
     */
    public function locking()
    {
        parent::locking();

        if (\count($this->allFromClass(Primary::class)) > 1) {
            throw new LockException('A table cannot have multiple primary constraints', 'primary');
        }
    }
}
