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
 *   'relatedAttributes' => [
 *       'route',
 *       'route_type_id',
 *       'node_id',
 *   ]
 *  ]
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
     * @var array $relatedAttributes
     */
    public $relatedAttributes = [];

    public function init()
    {

        // we work against the virtual attribute defined by HandsontableInputBehavior
        $this->attribute = 'handsontable_input_' . $this->relation;

        if (!$this->hasModel()) {
            throw new \CException("HasManyHandsontableInput requires a model");
        }

        // default to use all attributes of the related model class
        if (empty($this->relatedAttributes)) {
            $relations = $this->model->relations();
            $modelClass = $relations[$this->relation][1];
            $this->relatedAttributes = array_keys($modelClass::model()->attributes);
        }

        // format the handsontable settings in the way handsontable expects it
        $columns = array();
        $colHeaders = array();
        $dataSchema = new \stdClass();
        foreach ($this->relatedAttributes as $attribute) {
            $column = new \stdClass;
            $column->data = $attribute;
            $colHeaders[] = $attribute;
            $columns[] = $column;
            $dataSchema->{$attribute} = null;
        }

        $this->settings = array_merge($this->settings,
            [
                "colHeaders" => $colHeaders,
                "columns" => $columns,
                "dataSchema" => $dataSchema,
                "startCols" => count($colHeaders),
                "minSpareRows" => 1,
                "rowHeaders" => false,
            ]
        );
        parent::init();
    }

}
