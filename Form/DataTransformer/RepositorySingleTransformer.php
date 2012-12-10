<?php

namespace KFI\FrameworkBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Doctrine\Common\Persistence\ObjectRepository;

class RepositorySingleTransformer implements DataTransformerInterface
{
    /** @var ObjectRepository */
    private $repo;

    public static function bind(FormBuilderInterface $builder, ObjectRepository $repo)
    {
        $builder->addViewTransformer(new self($repo));
    }

    public function __construct(ObjectRepository $repo)
    {
        $this->repo = $repo;
    }

    /**
     * @param mixed $entity
     * @return mixed|string
     */
    public function transform($entity)
    {
        return isset($entity) ? $entity->getId() : '';
    }

    /**
     * @param mixed $id
     * @return mixed|object
     */
    public function reverseTransform($id)
    {
        return isset($id) ? $this->repo->find($id) : null;
    }
}