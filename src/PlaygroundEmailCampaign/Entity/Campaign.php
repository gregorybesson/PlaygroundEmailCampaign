<?php

namespace PlaygroundEmailCampaign\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\Factory;
use Doctrine\ORM\Mapping\UniqueConstraint;

/**
 * @ORM\Entity @HasLifecycleCallbacks
 * @ORM\Table(name="emailcampaign_campaign")
 */
class Campaign implements InputFilterAwareInterface
{
    protected $inputFilter;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string")
     */
    protected $name;

    /**
     * @ORM\Column(type="string")
     */
    protected $description;

    /**
     * @ORM\Column(type="string")
     */
    protected $fromName;

    /**
     * @ORM\Column(type="string")
     */
    protected $fromEmail;

    /**
     * @ORM\Column(type="string")
     */
    protected $subject;

    /**
     * @ORM\ManyToOne(targetEntity="Template", inversedBy="campaigns", cascade={"persist"})
     * @ORM\JoinColumn(name="template_id", referencedColumnName="id")
     */
    protected $template;

    /**
     * @ORM\ManyToOne(targetEntity="Mailinglist", inversedBy="campaigns", cascade={"persist"})
     * @ORM\JoinColumn(name="mailing_list_id", referencedColumnName="id")
     */
    protected $mailingList;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $isTracked;

    /**
     * @ORM\Column(type="string")
     */
    protected $trackingURL;

    /**
     * @ORM\Column(type="string")
     */
    protected $unsubscribeURL;

    public function getInputFilter()
    {
        return $this->inputFilter;
    }

    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        $this->inputFilter = $inputFilter;
        return $this;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    public function getFromName()
    {
        return $this->fromName;
    }

    public function setFromName($fromName)
    {
        $this->fromName = $fromName;
        return $this;
    }

    public function getFromEmail()
    {
        return $this->fromEmail;
    }

    public function setFromEmail($fromEmail)
    {
        $this->fromEmail = $fromEmail;
        return $this;
    }

    public function getSubject()
    {
        return $this->subject;
    }

    public function setSubject($subject)
    {
        $this->subject = $subject;
        return $this;
    }

    public function getTemplate()
    {
        return $this->template;
    }

    public function setTemplate($template)
    {
        $this->template = $template;
        return $this;
    }

    public function getMailingList()
    {
        return $this->mailingList;
    }

    public function setMailingList($mailingList)
    {
        $this->mailingList = $mailingList;
        return $this;
    }

    public function getIsTracked()
    {
        return $this->isTracked;
    }

    public function setIsTracked($isTracked)
    {
        $this->isTracked = $isTracked;
        return $this;
    }

    public function getTrackingURL()
    {
        return $this->trackingURL;
    }

    public function setTrackingURL($trackingURL)
    {
        $this->trackingURL = $trackingURL;
        return $this;
    }

    public function getUnsubscribeURL()
    {
        return $this->unsubscribeURL;
    }

    public function setUnsubscribeURL($unsubscribeURL)
    {
        $this->unsubscribeURL = $unsubscribeURL;
        return $this;
    }
}