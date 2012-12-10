<?php

namespace KFI\FrameworkBundle\Model;

interface EntityWithMetas
{
    public function getId();
    public function getMetasHelper();
    public function setMetasHelper();

    /**
     * @return EntityMeta[]
     */
    public function getMetas();
}
