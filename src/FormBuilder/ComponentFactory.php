<?php

namespace FormBuilder;


use FormBuilder\Base\IComponent;
use FormBuilder\Base\IComponentFactory;
use FormBuilder\Component\ImageLoader;
use FormBuilder\Component\TextField;

class ComponentFactory implements IComponentFactory {

    const TEXT_FIELD = "textField";
    const IMAGE_UPLOADER = "imageLoader";

    static public $templates=array();

    /**
     * @param String $type
     * @return IComponent
     */
    public function getComponent($type)
    {
        switch($type)
        {
            case self::TEXT_FIELD :
                return new TextField(self::$templates[$type]);

            case self::IMAGE_UPLOADER :
                return new ImageLoader(self::$templates[$type]);
        }

        return null;
    }

} 