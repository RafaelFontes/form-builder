<?php

namespace FormBuilder\Base;


interface IComponentFactory {
    /**
     * @param String $type
     * @return IComponent
     */
    public function getComponent($type);
} 