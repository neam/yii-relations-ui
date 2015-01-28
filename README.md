Yii Relations UI
================

To simplify editing relations in a Yii 1 app.

## Current widget

### Has-many relation input based on Handsontable.

Use in a Yii 1 app as follows:

```php
$this->widget('\neam\yii_relations_ui\widgets\HasManyHandsontableInput', [
    'model' => $model,
    'relation' => 'routes',
]);
```

Use any customized columns and [handsontable settings](https://github.com/handsontable/jquery-handsontable/wiki):

```php
$this->widget('\neam\yii_relations_ui\widgets\HasManyHandsontableInput', [
    'model' => $model,
    'relation' => 'routes',
    'settings' => [
        'columns' => [
            (object) ['data' => 'id'],
            (object) ['data' => 'route'],
            (object) ['data' => 'canonical', 'type' => 'checkbox'],
            (object) ['data' => 'route_type_id'],
            (object) ['data' => 'node_id'],
        ]
    ]
]);
```

Note: This widget requires Yii <- Yii 2 bridge available at https://github.com/neam/yii-yii2-bridge

#### Screenshot 1 - A hasMany relation "routes" displayed in handsontable

![Handsontable Screenshot 1](/docs/screenshots/handsontable-screen-1.jpg?raw=true "A hasMany relation "routes" displayed in handsontable")

#### Screenshot 2 - A new route being added

![Handsontable Screenshot 2](/docs/screenshots/handsontable-screen-2.jpg?raw=true "A new route being added")

#### Screenshot 3 - The new route after saving

![Handsontable Screenshot 3](/docs/screenshots/handsontable-screen-3.jpg?raw=true "The new route after saving")