<?php

/*
 * This file is part of the Admin DDD Extensions Bundle.
 *
 * Copyright (c) 2017-present LIN3S <info@lin3s.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace LIN3S\AdminDDDExtensionsBundle\Action;

use LIN3S\AdminBundle\Configuration\Model\Entity;
use LIN3S\AdminBundle\Configuration\Type\ActionType;
use LIN3S\SharedKernel\Application\CommandBus;
use LIN3S\SharedKernel\Exception\Exception;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Translation\TranslatorInterface;

final class HandleCommandActionType implements ActionType
{
    use OptionResolver;
    use Redirect;

    private $flashBag;
    private $twig;
    private $formFactory;
    private $commandBus;
    private $urlGenerator;
    private $translator;

    public function __construct(
        FormFactoryInterface $formFactory,
        CommandBus $commandBus,
        \Twig_Environment $twig,
        FlashBagInterface $flashBag,
        UrlGeneratorInterface $urlGenerator,
        TranslatorInterface $translator
    ) {
        $this->formFactory = $formFactory;
        $this->commandBus = $commandBus;
        $this->twig = $twig;
        $this->flashBag = $flashBag;
        $this->urlGenerator = $urlGenerator;
        $this->translator = $translator;
    }

    public function execute($entity, Entity $config, Request $request, $options = [])
    {
        $entityName = $config->name();

        $this->checkRequired($options, 'form');

        $form = $this->formFactory->create($options['form'], $entity);
        if ($request->isMethod('POST') || $request->isMethod('PUT') || $request->isMethod('PATCH')) {
            $form->handleRequest($request);
            if ($form->isValid() && $form->isSubmitted()) {
                try {
                    $command = $form->getData();
                    $this->commandBus->handle($command);

                    $this->flashBag->add(
                        'lin3s_admin_success',
                        sprintf('The %s is successfully saved', $entityName)
                    );

                    return $this->redirect($this->urlGenerator, $options, $entityName, $command);
                } catch (Exception $exception) {
                    $this->addError($exception, $options);
                }
            }
            $this->flashBag->add(
                'lin3s_admin_error',
                sprintf(
                    'Errors while saving %s. Please check all fields and try again',
                    $config->name()
                )
            );
        }

        return new Response(
            $this->twig->render('Lin3sAdminBundle:Admin:form.html.twig', [
                'entity'       => $entity,
                'entityConfig' => $config,
                'form'         => $form->createView(),
            ])
        );
    }

    private function addError(Exception $exception, array $options)
    {
        $exceptions = $this->catchableExceptions($options);
        $exceptionClassName = get_class($exception);


        if (array_key_exists($exceptionClassName, $exceptions)) {
            $exceptionName = get_class($exception);
            if (array_key_exists($exceptionName, $exceptions)) {
                $translation = $this->translator->trans($exceptions[$exceptionName]);
            }
            $this->flashBag->add('lin3s_admin_error', isset($translation) ?
                $translation : $exceptions[$exceptionClassName]);
        }
    }

    private function catchableExceptions(array $options)
    {
        if (!isset($options['catchable_exceptions'])) {
            return [];
        }

        return json_decode($options['catchable_exceptions'], true);
    }
}
