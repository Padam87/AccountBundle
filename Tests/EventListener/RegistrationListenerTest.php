<?php

namespace Padam87\AccountBundle\Tests\EventListener;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManager;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use Mockery as m;
use Money\Currency;
use Padam87\AccountBundle\EventListener\RegistrationListener;
use Padam87\AccountBundle\Service\Accountant;
use Padam87\AccountBundle\Tests\Resources\Entity\User;
use PHPUnit\Framework\TestCase;

class RegistrationListenerTest extends TestCase
{
    use m\Adapter\Phpunit\MockeryPHPUnitIntegration;

    public function tearDown()
    {
        parent::tearDown();

        m::close();
    }

    /**
     * @test
     *
     * @expectedException \LogicException
     */
    public function shouldThrowExceptionIfNotCompatible()
    {
        $doctrine = m::mock(Registry::class);

        $accountant = m::mock(Accountant::class);

        $event = m::mock(FilterUserResponseEvent::class);
        $event->shouldReceive('getUser')->andReturn(new \stdClass());

        $listener = new RegistrationListener($doctrine, $accountant, ['currencies' => ['EUR']]);
        $listener->addAccounts($event);
    }

    /**
     * @test
     */
    public function shouldCreateAccount()
    {
        $user = new User();

        $em = m::mock(EntityManager::class);
        $em->shouldReceive('persist')->once();
        $em->shouldReceive('flush')->once();

        $doctrine = m::mock(Registry::class);
        $doctrine->shouldReceive('getManager')->andReturn($em);

        $accountant = m::mock(Accountant::class);
        $accountant->shouldReceive('getOrCreateAccount')->with($user, m::type(Currency::class))->andReturn(null);

        $event = m::mock(FilterUserResponseEvent::class);
        $event->shouldReceive('getUser')->andReturn($user);

        $listener = new RegistrationListener($doctrine, $accountant, ['currencies' => ['EUR']]);
        $listener->addAccounts($event);
    }

    /**
     * @test
     */
    public function shouldCreateMultipleAccounts()
    {
        $user = new User();

        $em = m::mock(EntityManager::class);

        $em->shouldReceive('persist')->times(2);
        $em->shouldReceive('flush')->once();

        $doctrine = m::mock(Registry::class);
        $doctrine->shouldReceive('getManager')->andReturn($em);

        $accountant = m::mock(Accountant::class);
        $accountant->shouldReceive('getOrCreateAccount')->with($user, m::type(Currency::class))->andReturn(null);

        $event = m::mock(FilterUserResponseEvent::class);
        $event->shouldReceive('getUser')->andReturn($user);

        $listener = new RegistrationListener($doctrine, $accountant, ['currencies' => ['EUR', 'USD']]);
        $listener->addAccounts($event);
    }
}
