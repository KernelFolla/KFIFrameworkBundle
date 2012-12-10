<?php

namespace KFI\FrameworkBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
//use Symfony\Bridge\Doctrine\Form\DataTransformer\CollectionToArrayTransformer;
//use Symfony\Bridge\Doctrine\Form\EventListener\MergeDoctrineCollectionListener;
use Symfony\Component\Form\FormBuilderInterface;
use Doctrine\Common\Persistence\ObjectRepository;

class RepositoryTransformer implements DataTransformerInterface
{
    /** @var ObjectRepository */
    private $repo;

    public static function bind(FormBuilderInterface $builder, ObjectRepository $repo)
    {
        $builder
            //->addViewTransformer(new CollectionToArrayTransformer(), true)
            //->addEventSubscriber(new MergeDoctrineCollectionListener())
            ->addViewTransformer(new self($repo));
    }

    public function __construct(ObjectRepository $repo)
    {
        $this->repo = $repo;
    }

    /**
     * transform array of objects in an array of ids
     *
     * @param  object[] $collection
     * @return int[]
     */
    public function transform($collection)
    {
        $ret = array();
        if (!$collection) {
            return $ret;
        }
        foreach ($collection as $item) {
            $ret[] = $item->getId();
        }

        return $ret;
    }

    /**
     * transform an array of id in array of object
     *
     * @param mixed $ids
     * @return object[]
     * @throws TransformationFailedException
     */
    public function reverseTransform($ids)
    {
        $ret = array();
        if (!$ids) {
            return $ret;
        }
        foreach ($ids as $id) {
            if ($tmp = $this->repo->findOneBy(compact('id'))) {
                $ret[] = $tmp;
            } else {
                throw new TransformationFailedException(sprintf(
                    'l\'oggetto con id %s non esiste!',
                    $id
                ));
            }
        }

        return $ret;
    }
}