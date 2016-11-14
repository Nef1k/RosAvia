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

    /**
     * @var CertificateStuff
     */
    private $certificate_stuff;

    /**
     * CertificatePackStuff constructor.
     * @param EntityManager $em
     * @param CertificateStuff $certificateStuff
     */
    public function __construct(EntityManager $em, CertificateStuff $certificateStuff)
    {
        $this->em = $em;
        $this->certificate_stuff = $certificateStuff;
    }

    /**
     * @param $current_user
     * @param $cert_ids_in_pack
     * @param $payment_method_id
     */
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

    public function createActionWithCertificatePack($certificate_pack_id, $is_certs_activated){
        /** @var  $certificates_in_pack Sertificate[]*/
        $certificates_in_pack = $this->em->getRepository("AppBundle:Sertificate")->findBy(array("ID_CertificatePack" => $certificate_pack_id));
        /** @var  $certificate_in_pack Sertificate*/
        foreach($certificates_in_pack AS $certificate_in_pack){
            $certificate_in_pack->setIDCertificatePack();
            $this->em->persist($certificate_in_pack);
            if ($is_certs_activated) {
                $this->certificate_stuff->activateCertificates($certificates_in_pack);
            }
        }
        $certificate_pack = $this->em->getRepository("AppBundle:CertificatePack")->find($certificate_pack_id);
        $this->em->remove($certificate_pack);
        $this->em->flush();
    }

}