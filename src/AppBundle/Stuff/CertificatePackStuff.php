<?php
/**
 * Created by PhpStorm.
 * User: Роман
 * Date: 14.11.2016
 * Time: 15:34
 */

namespace AppBundle\Stuff;

use AppBundle\Entity\CertificatePack;
use AppBundle\Entity\PaymentMethod;
use AppBundle\Entity\Sertificate;
use Doctrine\ORM\EntityManager;

class CertificatePackStuff
{
    /**
     * @var EntityManager
     */
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function createCertificatePack($current_user, $cert_ids_in_pack, $payment_method_id){
        /** @var  $certificatePack CertificatePack*/
        $certificatePack = new CertificatePack();
        /** @var  $payment_method PaymentMethod*/
        $payment_method = $this->em->getRepository("AppBundle:PaymentMethod")->find($payment_method_id);
        $certificatePack->
            setIDUser($current_user)->
            setIDPaymentMethod($payment_method)->
            setCount(count($cert_ids_in_pack))->
            setCreationDate(new \DateTime());
        $this->em->persist($certificatePack);
        foreach($cert_ids_in_pack AS $cert_id_in_pack){
            /** @var  $certificate_in_pack Sertificate*/
            $certificate_in_pack = $this->em->getRepository("AppBundle:Sertificate")->find($cert_id_in_pack);
            $certificate_in_pack->setIDCertificatePack($certificatePack);
            $this->em->persist($certificate_in_pack);
        }
        $this->em->flush();
    }

}