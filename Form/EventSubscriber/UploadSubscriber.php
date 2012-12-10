<?php

namespace KFI\FrameworkBundle\Form\EventSubscriber;

use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

use KFI\FileUploaderBundle\Services\FileUploader;

class UploadSubscriber implements EventSubscriberInterface
{
    /** @var FileUploader */
    protected $uploader;
    protected $uniqID;
    protected $groupName;

    public function __construct($uniqID, FileUploader $uploader, $groupName)
    {
        $this->uniqID    = $uniqID;
        $this->uploader  = $uploader;
        $this->groupName = $groupName;
    }

    public static function getSubscribedEvents()
    {
        return array(
            FormEvents::POST_SET_DATA => array('onPostSetData', 10),
            FormEvents::POST_BIND     => array('onPostBind', 10)
        );
    }

    public function onPostSetData(FormEvent $event)
    {
        $data  = $event->getForm()->getParent()->getData();
        $isNew = $data->getId() === null;
        if (!$isNew) {
            $this->uploader->syncFiles(
                array(
                    'from_folder'      => sprintf('%s/%s', $this->groupName, $data->getId()),
                    'to_folder'        => sprintf('tmp/%s/%s', $this->groupName, $this->uniqID),
                    'create_to_folder' => true
                )
            );
        } else {

        }
    }

    public function onPostBind(FormEvent $event)
    {
        var_dump($this->uniqID);
        die('onPostBind');

        $data = $event->getForm()->getParent()->getData();

        $this->uploader->syncFiles(
            array(
                'from_folder'        => sprintf('tmp/%s/%s', $this->groupName, $this->uniqID),
                'to_folder'          => sprintf('%s/%s', $this->groupName, $data->getId()),
                'remove_from_folder' => true,
                'create_to_folder'   => true
            )
        );
    }
}
