<?php

namespace FormBuilder;


use FormBuilder\Base\IComponent;
use FormBuilder\Base\IComponentFactory;
use FormBuilder\Component\TextField;

class ComponentFactory implements IComponentFactory {

    const TEXT_FIELD = "textField";

    /**
     * @param String $type
     * @return IComponent
     */
    public function getComponent($type)
    {
        switch($type)
        {
            case self::TEXT_FIELD :

                return new TextField();

            break;
        }

        return null;
    }

} 