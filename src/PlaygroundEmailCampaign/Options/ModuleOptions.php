<?php

namespace PlaygroundEmailCampaign\Options;

use Zend\Stdlib\AbstractOptions;


class ModuleOptions extends AbstractOptions
{
    /**
     * web mail service name
     */
    protected $service = 'MailChimp';

    /**
     * web mail user API Key
     */
    protected $userKey = '999f451057b5f5fbfb3bda744d9ffaf6-us3';

    /**
     * web mail API url
     */
    protected $queryURL = 'https://<dc>.api.mailchimp.com/2.0/';

    public function getService()
    {
        return $this->service;
    }

    public function setService($service)
    {
        $this->service = $service;
        return $this;
    }

    public function getUserKey()
    {
        return $this->userKey;
    }

    public function setUserKey($userKey)
    {
        $this->userKey = $userKey;
        return $this;
    }

    public function getQueryURL()
    {
        return $this->queryURL;
    }

    public function setQueryURL($queryURL)
    {
        $this->queryURL = $queryURL;
        return $this;
    }
}
