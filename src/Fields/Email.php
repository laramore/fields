<?php
/**
 * Define a email field.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2019
 * @license MIT
 */

namespace Laramore\Fields;

use Illuminate\Support\Str;
use Laramore\Facades\Rules;

class Email extends Pattern
{
    protected $domains;

    public function domains($domains)
    {
        $this->needsToBeUnlocked();

        foreach ((array) $domains as $domain) {
            if (!preg_match($this->getConfig('patterns.domain'), $domain)) {
                throw new \Exception("$domain is not a right domain");
            }
        }

        $this->defineProperty('domains', $domains);

        return $this;
    }

    public function getMainDomain(): string
    {
        return \reset($this->getDomains());
    }

    public function getUsernamePattern(): string
    {
        return $this->getConfig('patterns.username');
    }

    public function getDomainPattern(): string
    {
        return $this->getConfig('patterns.domain');
    }

    public function getPattern(): string
    {
        return $this->getConfig('patterns.email');
    }

    public function transform($value)
    {
        $value = parent::transform($value);

        if (\is_null($value)) {
            return $value;
        }

        return $this->fix($value);
    }

    public function serialize($value)
    {
        return $value;
    }

    public function fix(string $value)
    {
        if ($this->hasRule(Rules::acceptUsername())) {
            return $value.'@'.$this->getMainDomain();
        }

        return $value;
    }
}
