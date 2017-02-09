<?php
namespace Tests\App\Entity;

use Doctrine\ORM\EntityRepository;

class InvoiceRepository extends EntityRepository
{
    /**
     * @param Invoice $invoice
     */
    public function insert(Invoice $invoice)
    {
        $this->_em->persist($invoice);
        $this->_em->flush($invoice);
    }
}