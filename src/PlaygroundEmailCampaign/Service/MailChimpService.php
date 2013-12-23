<?php

namespace PlaygroundEmailCampaign\Service;

use ZfcBase\EventManager\EventProvider;
use Zend\ServiceManager\ServiceManager;
use Zend\ServiceManager\ServiceManagerAwareInterface;

use PlaygroundEmailCampaign\Options\ModuleOptions;
use Assetic\Exception\Exception;
use Doctrine\Tests\Common\Annotations\Fixtures\Annotation\Template;

class MailChimpService extends EventProvider implements ServiceManagerAwareInterface
{
    /**
     * @var ModuleOptions
     */
    protected $options;

    /**
     * @var ServiceManager
     */
    protected $serviceManager;

    /**
     * @var \Mailchimp
     */
    protected $mc;

    /**
     * @var string
     * Main Mailchimp list id used to create segments to send email campaign
     * from playground admin
     */
    protected $mainListId;

    public function __construct($key)
    {
        try {
            $this->mc = new \Mailchimp($key);
        } catch (\Mailchimp_Error $e) {
            throw new \Exception('No API key provided');
        }
        $lists = $this->getLists();
        if ($lists) {
            $mainListData = end($lists['data']);
            $this->setMainListId($mainListData['id']);
        }
    }

    /**** GENERAL ****/
    public function ping()
    {
        try {
            $this->mc->helper->ping();
            return true;
        } catch (\Mailchimp_Invalid_ApiKey $e) {
            return false;
        } catch (\Mailchimp_Error $e) {
            return false;
        }
    }

    /**** TEMPLATES ****/
    public function addTemplate($template)
    {
        try {
            $result = $this->mc->templates->add($template->getTitle(), $template->getHtmlContent());
            if ($result) {
                return (int) $result['template_id'];
            }
        } catch (\Mailchimp_Error $e) {
            return false;
        }
        return false;
    }

    public function updateTemplate($template)
    {
        try {
            return $this->mc->templates->update($template->getDistantId(), array(
                'name' => $template->getTitle(),
                'html' => $template->getHtmlContent(),
            ));
        } catch (\Mailchimp_Error $e) {
            return false;
        }
    }

    public function getTemplateDataFromId($id)
    {
        try {
            return $this->mc->templates->info($id);
        } catch (\Invalid_Template $e) {
            return false;
        } catch (\Mailchimp_Error $e) {
            return false;
        }
    }

    public function getSourceTemplate($template)
    {
        $result = $this->getTemplateDataFromId($template->getDistantId());
        return ($result) ? $result['source'] : null;
    }

    public function getPreviewTemplate($template)
    {
        $result = $this->getTemplateDataFromId($template->getDistantId());
        return ($result) ? $result['preview'] : null;
    }

    public function deleteTemplate($template)
    {
        try{
            return $this->mc->templates->del($template->getDistantId());
        } catch (\Mailchimp_Error $e) {
            return false;
        }
    }

    public function listCustomTemplates()
    {
        return $this->listTemplates(true, false, false);
    }

    public function listDefaultTemplates()
    {
        return $this->listTemplates(false, true, false);
    }

    public function listTemplates($user, $gallery, $base)
    {
        $templates = array();
        try{
            $results = $this->mc->templates->getList(array('user'=>$user, 'gallery'=>$gallery, 'base'=>$base));

            foreach ($results as $type) {
                foreach ($type as $template) {
                    $data = $this->getTemplateDataFromId($template['id']);
                    if ($data) {
                        $templates[] = array(
                            'distantId'=>$template['id'],
                            'title' =>$template['name'],
                            'htmlContent'=> $data['source'],
                            'preview' => $template['preview_image']
                        );
                    }
                }
            }
            return $templates;
        } catch (\Mailchimp_Error $e) {
            return false;
        }
    }

    /**** LISTS ****/
    public function getLists()
    {
        try{
            $lists = $this->mc->lists->getList();
            $mainListData = end($lists['data']);
            $this->setMainListId($mainListData['id']);
            return $lists;
        } catch (\Mailchimp_Error $e) {
            return false;
        }
    }

    public function listLists()
    {
        try {
            $segments = $this->mc->lists->staticSegments($this->mainListId);
            return $segments;
        } catch (\Mailchimp_Error $e) {
            return false;
        }
    }

    public function createList($list)
    {
        try {
            $segment = $this->mc->lists->staticSegmentAdd($this->getMainListId(), $list->getName());
            return ($segment) ? $segment['id'] : $segment;
        } catch (\Mailchimp_Error $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function subscribeList($segment, $contact, $sendEmail)
    {
        try {
            if ($contact->getDistantId()) {
                $batch = array('euid' => $contact->getDistantid());
            } else {
                $batch = array('email' => $contact->getUser()->getEmail());
            }
            $contact = $this->mc->lists->subscribe($this->getMainListId(), $batch, array(), 'html', false, false, true, $sendEmail);
            $this->mc->lists->staticSegmentMembersAdd($this->getMainListId(), $segment->getDistantId(), array($batch));
            return ($contact) ? $contact['euid'] : $contact;
        } catch (\Mailchimp_Error $e) {
            return false;
        }
    }

    public function unsubscribeList($segment, $contact, $clearSubscription)
    {
        try {
            $batch = array('euid' => $contact->getDistantId());
            $this->mc->lists->staticSegmentMembersDel($this->getMainListId(), $segment->getDistantId(), array($batch));
            $this->mc->lists->unsubscribe($this->getMainListId(), $batch, $clearSubscription);
            return true;
        } catch (\Mailchimp_Error $e) {
            return false;
        }
    }

    public function deleteList($segment)
    {
        try {
            return $this->mc->lists->staticSegmentDel($this->getMainListId(), $segment->getDistantId());
        } catch (\Mailchimp_Error $e) {
            return false;
        }
    }

    public function getServiceManager()
    {
        return $this->serviceManager;
    }

    public function setServiceManager(ServiceManager $serviceManager)
    {
        $this->serviceManager = $serviceManager;
        return $this;
    }

    public function getOptions()
    {
        if (!$this->options instanceof ModuleOptions) {
            $this->setOptions($this->getServiceManager()->get('playgroundemailcampaign_module_options'));
        }
        return $this->options;
    }

    public function setOptions(ModuleOptions $options)
    {
        $this->options = $options;
        return $this;
    }

    public function getMc()
    {
        return $this->mc;
    }

    public function setMc(\Mailchimp $mc)
    {
        $this->mc = $mc;
        return $this;
    }

    public function getMainListId()
    {
        return $this->mainListId;
    }

    public function setMainListId($mainListId)
    {
        $this->mainListId = $mainListId;
        return $this;
    }
}