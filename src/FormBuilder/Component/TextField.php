<?php

namespace FormBuilder\Component;

use FormBuilder\Entity\Component;

class TextField extends Component {

    protected $length = 150;
    protected $type = "VARCHAR";
    protected $label = "Text:";

    /**
     * @param array $properties
     * @return void
     */
    public function loadProperties(\stdClass $properties)
    {
        $this->properties = $properties;
        foreach($properties as $name => $value)
        {
            switch($name)
            {
                case "name" :
                    $this->name = $value;
                    break;
                case "maxLength":
                    $this->length = $value;
                    break;
                case "multiLine":
                    if ($value)
                    {
                        $this->type = "TEXT";
                        $this->length = null;
                    }
                    break;
                case "required":
                    $this->nullable = !$value;
                    break;
                case "id":
                    $this->id = $value;
                    break;
                case "label":
                    $this->label = $value;
                    break;
            }
        }
    }

    /**
     * @param array $data
     * @return string
     */
    public function toHtmlField(array $data = null)
    {
        $html = '<div><label>'.$this->label.'</label><input type="text" id="'.$this->name.'" name="'.$this->name.'" ';

        if (!empty($data))
        {
            $html .= 'value="'.$data[$this->name].'"';
        }

        $html .= "/></div>";

        return $html;
    }
}