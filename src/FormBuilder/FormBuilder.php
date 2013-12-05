<?php

namespace FormBuilder;


use FormBuilder\Base\IComponent;
use FormBuilder\Base\IComponentFactory;
use SQLTools\Command\AddField;
use SQLTools\Command\ChangeField;
use SQLTools\Command\DropField;
use SQLTools\Entity\Field;
use SQLTools\SQLConfig;
use SQLTools\SQLTools;

class FormBuilder {

    /**
     * @var SQLConfig $config
     */
    private $config;

    /**
     * @var IComponentFactory $factory
     */
    private $factory;

    private $name;

    static public $autoAddPrimaryKey=false;

    private $templateFiles = array();

    /**
     * @var \Closure
     */
    private $onFieldCreate = null;

    /**
     * @var array
     */
    private $components = array();

    public function __construct(SQLConfig $config)
    {

        SQLTools::configure($config);
    }

    public function setTemplateFile($fieldType, $file)
    {
        $this->templateFiles[$fieldType] = $file;
        return $this;
    }


    public function parse(\stdClass $json, IComponentFactory $factory = null)
    {
        $this->factory = (!$factory) ? new ComponentFactory() : $factory;
        $this->name = $json->name;

        $this->parseComponents( $json->components );
    }

    private function parseComponents(array $components)
    {
        foreach($components as $component)
        {
            $cmp = $this->factory->getComponent($component->type);

            $cmp->loadProperties($component->properties);

            if (!empty($this->templateFiles[$component->type]))
            {
                $cmp->setTemplate($this->templateFiles[$component->type]);
            }

            $this->components[] = $cmp;
        }
    }

    private function getTableFields()
    {
        $fields = array();

        if (self::$autoAddPrimaryKey)
        {
            $fields[] = new Field("id", "int", 10, false, null, true,null, "AUTO_INCREMENT");
        }
        /**
         * @var IComponent $component
         */
        foreach($this->components as $component)
        {
            $fields[] = $component->toTableField();
        }

        return $fields;
    }

    public function getHtml(array $data = null)
    {
        $html = "";
        /**
         * @var IComponent $component
         */
        foreach($this->components as $component)
        {
            $html .= $component->getHtml($data) . "\n";
        }

        return $html;
    }

    public function setOnFieldCreatedHandler($handler)
    {
        $this->onFieldCreate = $handler;
    }

    public function saveForm($dbName)
    {


        $stmt = SQLTools::getConnection()->prepare(
            "SELECT COUNT(*) total
             FROM information_schema.tables
             WHERE table_schema = :db
             AND table_name = :table;");

        $stmt->execute(array(
            "db" => $dbName,
            "table" => $this->name
        ));

        if ($stmt->fetchColumn())
        {
            // ALTER
            /**
             * @var IComponent $component
             */
            $columns = array("id");
            foreach($this->components as $component)
            {
                $stmt = SQLTools::getConnection()->prepare(
                    "SELECT COUNT(*) total
                     FROM information_schema.columns
                     WHERE
                     table_schema = :db AND
                     table_name   = :table AND
                     column_name  = :column;");

                $stmt->execute(array(
                    "db" => $dbName,
                    "table" => $this->name,
                    "column" => $component->getId()
                ));

                if ($stmt->fetchColumn())
                {
                    SQLTools::execute(new ChangeField($this->name, $component->getId(), $component->toTableField()));
                }
                else
                {
                    SQLTools::execute(new AddField($this->name, $component->toTableField()));
                }

                if (!empty($this->onFieldCreate))
                {
                    $this->onFieldCreate->__invoke($this, $component);
                }

                $columns[] = $component->getName();
            }

            $stmt = SQLTools::getConnection()->prepare("SELECT column_name
                     FROM information_schema.columns
                     WHERE
                     table_schema = :db AND
                     table_name   = :table AND
                     column_name not in(:columns);");

            $stmt->execute(array(
                "db" => $dbName,
                "table" => $this->name,
                "columns" => implode(",", $columns)
            ));


            $columnsToDelete = $stmt->fetchAll(\PDO::FETCH_OBJ);

            foreach($columnsToDelete as $columnToDelete)
            {
                SQLTools::execute(new DropField($this->name, $columnToDelete->column_name));
            }

        }
        else
        {

            $fields = $this->getTableFields();

            // CREATE
            SQLTools::create_table($this->name, $fields);

            if (!empty($this->onFieldCreate))
            {
                foreach($fields as $field)
                {
                    $this->onFieldCreate->__invoke($this, $field);
                }
            }
        }
    }

    public function getTableName()
    {
        return $this->name;
    }
}