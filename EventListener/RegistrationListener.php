<?php

namespace Padam87\AccountBundle\EventListener;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManager;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use FOS\UserBundle\FOSUserEvents;
use Money\Currency;
use Padam87\AccountBundle\Entity\AccountHolderInterface;
use Padam87\AccountBundle\Entity\AccountInterface;
use Padam87\AccountBundle\Service\Accountant;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class RegistrationListener implements EventSubscriberInterface
{
    /**
     * @var Registry
     */
    private $doctrine;

    /**
     * @var array
     */
    private $config;

    /**
     * @var Accountant
     */
    private $accountant;

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            FOSUserEvents::REGISTRATION_COMPLETED => 'addAccounts'
        ];
    }

    /**
     * @param Registry   $doctrine
     * @param Accountant $accountant
     * @param array      $config
     */
    public function __construct(Registry $doctrine, Accountant $accountant, array $config)
    {
        $this->doctrine = $doctrine;
        $this->accountant = $accountant;
        $this->config = $config;
    }

    /**
     * @param FilterUserResponseEvent $event
     */
    public function addAccounts(FilterUserResponseEvent $event)
    {
        $user = $event->getUser();

        if (!$user instanceof AccountHolderInterface) {
            throw new \LogicException(
                'Your User class must implement the AccountHolderInterface
                provided by the bundle to use the RegistrationListener'
            );
        }

        /** @var EntityManager $em */
        $em = $this->doctrine->getManager();

        foreach ($this->config['currencies'] as $currency) {
            $account = $this->accountant->getOrCreateAccount($user, new Currency($currency));

            $em->persist($account);
        }

        $em->flush();
    }
}
