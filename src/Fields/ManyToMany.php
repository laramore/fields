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

use Illuminate\Container\Container;
use Illuminate\Support\Str;
use Laramore\Exceptions\ConfigException;
use Laramore\Traits\Field\ManyToManyRelation;
use Laramore\Fields\BaseLink;
use Laramore\Fields\Constraint\Unique;

class ManyToMany extends BaseComposed
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
     * Unique relation.
     *
     * @var bool
     */
    protected $uniqueRelation = false;

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

        $this->pivotName($this->getConfig('templates.pivot'), $this->getConfig('templates.reversed_pivot'));

        if (\is_null($this->pivotName)) {
            throw new ConfigException($this->getConfigPath('templates.pivot'), ['any string name'], null);
        }

        if (\is_null($this->reversedPivotName)) {
            throw new ConfigException($this->getConfigPath('templates.reversed_pivot'), ['any string name'], null);
        }
    }

    /**
     * Return the reversed field.
     *
     * @return BaseLink
     */
    public function getReversed(): BaseLink
    {
        return $this->getField('reversed');
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
            $this->defineProperty('on', $this->getField('reversed')->off = $model);
            $this->to($model::getMeta()->getPrimary()->all()[0]->attname);
        }

        if ($reversedName) {
            $this->reversedName($reversedName);
        } else if ($model === 'self') {
            $this->reversedName($this->getConfig('templates.self_reversed'));
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

        $this->defineProperty('to', $this->getField('reversed')->from = $name);

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

        $this->fieldsName['reversed'] = $reversedName;

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

            $this->pivotMeta->setField(
                $offName,
                OneToMany::field()->on($this->getMeta()->getModelClass())
            );

            $offField = OneToMany::field()->on($this->on);

            if ($this->isOnSelf()) {
                $offField->reversedName($this->getConfig('templates.self_pivot_reversed'));
            }

            $this->pivotMeta->setField(
                $onName,
                $offField
            );
        }

        [$to, $from] = $this->pivotMeta->getPivots();

        $this->setProperty('pivotTo', $to);
        $this->setProperty('pivotFrom', $from);

        if ($this->uniqueRelation) {
            $this->unique($this->uniqueRelation === true ? null : $this->uniqueRelation);
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

        if (\is_null($this->pivotMeta)) {
            $this->loadPivotMeta();
        }

        $this->defineProperty('off', $this->getField('reversed')->on = $this->getMeta()->getModelClass());

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

        $this->defineProperty('reversedName', $this->getField('reversed')->name);
        $this->defineProperty('from', $this->getField('reversed')->to = $this->getMeta()->getPrimary()->all()[0]->attname);

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
     * @param  string $name
     * @return self
     */
    public function unique(string $name=null)
    {
        $this->needsToBeUnlocked();

        if (\is_null($this->pivotMeta)) {
            $this->uniqueRelation = $name ?: true;
        } else {
            $this->uniqueRelation = true;
            $this->pivotMeta->unique([$this->pivotTo, $this->pivotFrom], $name);
        }

        return $this;
    }
}
