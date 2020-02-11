<?php
/**
 * Define a may to many field.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2019
 * @license MIT
 */

namespace Laramore\Fields;

use Illuminate\Support\Str;
use Laramore\Exceptions\ConfigException;
use Laramore\Traits\Field\ManyToManyRelation;
use Laramore\Fields\LinkField;
use Laramore\Fields\Constraint\Unique;

class ManyToMany extends CompositeField
{
    use ManyToManyRelation;

    /**
     * Defined reversed name.
     *
     * @var string
     */
    protected $reversedName;

    /**
     * Indicate if this use a specific pivot.
     *
     * @var boolean
     */
    protected $usePivot;

    /**
     * Pivot class name.
     *
     * @var string
     */
    protected $pivotClass;

    /**
     * Defined reversed pivot name.
     *
     * @var string
     */
    protected $reversedPivotName;

    /**
     * Define the pivot and reversed pivot names.
     *
     * @param string $pivotName
     * @param string $reversedPivotName
     * @return self
     */
    public function pivotName(string $pivotName, string $reversedPivotName=null)
    {
        $this->needsToBeUnlocked();

        $this->defineProperty('pivotName', $pivotName);

        if (!\is_null($reversedPivotName)) {
            $this->setProperty('reversedPivotName', $reversedPivotName);
        }

        return $this;
    }

    /**
     * Create a new field with basic options.
     * The constructor is protected so the field is created writing left to right.
     * ex: Text::field()->maxLength(255) insteadof (new Text)->maxLength(255).
     *
     * Define by default pivot and reversed pivot names.
     *
     * @param array|null $options
     */
    protected function __construct(array $options=null)
    {
        parent::__construct($options);

        $this->pivotName($this->getConfig('pivot_name_template'), $this->getConfig('reversed_pivot_name_template'));

        if (\is_null($this->pivotName)) {
            throw new ConfigException($this->getConfigPath('pivot_name_template'), ['any string name'], null);
        }

        if (\is_null($this->reversedPivotName)) {
            throw new ConfigException($this->getConfigPath('reversed_pivot_name_template'), ['any string name'], null);
        }
    }

    /**
     * Return the reversed field.
     *
     * @return LinkField
     */
    public function getReversed(): LinkField
    {
        return $this->getLink('reversed');
    }

    /**
     * Define the model on which to point.
     *
     * @param string $model
     * @param string $reversedName
     * @return self
     */
    public function on(string $model, string $reversedName=null)
    {
        $this->needsToBeUnlocked();

        if ($model === 'self') {
            $this->defineProperty('on', $model);
        } else {
            $this->defineProperty('on', $this->getLink('reversed')->off = $model);
            $this->to($model::getMeta()->getPrimary()->all()[0]->attname);
        }

        if ($reversedName) {
            $this->reversedName($reversedName);
        } else if ($model === 'self') {
            $this->reversedName($this->getConfig('self_reversed_name_template'));
        }

        return $this;
    }

    /**
     * Define on self.
     *
     * @return self
     */
    public function onSelf()
    {
        return $this->on('self');
    }

    /**
     * Define the attribute name.
     *
     * @param string $name
     * @return self
     */
    public function to(string $name)
    {
        $this->needsToBeUnlocked();

        $this->defineProperty('to', $this->getLink('reversed')->from = $name);

        return $this;
    }

    /**
     * Define the reversed name of the relation.
     *
     * @param string $reversedName
     * @return self
     */
    public function reversedName(string $reversedName)
    {
        $this->needsToBeUnlocked();
        $this->needsToBeUnowned();

        $this->linksName['reversed'] = $reversedName;

        return $this;
    }

    /**
     * Indicate which pivot to use.
     *
     * @param string $pivotClass
     * @return self
     */
    public function usePivot(string $pivotClass=null)
    {
        $this->needsToBeUnlocked();

        $this->defineProperty('usePivot', true);
        $this->defineProperty('pivotClass', $pivotClass);

        return $this;
    }

