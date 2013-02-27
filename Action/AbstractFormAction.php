<?php

namespace KFI\FrameworkBundle\Action;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

use Mercurio\TuttoRespiroBundle\Form\Type\OrderType;
use Mercurio\TuttoRespiroBundle\Service\OrderManager;
use Mercurio\TuttoRespiroBundle\Entity\Product;

abstract class AbstractFormAction
{
    /** @var OrderManager */
    protected $orderManager;
    /** @var Controller */
    protected $controller;

    /** @var Request */
    protected $request;
    /** @var FormInterface */
    protected $form;

    public function __construct(Controller $controller, Product $product){
        $this->controller = $controller;
        $this->orderManager = $controller->get('app.order.manager');
        $this->form = $controller->createForm(
            new OrderType(),
            $this->orderManager->getNewOrder($product)
        );
        $this->request = $controller->getRequest();
    }

    public function execute()
    {
        if ($this->checkForm()) {
            $this->dispatchForm();
        }
        return array(
            'form' => $this->form->createView(),
            'order' => $this->form->getData()
        );
    }

    protected function checkForm()
    {
        if ($this->request->getMethod() != 'POST') {
            return false;
        }
        $this->form->bind($this->request);

        return $this->form->isValid();
    }

    public function dispatchForm()
    {
        /** @var $order \Mercurio\TuttoRespiroBundle\Entity\Order */
        $order = $this->orderManager->save($this->form->getData());
        $email = $order->getUser()->getEmail();
        $message = \Swift_Message::newInstance();
        $message->setSubject(sprintf('[%s] ordine n. ', $order->getId()));
        $message->setFrom($c->getParameter('option_site_mail'));
        $message->setTo('marino@comodo.it');
        $message->setBody($body,'text/html');
        $this->get('mailer')->send($message);

        //$this->return = $this->nextRedirect;
    }
}
