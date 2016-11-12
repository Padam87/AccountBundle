<?php

namespace Padam87\AccountBundle\DependencyInjection;

use Padam87\AccountBundle\Doctrine\Type\CurrencyType;
use Padam87\AccountBundle\Doctrine\Type\MoneyType;
use Padam87\AccountBundle\Entity\Account;
use Padam87\AccountBundle\Entity\Transaction;
use Padam87\AccountBundle\Entity\UserInterface;
use Padam87\AccountBundle\EventListener\RegistrationListener;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

class Padam87AccountExtension extends Extension implements PrependExtensionInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);
        $bundles = $container->getParameter('kernel.bundles');

        $container->setParameter('padam87_account_config', $config);

        if ($config['registration_listener']) {
            if (!isset($bundles['FOSUserBundle'])) {
                throw new \LogicException('The registration listener feature requires the FOSUserBundle.');
            }

            $container->setDefinition(
                'padam87_account.registration_listener',
                (new Definition(RegistrationListener::class))
                    ->addTag('kernel.event_subscriber')
                    ->addArgument(new Reference('doctrine'))
                    ->addArgument($config)
            );
        }

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');
    }

    /**
     * {@inheritdoc}
     */
    public function prepend(ContainerBuilder $container)
    {
        $configs = $container->getExtensionConfig($this->getAlias());
        $config = $this->processConfiguration(new Configuration(), $configs);

        $container->prependExtensionConfig(
            'doctrine',
            [
                'dbal' => [
                    'types' => [
                        'money' => MoneyType::class,
                        'currency' => CurrencyType::class,
                    ]
                ],
                'orm' => [
                    'resolve_target_entities' => [
                        Account::class => $config['classes']['account'],
                        Transaction::class => $config['classes']['transaction'],
                        UserInterface::class => $config['classes']['user'],
                    ],
                    'mappings' => [
                        'Money' => [
                            'type' => 'xml',
                            'dir' => '%kernel.root_dir%/../vendor/padam87/account-bundle/Resources/Money/doctrine',
                            'prefix' => 'Money',
                            'is_bundle' => false,
                        ]
                    ]
                ]
            ]
        );
    }
}
