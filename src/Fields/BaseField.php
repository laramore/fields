<?php
/**
 * Define all basic field methods.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2019
 * @license MIT
 */

namespace Laramore\Fields;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Event;
use Laramore\Elements\TypeElement;
use Laramore\Facades\{
    Rule, Type
};
use Laramore\Interfaces\{
    IsAField, IsConfigurable, IsAnOwner
};
use Laramore\Traits\{
    IsOwned, IsLocked, HasProperties
};
use Laramore\Traits\{
    HasRules, HasLockedMacros
};
use Laramore\Meta;
use Laramore\Exceptions\ConfigException;

abstract class BaseField implements IsAField, IsConfigurable
{
    use IsOwned, IsLocked, HasLockedMacros, HasProperties, HasRules {
        own as protected ownFromTrait;
        setOwner as protected setOwnerFromTrait;
        lock as protected lockFromTrait;
        setProperty as protected forceProperty;
        HasLockedMacros::__call as protected callMacro;
        HasProperties::__call as protected callProperty;
    }

    /**
     * Meta that owns this field.
     *
     * @var \Laramore\Meta
     */
    protected $meta;

    /**
     * Default value of this field.
     *
     * @var mixed
     */
    protected $default;

    /**
     * Create a new field with basic rules.
     * The constructor is protected so the field is created writing left to right.
     * ex: Text::field()->maxLength(255) insteadof (new Text)->maxLength(255).
     *
     * @param array|null $rules
     */
    protected function __construct(array $rules=null)
    {
        $this->addRules($rules ?: $this->getType()->getDefaultRules());
    }

