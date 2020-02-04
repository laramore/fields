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

    protected $reversedName;
    protected $usePivot;
    protected $pivotClass;
    protected $reversedPivotName;

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
     * Create a new field with basic rules.
     * The constructor is protected so the field is created writing left to right.
     * ex: Text::field()->maxLength(255) insteadof (new Text)->maxLength(255).
     *
     * @param array|null $rules
     */
    protected function __construct(array $rules=null)
    {
        parent::__construct($rules);

        $this->pivotName = $this->getConfig('pivot_name_template');

        if (\is_null($this->pivotName)) {
            throw new ConfigException($this->getConfigPath('pivot_name_template'), ['any string name'], null);
        }

        $this->reversedPivotName = $this->getConfig('reversed_pivot_name_template');

        if (\is_null($this->reversedPivotName)) {
            throw new ConfigException($this->getConfigPath('reversed_pivot_name_template'), ['any string name'], null);
        }
    }

    public function getReversed(): LinkField
    {
        return $this->getLink('reversed');
    }

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

    public function onSelf()
    {
        return $this->on('self');
    }

    public function to(string $name)
    {
        $this->needsToBeUnlocked();

        $this->defineProperty('to', $this->getLink('reversed')->from = $name);

        return $this;
    }

    public function reversedName(string $reversedName)
    {
        $this->needsToBeUnlocked();
        $this->needsToBeUnowned();

        $this->linksName['reversed'] = $reversedName;

        return $this;
    }

    public function usePivot(string $pivotClass=null)
    {
        $this->needsToBeUnlocked();

        $this->defineProperty('usePivot', true);
        $this->defineProperty('pivotClass', $pivotClass);

        return $this;
    }

    protected function loadPivotMeta()
    {
        $offMeta = $this->getMeta();
        $onMeta = $this->on::getMeta();
        $onTable = $onMeta->getTableName();
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

    public function owned()
    {
        if ($this->on === 'self') {
            $this->on($this->getMeta()->getModelClass());
        }

        $this->loadPivotMeta();

        $this->defineProperty('off', $this->getLink('reversed')->on = $this->getMeta()->getModelClass());

        parent::owned();
    }

    protected function locking()
    {
        if (!$this->on) {
            throw new \Exception('Related model settings needed. Set it by calling `on` method');
        }

        $this->defineProperty('reversedName', $this->getLink('reversed')->name);
        $this->defineProperty('from', $this->getLink('reversed')->to = $this->getMeta()->getPrimary()->all()[0]->attname);

        parent::locking();
    }

    public function isOnSelf()
    {
        return $this->on === $this->getMeta()->getModelClass();
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
            $class = config('field.constraints.types.unique.class');
        }

        if (\is_null($this->pivotMeta)) {
            $this->constraints['unique'] = \func_get_args();
        } else {
            $this->pivotMeta->unique([$this->pivotTo, $this->pivotFrom], ...\func_get_args());
        }

        return $this;
    }
}
