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
use Laramore\Traits\Field\ManyToManyRelation;
use Laramore\Fields\LinkField;
use Laramore\Fields\Constraint\Unique;
use Laramore\Eloquent\FakePivot;
use Laramore\Meta;
use MetaManager;

class ManyToMany extends CompositeField
{
    use ManyToManyRelation;

    protected $reversedName;
    protected $usePivot;
    protected $pivotClass;

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

    public function reversedName(string $reversedName=null)
    {
        $this->needsToBeUnlocked();

        $this->linksName['reversed'] = $reversedName ?: '+{modelname}';

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
        $offTable = $offMeta->getTableName();
        $onTable = $onMeta->getTableName();
        $offName = $offMeta->getModelClassName();
        $onName = Str::singular($this->name);

        $namespaceName = 'App\\Pivots';
        $pivotClassName = ucfirst($offName).ucfirst($onName);
        $pivotClass = "$namespaceName\\$pivotClassName";

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

            $this->pivotMeta->set(
                $offName,
                $offField = Foreign::field()->on($this->getMeta()->getModelClass())->reversedName('pivot'.ucfirst($onTable))
            );

            $this->pivotMeta->set(
                $onName,
                $onField = Foreign::field()->on($this->on)->reversedName('pivot'.ucfirst($this->name))
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
            $class = config('fields.constraints.types.unique.class');
        }

        if (\is_null($this->pivotMeta)) {
            $this->constraints['unique'] = \func_get_args();
        } else {
            $this->pivotMeta->unique([$this->pivotTo, $this->pivotFrom], ...\func_get_args());
        }

        return $this;
    }
}
