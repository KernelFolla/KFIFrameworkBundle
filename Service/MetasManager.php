<?php

namespace KFI\FrameworkBundle\Services;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Persistence\ObjectRepository;

use \KFI\FrameworkBundle\Model\WithId;

class MetasManager
{
    /** @var ObjectManager */
    protected $manager;
    /** @var ObjectRepository */
    protected $repo;
    protected $schema;
    protected $helperClassName;
    protected $helpers;

    public function __construct(ObjectManager $manager, $metaRepository, $helperClassName, $schema){
        $this->manager = $manager;
        $this->repo = $manager->getRepository($metaRepository);
        $this->schema = $schema;
    }

    public function getHelper(WithId $entity){
        $id = $entity->getId();
        if(!isset($this->helpers[$id])){
            $className = $this->helperClassName;
            $this->helpers[$id] = new $className($this, $entity);
        }
    }
}