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
 * @ORM\Table(
 *              name="emailcampaign_template",
 *              uniqueConstraints={@UniqueConstraint(name="title", columns={"title"})}
 *           )
 */
class Template implements InputFilterAwareInterface
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
    protected $title;


    /**
     * @ORM\Column(type="string")
     */
    protected $htmlStructure;

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    public function getHtmlStructure()
    {
        return $this->htmlStructure;
    }

    public function setHtmlStructure($htmlStructure)
    {
        $this->htmlStructure = $htmlStructure;
        return $this;
    }

    public function populate($data= array())
    {
        if (isset($data['title']) && $data['title'] != null) {
            $this->title = $data['title'];
        }
        if (isset($data['htmlStructure']) && $data['htmlStructure'] != null) {
            $this->htmlStructure = $data['htmlStructure'];
        }
    }

    public function getInputFilter()
    {
        if (!$this->inputFilter) {
            $inputFilter = new InputFilter();
            $factory = new Factory();

            $inputFilter->add($factory->createInput(array('name' => 'id', 'required' => true, 'filters' => array(array('name' => 'Int'),),)));

            $inputFilter->add($factory->createInput(array(
                'name' => 'title',
                'required' => true,
                'validators' => array(
                    array('name' => 'NotEmpty',),
                ),
            )));

            $inputFilter->add($factory->createInput(array(
                'name' => 'htmlStructure',
                'required' => true,
                'validators' => array(
                    array('name' => 'NotEmpty',),
                ),
            )));
        }
        return $this->inputFilter;
    }

    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new \Exception("Not used");
    }

}