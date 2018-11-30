<?php

namespace Padam87\AccountBundle\Tests\DependencyInjection;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Padam87\AccountBundle\DependencyInjection\Padam87AccountExtension;
use Padam87\AccountBundle\Service\Accountant;
use Padam87\AccountBundle\Tests\Resources\Entity\Account;
use Padam87\AccountBundle\Tests\Resources\Entity\Transaction;

class ExtensionTest extends AbstractExtensionTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function getContainerExtensions()
    {
        return [
            new Padam87AccountExtension()
        ];
    }

    /**
     * @test
     */
    public function serviceRegistration()
    {
        $this->load(
            [
                'classes' => [
                    'account' => Account::class,
                    'transaction' => Transaction::class,
                ]
            ]
        );

        $this->assertContainerBuilderHasService('padam87_account.accountant', Accountant::class);

        $this->assertContainerBuilderHasServiceDefinitionWithTag(
            'padam87_account.transaction_listener',
            'doctrine.event_subscriber'
        );

        $this->assertContainerBuilderHasServiceDefinitionWithTag(
            'padam87_account.form.extension.money_type_extension',
            'form.type_extension',
            [
                'extended_type' => 'Symfony\Component\Form\Extension\Core\Type\MoneyType',
                'extended-type' => 'Symfony\Component\Form\Extension\Core\Type\MoneyType',
            ]
        );
    }

    /**
     * @test
     * @expectedException \LogicException
     */
    public function registrationListenerExceptionWithoutUserBundle()
    {
        $this->load(
            [
                'classes' => [
                    'account' => Account::class,
                    'transaction' => Transaction::class,
                ],
                'registration_listener' => true,
            ]
        );
    }

    /**
     * @test
     */
    public function registrationListener()
    {
        $this->container->setParameter('kernel.bundles', ['FOSUserBundle' => true]);

        $this->load(
            [
                'classes' => [
                    'account' => Account::class,
                    'transaction' => Transaction::class,
                ],
                'registration_listener' => true,
            ]
        );

        $this->assertContainerBuilderHasService('padam87_account.registration_listener');
    }

    /**
     * @test
     */
    public function accountantClass()
    {
        $this->load(
            [
                'classes' => [
                    'account' => Account::class,
                    'transaction' => Transaction::class,
                ],
                'accountant' => [
                    'class' => \stdClass::class,
                ]
            ]
        );

        $this->assertContainerBuilderHasService('padam87_account.accountant', \stdClass::class);
    }
}
