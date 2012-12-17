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
     * @param $children
     * @param $pos
     * @return mixed
     */
    abstract protected function createNewItem($children, $pos);

    /**
     * @param $item
     * @param $pos
     * @return mixed
     */
    abstract protected function bindTmpItem($item, $pos);

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
            if(isset($item)){
                $c                           = $this->transformItem($item);
                $this->tmpItems[$c->getId()] = $item;
                $ret[]                       = $c;
            }
        }

        return $ret;
    }


    public function reverseTransform($items)
    {
        $items = $this->childrenTransformer->reverseTransform($items);
        $ret   = new ArrayCollection();
        foreach ($items as $item) {
            $ret->add($this->reverseTransformItem($item, $ret->count()));
        }
        return $ret;
    }

    /**
     * @param object $item
     * @param int $pos
     * @return object
     */
    public function reverseTransformItem($item, $pos)
    {
        $id = $item->getId();

        return isset($this->tmpItems[$id]) ?
            $this->bindTmpItem($this->tmpItems[$id], $pos)
            : $this->createNewItem($item,$pos);
    }
}