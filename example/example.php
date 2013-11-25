<?php

use FormBuilder\FormBuilder;
use SQLTools\Command\DropDataBase;
use SQLTools\SQLConfig;
use SQLTools\SQLTools;

require_once("../vendor/autoload.php");

define("DB_NAME", "formbuilder_test");

$formBuilder = new FormBuilder(new SQLConfig("localhost", "root"));

$json = json_decode('
{
    "id": 2,
    "label": "News",
    "name": "news",
    "active": true,
    "shared": true,
    "components": [
        {
            "type": "textField",
            "properties": {
                "name": "title",
                "placeholder": "",
                "label": "Title",
                "classes": "form-control",
                "maxLength": "255",
                "multiLine": false,
                "rows": 3,
                "required": true,
                "id": "title"
            }
        },
        {
            "type": "textField",
            "properties": {
                "name": "description",
                "placeholder": "Write your description here",
                "label": "Description",
                "classes": "form-control",
                "maxLength": "250",
                "multiLine": true,
                "rows": 3,
                "required": true,
                "id": "description"
            }
        }
    ]
}
');

FormBuilder::$autoAddPrimaryKey = true;

$formBuilder->parse($json);

try
{
    SQLTools::execute(new DropDataBase("formbuilder_test"));
}
catch(PDOException $e)
{

}

SQLTools::create_database(DB_NAME);

SQLTools::getConfig()->setDb(DB_NAME);

$formBuilder->saveForm(DB_NAME);