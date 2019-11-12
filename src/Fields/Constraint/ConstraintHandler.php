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

    public function getPrimary(): ?Primary
    {
        $primaries = $this->allFromClass(Primary::class);

        if (\count($primaries)) {
            return $primaries[0];
        }

        return null;
    }

    public function getIndexes(): array
    {
        return $this->allFromClass(Index::class);
    }

    public function getUniques(): array
    {
        return $this->allFromClass(Unique::class);
    }

    public function getForeigns(): array
    {
        return $this->allFromClass(Foreign::class);
    }

    public function locking()
    {
        parent::locking();

        if (\count($this->allFromClass(Primary::class)) > 1) {
            throw new LockException('A table cannot have multiple primary constraints', 'primary');
        }
    }
}
