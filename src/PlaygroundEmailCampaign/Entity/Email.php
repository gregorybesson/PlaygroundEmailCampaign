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
 * @ORM\Table(name="emailcampaign_email")
 */
class Email implements InputFilterAwareInterface
{
    protected $inputFilter;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Campaign", inversedBy="emails", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="campaign_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $campaign;

    /**
     * @ORM\ManyToOne(targetEntity="Contact", inversedBy="emails", cascade={"persist"})
     * @ORM\JoinColumn(name="to_contact_id", referencedColumnName="id")
     */
    protected $to;

    // state in queued, delivered, bounce, blocked
    /**
     * @ORM\Column(type="string")
     */
    protected $state;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $clicked = false;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $opened = false;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $reportedAsSpam = false;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $unsuscribed = false;

    public function getArrayCopy()
    {
        return get_object_vars($this);
    }

    public function populate($data = array())
    {
        if (isset($data['campaign']) && $data['campaign'] != null) {
            $this->campaign = $data['campaign'];
        }
        if (isset($data['to']) && $data['to'] != null) {
            $this->to = $data['to'];
        }
        if (isset($data['state']) && $data['state'] != null) {
            $this->state = $data['state'];
        }
        if (isset($data['clicked']) && $data['clicked'] != null) {
            $this->clicked = $data['clicked'];
        }
        if (isset($data['opened']) && $data['opened'] != null) {
            $this->opened = $data['opened'];
        }
        if (isset($data['reportedAsSpam']) && $data['reportedAsSpam'] != null) {
            $this->reportedAsSpam = $data['reportedAsSpam'];
        }
        if (isset($data['unsuscribed']) && $data['unsuscribed'] != null) {
            $this->unsuscribed = $data['unsuscribed'];
        }
    }

    public function getInputFilter()
    {
        if (!$this->inputFilter) {
            $inputFilter = new InputFilter();
            $factory = new Factory();

            $inputFilter->add($factory->createInput(array('name' => 'id', 'required' => true, 'filters' => array(array('name' => 'Int'),),)));

            $inputFilter->add($factory->createInput(array(
                'name' => 'campaign',
                'required' => true,
            )));

            $inputFilter->add($factory->createInput(array(
                'name' => 'state',
                'required' => true,
                'validators' => array(
                    array('name' => 'StringLength', 'options' => array('min'=>1, 'max' => 255)),
                    array(
                        'name' => 'InArray',
                        'options' => array(
                            'haystack' => array('queued', 'delivered', 'bounce', 'blocked'),
                        ),
                    ),
                ),
            )));

            $this->inputFilter = $inputFilter;
        }
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

    public function getCampaign()
    {
        return $this->campaign;
    }

    public function setCampaign($campaign)
    {
        $this->campaign = $campaign;
        return $this;
    }

    public function getTo()
    {
        return $this->to;
    }

    public function setTo($to)
    {
        $this->to = $to;
        return $this;
    }

    public function getState()
    {
        return $this->state;
    }

    public function setState($state)
    {
        $this->state = $state;
        return $this;
    }

    public function getClicked()
    {
        return $this->clicked;
    }

    public function setClicked($clicked)
    {
        $this->clicked = $clicked;
        return $this;
    }

    public function getOpened()
    {
        return $this->opened;
    }

    public function setOpened($opened)
    {
        $this->opened = $opened;
        return $this;
    }

    public function getReportedAsSpam()
    {
        return $this->reportedAsSpam;
    }

    public function setReportedAsSpam($reportedAsSpam) {
        $this->reportedAsSpam = $reportedAsSpam;
        return $this;
    }

    public function getUnsuscribed()
    {
        return $this->unsuscribed;
    }

    public function setUnsuscribed($unsuscribed)
    {
        $this->unsuscribed = $unsuscribed;
        return $this;
    }
}