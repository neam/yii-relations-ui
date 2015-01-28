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
 *           (object) ['data' => 'canonical', 'type' => 'checkbox', 'checkedTemplate' => 1, 'uncheckedTemplate' => 0], // example of using checkbox to save an attribute of type BOOLEAN NOT NULL
 *           (object) ['data' => 'route_type_id'],
 *           (object) ['data' => 'node_id'],
 *           (object) ['special' => 'delete_checkbox'], // special virtual column to mark which items should be deleted
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

        parent::init();
    }

    public function populateSettings($dataJson)
    {

        parent::populateSettings($dataJson);

        // Add necessary attributes in data for special _delete column(s)
        foreach ($this->settings["columns"] as $i => &$column) {
            if (isset($column->special) && $column->special == "delete_checkbox") {
                $column->data = "_delete";
                $column->type = "checkbox";
                $column->uncheckedTemplate = null;
                foreach ($this->settings["data"] as $k => &$row) {
                    $row->_delete = null;
                }
                $this->settings["dataSchema"]->_delete = null;
            }
        }

        if (empty($this->settings["startCols"])) {
            $this->settings["startCols"] = count($this->settings["columns"]);
        }

        if (empty($this->settings["colHeaders"])) {
            $colHeaders = array();
            foreach ($this->settings["columns"] as $c) {
                $colHeaders[] = $c->data;
            }
            $this->settings["colHeaders"] = $colHeaders;
        }

    }

}
