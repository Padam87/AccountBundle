<?php

namespace Padam87\AccountBundle\DependencyInjection;

use Padam87\AccountBundle\Entity\Account;
use Padam87\AccountBundle\Entity\Transaction;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
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

        $container->setParameter('padam87_account_config', $config);

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
                'orm' => [
                    'resolve_target_entities' => [
                        Account::class => $config['classes']['account'],
                        Transaction::class => $config['classes']['transaction'],
                    ],
                ]
            ]
        );
    }
}
