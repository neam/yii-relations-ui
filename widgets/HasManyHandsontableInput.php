<?php

namespace neam\yii_relations_ui\widgets;

use neam\yii_handsontable_input\widgets\HandsontableInput;

use Yii;

/**
 * Has-many relation input based on handsontable.
 *
 * Use in a Yii 1 app as follows:
 *
 * ```php
 * $this->widget('\neam\yii_relations_ui\widgets\HasManyHandsontableInput',[
 *   'model' => $model->ensureNode(),
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

    /**
     * @var string bower asset alias
     */
    public $appBowerAlias = 'bower-components';

    /**
     * @var bool whether or not to register select2 editor for handsontable (experimental)
     */
    public $registerSelect2Editor = false;

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

    public function run()
	{
        $return = parent::run();
        $this->registerAssets();
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
                    // Mark all suggestions (those without id set) as to be deleted so that they need to be manually confirmed before saving
                    if (empty($row->id)) {
                        $row->_delete = true;
                    } else {
                        $row->_delete = null;
                    }
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

    public function registerAssets() {

        // Necessary in order to publish the yii2 assets required for this view
        Yii::$app->getView()->registerYii2Assets();

        if (!$this->registerSelect2Editor) {
            return;
        }

        // Register select2-editor for handsontable
        $assetPathAlias = $this->appBowerAlias.'.select2';
        $assetsUrl = Yii::app()->assetManager->publish(Yii::getPathOfAlias($assetPathAlias), false, -1, $forceCopyAssets = false);
        Yii::app()->getClientScript()->registerScriptFile($assetsUrl . "/select2.js");
        Yii::app()->getClientScript()->registerCssFile($assetsUrl . "/select2.css");
        $assetPathAlias = $this->appBowerAlias . '.Handsontable-select2-editor';
        $assetsUrl = Yii::app()->assetManager->publish(Yii::getPathOfAlias($assetPathAlias), false, -1, $forceCopyAssets = false);
        Yii::app()->getClientScript()->registerScriptFile($assetsUrl . "/select2-editor.js");

    }

}
