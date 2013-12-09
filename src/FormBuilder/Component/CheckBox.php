<?php

namespace FormBuilder\Component;

use FormBuilder\Entity\Component;

class CheckBox extends Component {

    protected $length = 1;
    protected $type = "TINYINT";
    protected $label = "Enabled";

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
        $html = '<div><label><input type="checkbox" id="'.$this->name.'" name="'.$this->name.'" ';

        if (!empty($data))
        {
            $html .= 'value="'.$data[$this->name].'"';
        }

        $html .= '/>'.$this->label.'</label></div>';

        return $html;
    }
}