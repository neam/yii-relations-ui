<?php

namespace neam\yii_relations_ui\widgets;

use neam\yii_handsontable_input\widgets\HandsontableInput;

/**
 * Has-many relation input based on handsontable.
 *
 * Use in a Yii 1 app as follows:
 *
 * ```php
 * $this->widget('\neam\yii_relations_ui\widgets\HasManyHandsontableInput',[
 *   'model' => $model->node(),
 *   'relation' => 'routes',
 *   'settings' => [
 *       'columns' => [
 *           (object) ['data' => 'id'],
 *           (object) ['data' => 'route'],
 *           (object) ['data' => 'canonical', 'type' => 'checkbox'],
 *           (object) ['data' => 'route_type_id'],
 *           (object) ['data' => 'node_id'],
 *       ]
 *    ]
 * ]);
 * ```
 */
class HasManyHandsontableInput extends HandsontableInput
{

    /**
     * Required by CInputWidget
     * @var
     */
    public $options;

    protected $jsWidget;

    /**
     * @var string $relation
     */
    public $relation = null;

    public function init()
    {

        // we work against the virtual attribute defined by HandsontableInputBehavior
        $this->attribute = 'handsontable_input_' . $this->relation;

        if (!$this->hasModel()) {
            throw new \CException("HasManyHandsontableInput requires a model");
        }

        // attributes of the related model class
        $relations = $this->model->relations();
        $modelClass = $relations[$this->relation][1];
        $relatedAttributes = array_keys($modelClass::model()->attributes);

        // default settings

        if (empty($this->settings["minSpareRows"])) {
            $this->settings["minSpareRows"] = 1; // So that we can add a new
        }

        if (empty($this->settings["rowHeaders"])) {
            $this->settings["rowHeaders"] = false;
        }

        if (empty($this->settings["dataSchema"])) {
            $dataSchema = new \stdClass();
            foreach ($relatedAttributes as $attribute) {
                $dataSchema->{$attribute} = null;
            }
            $this->settings["dataSchema"] = $dataSchema;
        }

        if (empty($this->settings["columns"])) {
            $columns = array();
            foreach ($relatedAttributes as $attribute) {
                $column = new \stdClass;
                $column->data = $attribute;
                $columns[] = $column;
            }
            $this->settings["columns"] = $columns;
        }

        if (empty($this->settings["startCols"])) {
            $this->settings["startCols"] = count($this->settings["columns"]);
        }

        if (empty($this->settings["colHeaders"])) {
            $colHeaders = array();
            foreach ($this->settings["columns"] as $column) {
                $colHeaders[] = $column->data;
            }
            $this->settings["colHeaders"] = $colHeaders;
        }

        parent::init();
    }

}
