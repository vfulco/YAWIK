<?php
/**
 * YAWIK
 * 
 * @filesource
 * @copyright (c) 2013-2015 Cross Solution (http://cross-solution.de)
 * @license   MIT
 * @author    weitz@cross-solution.de
 */


namespace Jobs\Filter;

use Jobs\Entity\Job;
use Zend\Filter\FilterInterface;
use Zend\View\Model\ViewModel;

/**
 * assembles a viewModel for Templates
 * this class needs to be extended for specific assignments
 * Class viewModelTemplateFilterAbstract
 * @package Jobs\Filter
 */
abstract class viewModelTemplateFilterAbstract implements FilterInterface
{

    /**
     * @var array assemples all data for the viewmodel
     */
    protected $container;

    /**
     * @var Job
     */
    protected $job;

    /**
     * @var array
     */
    protected $config;

    /**
     * creating absolute links like the apply-link
     * absolute links are needed on the server of the provider
     * @var
     */
    protected $urlPlugin;

    /**
     * also needed to create absolute links
     * @var
     */
    protected $basePathHelper;

    /**
     * @var
     */
    protected $serverUrlHelper;

    /**
     * @param $config
     */
    public function setConfig($config)
    {
        $this->config = $config;
        return;
    }

    /**
     * @param $urlPlugin
     */
    public function setUrlPlugin($urlPlugin)
    {
        $this->urlPlugin = $urlPlugin;
        return;
    }

    /**
     * @param $basePathHelper
     */
    public function setBasePathHelper($basePathHelper)
    {
        $this->basePathHelper = $basePathHelper;
        return;
    }

    /**
     * @return mixed
     */
    public function getBasePathHelper()
    {
        return $this->basePathHelper;
    }

    /**
     * @param $serverUrlHelper
     */
    public function setServerUrlHelper($serverUrlHelper)
    {
        $this->serverUrlHelper = $serverUrlHelper;
        return;
    }

    /**
     * @return mixed
     */
    public function getServerUrlHelper()
    {
        return $this->serverUrlHelper;
    }

    /**
     * @param mixed $value
     * @return mixed|ViewModel
     * @throws \InvalidArgumentException
     */
    public function filter($value)
    {
        $model    = new ViewModel();
        $this->container = array();
        $this->extract($value);
        $model->setVariables($this->container);
        if (!isset($this->job)) {
            throw new \InvalidArgumentException('cannot create a viewModel for Templates without an $job');
        }
        $model->setTemplate('templates/' . $this->job->template . '/index');
        return $model;
    }

    /**
     * should be overwritten
     * here are all assignments to container arr administered
     * input-attributes are the job and the configuration
     * output-attribute is the container
     * @param $value
     * @return mixed
     */
    abstract protected function extract ($value);

    /**
     * @return $this
     * @throws \InvalidArgumentException
     */
    protected function setUriApply() {
        if (!isset($this->job)) {
            throw new \InvalidArgumentException('cannot create a viewModel for Templates without an $job');
        }
        $atsMode = $this->job->getAtsMode();
        $uriApply = false;
        if ($atsMode->isIntern() || $atsMode->isEmail()) {
            $uriApply = $this->urlPlugin->fromRoute('lang/apply', array('applyId' => $this->job->applyId), array('force_canonical' => True));
        } else if ($atsMode->isUri()) {
            $uriApply = $atsMode->getUri();
        }
        $this->container['uriApply'] = $uriApply;
        return $this;
    }

    /**
     * @return $this
     * @throws \InvalidArgumentException
     */
    protected function setLocation() {
        if (!isset($this->job)) {
            throw new \InvalidArgumentException('cannot create a viewModel for Templates without an $job');
        }
        $location = $this->job->getLocation();
        $this->container['location'] = isset($location)?$location:'';
        return $this;
    }

    /**
     * @return $this
     * @throws \InvalidArgumentException
     */
    protected function setDescription() {
        if (!isset($this->job)) {
            throw new \InvalidArgumentException('cannot create a viewModel for Templates without an $job');
        }
        $description = $this->job->templateValues->description;
        $this->container['description'] = isset($description)?$description:'';
        return $this;
    }

    /**
     * @return $this
     * @throws \InvalidArgumentException
     */
    protected function setOrganizationInfo() {
        if (!isset($this->job)) {
            throw new \InvalidArgumentException('cannot create a viewModel for Templates without an $job');
        }
        $organizationName = '';
        $organizationStreet = '';
        $organizationPostalCode = '';
        $organizationPostalCity = '';
        $organization = $this->job->organization;
        if (isset($organization)) {
            $organizationName = $organization->organizationName->name;
            $organizationStreet = $organization->contact->street.' '.$organization->contact->houseNumber;
            $organizationPostalCode = $organization->contact->postalcode;
            $organizationPostalCity = $organization->contact->city;
        }
        $this->container['organizationName'] = $organizationName;
        $this->container['street'] = $organizationStreet;
        $this->container['postalCode'] = $organizationPostalCode;
        $this->container['city'] = $organizationPostalCity;

        if (isset($organization) && isset($organization->image) && $organization->image->uri) {
            $this->container['uriLogo'] = $this->makeAbsolutePath($organization->image->uri);
        } else {
            $this->container['uriLogo'] = $this->makeAbsolutePath($this->config->default_logo);
        }
        return $this;
    }

    /**
     * @return $this
     */
    protected function setTemplate() {
        $this->container['templateName'] = $this->job->template;
        return $this;
    }

    /**
     * combines two helper
     * @param $path
     * @return mixed
     */
    protected function makeAbsolutePath($path) {
        $path = $this->serverUrlHelper->__invoke($this->basePathHelper->__invoke($path));
        return $path;
    }
}
