<?php

namespace Padam87\AccountBundle\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\UnitOfWork;
use Padam87\AccountBundle\Entity\Transaction;

class TransactionListener implements EventSubscriber
{
    /**
     * {@inheritdoc}
     */
    public function getSubscribedEvents()
    {
        return [
            'onFlush' => 'onFlush'
        ];
    }

    public function onFlush(OnFlushEventArgs $args)
    {
        $em = $args->getEntityManager();
        $uow = $em->getUnitOfWork();

        foreach ($uow->getScheduledEntityInsertions() as $entity) {
            if ($entity instanceof Transaction) {
                $this->updateBalance($entity, $em, $uow);
            }
        }

        foreach ($uow->getScheduledEntityUpdates() as $entity) {
            if ($entity instanceof Transaction) {
                throw new \LogicException('A transaction should never be updated. Please create a new transaction.');
            }
        }

        foreach ($uow->getScheduledEntityDeletions() as $entity) {
            if ($entity instanceof Transaction) {
                throw new \LogicException('A transaction should never be deleted. Please create a new transaction.');
            }
        }
    }

    private function updateBalance(Transaction $transaction, EntityManager $em, UnitOfWork $uow)
    {
        if (null === $account = $transaction->getAccount()) {
            throw new \LogicException('A transaction must have an account specified.');
        }

        $money = $account->getBalance()->add($transaction->getAmount());
        $account->setBalance($money);

        $uow->persist($account);
        $uow->computeChangeSet($em->getClassMetadata(get_class($account)), $account);
    }
}