    /**
     * Load the pivot meta.
     *
     * @return void
     */
    protected function loadPivotMeta()
    {
        $offMeta = $this->getMeta();
        $onMeta = $this->on::getMeta();
        $offName = Str::snake($offMeta->getModelClassName());
        $onName = Str::snake(Str::singular($this->name));
        $namespaceName = 'App\\Pivots';
        $pivotClassName = ucfirst($offName).ucfirst($onName);
        $pivotClass = "$namespaceName\\$pivotClassName";

        $this->pivotName = $this->replaceInFieldTemplate($this->pivotName, $offName);
        $this->reversedPivotName = $this->replaceInFieldTemplate($this->reversedPivotName, $onName);

        if ($this->usePivot) {
            if ($this->pivotClass) {
                $pivotClass = $this->pivotClass;
            }

            $this->setProperty('pivotMeta', $pivotClass::getMeta());
        } else {
            // Create dynamically the pivot class (only and first time I use eval, really).
            if (!\class_exists($pivotClass)) {
                eval("namespace $namespaceName; class $pivotClassName extends \Laramore\Eloquent\FakePivot {}");
            }

            $this->setProperty('pivotMeta', $pivotClass::getMeta());

            $this->pivotMeta->setComposite(
                $offName,
                Foreign::field()->on($this->getMeta()->getModelClass())
            );

            $offField = Foreign::field()->on($this->on);

            if ($this->isOnSelf()) {
                $offField->reversedName($this->getConfig('self_pivot_reversed_name_template'));
            }

            $this->pivotMeta->setComposite(
                $onName,
                $offField
            );
        }

        [$to, $from] = $this->pivotMeta->getPivots();

        $this->setProperty('pivotTo', $to);
        $this->setProperty('pivotFrom', $from);

        if (isset($this->constraints['unique'])) {
            $this->pivotMeta->unique([$this->pivotTo, $this->pivotFrom], ...$this->constraints['unique']);

            unset($this->constraints['unique']);
        }
    }

    /**
     * Define on and off variables after being owned.
     *
     * @return void
     */
    public function owned()
    {
        if ($this->on === 'self') {
            $this->on($this->getMeta()->getModelClass());
        }

        $this->loadPivotMeta();

        $this->defineProperty('off', $this->getLink('reversed')->on = $this->getMeta()->getModelClass());

        parent::owned();
    }

    /**
     * Check and set variables during locking.
     *
     * @return void
     */
    protected function locking()
    {
        if (!$this->on) {
            throw new \Exception('Related model settings needed. Set it by calling `on` method');
        }

        $this->defineProperty('reversedName', $this->getLink('reversed')->name);
        $this->defineProperty('from', $this->getLink('reversed')->to = $this->getMeta()->getPrimary()->all()[0]->attname);

        parent::locking();
    }

    /**
     * Indicate if it is a relation on itself.
     *
     * @return boolean
     */
    public function isOnSelf()
    {
        return \in_array($this->on, [$this->getMeta()->getModelClass(), 'self']);
    }

    /**
     * Define a unique constraint.
     *
     * @param  string  $name
     * @param  string  $class
     * @param  integer $priority
     * @return self
     */
    public function unique(string $name=null, string $class=null, int $priority=Unique::MEDIUM_PRIORITY)
    {
        $this->needsToBeUnlocked();

        if (isset($this->constraints['unique'])) {
            throw new \LogicException("This field cannot have multiple unique constraints");
        }

        if (\is_null($class)) {
            $class = config('field.constraints.configurations.unique.class');
        }

        if (\is_null($this->pivotMeta)) {
            $this->constraints['unique'] = \func_get_args();
        } else {
            $this->pivotMeta->unique([$this->pivotTo, $this->pivotFrom], ...\func_get_args());
        }

        return $this;
    }
}