    /**
     * Call the constructor and generate the field.
     *
     * @param  array|null $rules
     * @return self
     */
    public static function field(array $rules=null)
    {
        $creating = Event::until('fields.creating', static::class, \func_get_args());

        if ($creating === false) {
            return null;
        }

        $field = $creating ?: new static($rules);

        Event::dispatch('fields.created', $field);

        return $field;
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

        return 'field.configurations.'.$name.(\is_null($path) ? '' : '.'.$path);
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
     * Return the type derived of this field.
     *
     * @return TypeElement
     */
    protected function resolveType(): TypeElement
    {
        $type = $this->getConfig('type');

        if (\is_null($type)) {
            throw new ConfigException($this->getConfigPath('type'), \array_keys(Type::all()), null);
        }

        return Type::get($type);
    }

    /**
     * Return the type object of the field.
     *
     * @return TypeElement
     */
    public function getType(): TypeElement
    {
        return $this->resolveType();
    }

    /**
     * Return a property by its name.
     *
     * @param  string $key
     * @return mixed
     * @throws \ErrorException If no property exists with this name.
     */
    public function getProperty(string $key)
    {
        if ($key === 'type') {
            return $this->getType();
        } else if ($key === 'native') {
            return $this->getNative();
        }

        if ($this->hasProperty($key)) {
            if (\method_exists($this, $method = 'get'.\ucfirst($key))) {
                return \call_user_func([$this, $method]);
            }

            return $this->$key;
        } else if (Rule::has($snakeKey = Str::snake($key))) {
            return $this->hasRule($snakeKey);
        }
    }

    /**
     * Manage the definition of a property.
     *
     * @param string $key
     * @param mixed  $value
     * @return self
     * @throws \ErrorException If no property exists with this name.
     */
    public function setProperty(string $key, $value)
    {
        $this->needsToBeUnlocked();

        if (Rule::has($snakeKey = Str::snake($key))) {
            if ($value === false) {
                return $this->removeRule($snakeKey);
            }

            return $this->addRule($snakeKey);
        }

        return $this->forceProperty($key, $value);
    }

    /**
     * Parse the name value.
     *
     * @param  string $name
     * @return string
     */
    public static function parseName(string $name): string
    {
        return static::replaceInTemplate(config('field.name_template'), compact('name'));
    }

    /**
     * Define the name of the field.
     *
     * @param  string $name
     * @return self
     */
    protected function setName(string $name)
    {
        $this->needsToBeUnlocked();

        if (!is_null($this->name)) {
            throw new \LogicException('The field name cannot be defined multiple times');
        }

        $this->name = $name;

        return $this;
    }

    /**
     * Return the native value of this field.
     * Commonly, its name.
     *
     * @return string
     */
    public function getNative(): string
    {
        return $this->name;
    }

    /**
     * Return the fully qualified name.
     *
     * @return string
     */
    public function getFullName(): string
    {
        $this->needsToBeOwned();

        return $this->getMeta()->getTableName().'.'.$this->getNative();
    }

    /**
     * Define the field as not visible.
     *
     * @param  boolean $hidden
     * @return self
     */
    public function hidden(bool $hidden=true)
    {
        return $this->visible(!$hidden);
    }

    /**
     * Define a default value for this field.
     *
     * @param  mixed $value
     * @return self
     */
    public function default($value=null)
    {
        $this->needsToBeUnlocked();

        $this->removeRule(Rule::required());

        if (\is_null($value)) {
            $this->nullable();
        }

        $this->defineProperty('default', $value);

        return $this;
    }

    /**
     * Set the owner.
     *
     * @param IsAnOwner $owner
     * @return void
     */
    protected function setOwner(IsAnOwner $owner)
    {
        $this->setOwnerFromTrait($owner);

        if (!$this->hasProperty('meta')) {
            while (!($owner instanceof Meta)) {
                $owner = $owner->getOwner();
            }

            $this->setMeta($owner);
        }
    }

    /**
     * Assign a unique owner to this instance.
     *
     * @param  IsAnOwner $owner
     * @param  string    $name
     * @return self
     */
    public function own(IsAnOwner $owner, string $name)
    {
        $this->needsToBeUnlocked();

        $name = static::parseName($name);

        $owning = Event::until('fields.owning', $this, $owner, $name);

        if ($owning === false) {
            return $this;
        }

        $this->ownFromTrait(($owning[0] ?? $owner), ($owning[1] ?? $name));

        Event::dispatch('fields.owned', $this);

        return $this;
    }

    /**
     * Callaback when the instance is owned.
     *
     * @return void
     */
    protected function owned()
    {
        $owner = $this->getOwner();

        if (!($owner instanceof Meta) && !($owner instanceof CompositeField)) {
            throw new \LogicException('A field should be owned by a Meta or a CompositeField');
        }
    }

    /**
     * Disallow any modifications after locking the instance.
     *
     * @return self
     */
    public function lock()
    {
        $this->needsToBeOwned();

        $locking = Event::until('fields.locking', $this);

        if ($locking === false) {
            return $this;
        }

        $this->lockFromTrait();

        Event::dispatch('fields.locked', $this);

        return $this;
    }

    /**
     * Each class locks in a specific way.
     *
     * @return void
     */
    protected function locking()
    {
        $this->checkRules();
        $this->setProxies();
    }

    /**
     * Check all properties and rules before locking the field.
     *
     * @return void
     */
    protected function checkRules()
    {
        $name = $this->getFullName();

        if ($this->hasProperty('default')) {
            if (\is_null($this->getProperty('default'))) {
                if ($this->hasRule(Rule::notNullable())) {
                    throw new \LogicException("The field `$name` cannot be null and defined as null by default");
                } else if (!$this->hasRule(Rule::nullable()) && !$this->hasRule(Rule::required())) {
                    throw new \LogicException("The field `$name` cannot be null, defined as null by default and not required");
                }
            } else if ($this->hasRule(Rule::required())) {
                throw new \LogicException("The field `$name` cannot have a default value and be required");
            }
        }

        if (!$this->hasRule(Rule::fillable()) && $this->hasRule(Rule::required())) {
            throw new \LogicException("The field `$name` must be fillable if it is required");
        }

        if ($this->hasRule(Rule::notNullable()) && $this->hasRule(Rule::nullable())) {
            throw new \LogicException("The field `$name` cannot be nullable and not nullable on the same time");
        }
    }

    /**
     * Define all proxies for this field.
     *
     * @return void
     */
    protected function setProxies()
    {
        $class = config('field.proxies.class');
        $proxies = \array_merge(config('field.proxies.common'), $this->getConfig('proxies'));

        if (!config('field.proxies.enabled') || \is_null($class) || \is_null($proxies)) {
            return;
        }

        $proxyHandler = $this->getMeta()->getProxyHandler();
        $default = config('field.proxies.configurations');

        foreach ($proxies as $methodName => $data) {
            if (\is_null($data)) {
                continue;
            }

            $data = \array_merge($default, $data);
            $name = static::replaceInTemplate($data['name_template'], [
                'methodname' => $methodName,
                'fieldname' => Str::camel($this->name),
            ]);

            $proxyHandler->add(new $class($name, $this, $methodName, $data['requirements'], $data['targets']));
        }
    }

    /**
     * Remplace in template with values.
     *
     * @param  string $template
     * @param  array  $keyValues
     * @return string
     */
    public static function replaceInTemplate(string $template, array $keyValues): string
    {
        foreach ($keyValues as $key => $value) {
            $template = \str_replace([
                '${'.$key.'}', '#{'.$key.'}', '+{'.$key.'}', '^{'.$key.'}', '*{'.$key.'}', '_{'.$key.'}', '-{'.$key.'}',
            ], [
                $value, Str::singular($value), Str::plural($value), \ucwords($value), \ucwords(Str::plural($value)),
                Str::snake($value), Str::camel($value)
            ], $template);
        }

        return $template;
    }

    /**
     * Define the meta of this field.
     *
     * @param  Meta $meta
     * @return self
     */
    public function setMeta(Meta $meta)
    {
        $this->needsToBeUnlocked();

        if ($this->hasProperty('meta')) {
            throw new \LogicException('The meta cannot be defined multiple times');
        }

        $this->defineProperty('meta', $meta);

        return $this;
    }

    /**
     * Return the meta of this field.
     * The owner could be a composite field and so on but not the coresponded meta.
     *
     * @return Meta
     */
    public function getMeta(): Meta
    {
        return $this->meta;
    }

    /**
     * Return a property, or set one.
     *
     * @param  string $method
     * @param  array  $args
     * @return mixed
     */
    public function __call(string $method, array $args)
    {
        if (static::hasMacro($method)) {
            return $this->callMacro($method, $args);
        }

        return $this->callProperty($method, $args);
    }

    /**
     * Return the native value of this field.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getNative();
    }
}
