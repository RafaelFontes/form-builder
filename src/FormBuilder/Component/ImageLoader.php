<?php

namespace FormBuilder\Component;

use FormBuilder\Entity\Component;

class ImageLoader extends Component {

    protected $type   = "int";
    protected $label  = "Image";
    protected $name   = "fileId";

    /**
     * @param \stdClass $properties
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
                case "label":
                    $this->label = $value;
                    break;
                case "required":
                    $this->nullable = !$value;
                    break;
                case "id":
                    $this->id = $value;
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
        $html = '<div><label>'.$this->label.'</label><input type="file" id="'.$this->name.'" name="'.$this->name.'" />';

        if (!empty($data))
        {
            $html .= '<img src="'.$data[$this->name].'" />';
        }

        $html .= "</div>";

        return $html;
    }

}