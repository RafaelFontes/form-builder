<?php

namespace FormBuilder;


use FormBuilder\Base\IComponent;
use FormBuilder\Base\IComponentFactory;
use SQLTools\Command\AddField;
use SQLTools\Command\ChangeField;
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

    /**
     * @var array
     */
    private $components = array();

    public function __construct(SQLConfig $config)
    {
        SQLTools::configure($config);

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

    public function getHtml()
    {
        $html = "";
        /**
         * @var IComponent $component
         */
        foreach($this->components as $component)
        {
            $html .= $component->toHtmlField() . "\n";
        }

        return $html;
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
            }
        }
        else
        {
            // CREATE
            SQLTools::create_table($this->name, $this->getTableFields());
        }

    }
}