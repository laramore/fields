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

use Laramore\Facades\Rule;

class Email extends Pattern
{
    /**
     * All defined allowed domains.
     *
     * @var array
     */
    protected $allowedDomains;

    /**
     * Define the allowed domains.
     *
     * @param array $allowedDomains
     * @return self
     */
    public function allowedDomains(array $allowedDomains)
    {
        $this->needsToBeUnlocked();

        foreach ($allowedDomains as $allowedDomain) {
            if (!\preg_match($this->getConfig('patterns.domain'), $allowedDomain)) {
                throw new \Exception("`$allowedDomain` is not a right domain");
            }
        }

        $this->defineProperty('allowedDomains', $allowedDomains);

        return $this;
    }

    /**
     * Define the allowed domain.
     *
     * @param string $allowedDomain
     * @return self
     */
    public function allowedDomain(string $allowedDomain)
    {
        return $this->allowedDomains([$allowedDomain]);
    }

    /**
     * Return the main domain.
     *
     * @return string
     */
    public function getMainDomain(): string
    {
        return \reset($this->getDomains());
    }

    /**
     * Return the username pattern.
     *
     * @return string
     */
    public function getUsernamePattern(): string
    {
        return $this->getConfig('patterns.username');
    }

    /**
     * Return the domain pattern.
     *
     * @return string
     */
    public function getDomainPattern(): string
    {
        return $this->getConfig('patterns.domain');
    }

    /**
     * Return the pattern to match.
     *
     * @return string
     */
    public function getPattern(): string
    {
        return $this->getConfig('patterns.email');
    }

    /**
     * Transform the value to be used as a correct format.
     *
     * @param  mixed $value
     * @return mixed
     */
    public function transform($value)
    {
        $value = parent::transform($value);

        if (\is_null($value)) {
            return $value;
        }

        return $this->fix($value);
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
     * Fix the wrong value.
     *
     * @param string $value
     * @return mixed
     */
    public function fix(string $value)
    {
        if ($this->hasRule(Rule::acceptUsername())) {
            return $value.'@'.$this->getMainDomain();
        }

        return $value;
    }
}
