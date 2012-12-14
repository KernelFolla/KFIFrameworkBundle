<?php

namespace KFI\FrameworkBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @see http://en.wikipedia.org/wiki/Junction_table
 */
abstract class JunctionTableTransformer implements DataTransformerInterface
{
    private $repo;
    private $childrenTransformer;

    /** @var object[] */
    private $tmpItems = array();

    /**
     * @param object $item
     * @return object
     */
    abstract protected function transformItem($item);

    /**
     * @param object $children
     * @return object
     */
    abstract protected function createNewItem($children);

    public function __construct(ObjectRepository $junctionRepo, ObjectRepository $childrenRepo)
    {
        $this->repo                = $junctionRepo;
        $this->childrenTransformer = new RepositoryTransformer($childrenRepo);
    }

    /**
     * @param object[] $collection
     * @return array|mixed
     */
    public function transform($collection)
    {
        $ret = array();
        if (!$collection) {
            return $ret;
        }

        foreach ($collection as $item) {
            $c                           = $this->transformItem($item);
            $this->tmpItems[$c->getId()] = $item;
            $ret[]                       = $c;
        }

        return $ret;
    }


    public function reverseTransform($items)
    {
        $items = $this->childrenTransformer->reverseTransform($items);
        $ret   = new ArrayCollection();
        foreach ($items as $item) {
            $obj   = $this->reverseTransformItem($item);
            $ret->add($obj);
        }
        return $ret;
    }

    /**
     * @param Object $item
     * @return Object
     */
    public function reverseTransformItem($item)
    {
        $id = $item->getId();

        return isset($this->tmpItems[$id]) ?
            $this->tmpItems[$id]
            : $this->createNewItem($item);
    }
}