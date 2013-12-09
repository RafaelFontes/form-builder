<?php

namespace FormBuilder\Base;


use SQLTools\Entity\Field;

interface IComponent {
    /**
     * @param \stdClass $properties
     * @return void
     */
    public function loadProperties(\stdClass $properties);

    /**
     * @param \stdClass $json
     * @return mixed
     */
    public function getHtml(array $json = null);

    /**
     * @param \stdClass $json
     * @return Field
     */
    public function toTableField(\stdClass $json = null);

    public function getId();
    public function getName();
    public function setTemplate($phtml);
} 