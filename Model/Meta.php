<?php

namespace KFI\FrameworkBundle\Model;

interface EntityMeta
{
    public function __construct($parent = null, $key = null, $value = null);
    public function getID();
    public function getKey();
    public function getValue();
    public function setValue($value);

    /** @return EntityWithMetas */
    public function getParent();
}
