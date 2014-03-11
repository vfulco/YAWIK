<?php
/**
 * YAWIK
 *
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   GPLv3
 */

namespace Jobs\Entity;

use Core\Entity\AbstractIdentifiableModificationDateAwareEntity as BaseEntity;
use Core\Entity\EntityInterface;
use Core\Entity\RelationEntity;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Core\Repository\DoctrineMongoODM\Annotation as Cam;
use Doctrine\Common\Collections\Collection;
use Auth\Entity\UserInterface;
use Core\Entity\PreUpdateAwareInterface;
use Core\Entity\Permissions;
use Core\Entity\PermissionsInterface;

/**
 * The job model
 *
 * @ODM\Document(collection="jobs", repositoryClass="Jobs\Repository\Job")
 */
class Job extends BaseEntity implements JobInterface {

    /**
     * uniq ID of a job posting.
     *
     * @var String
     * 
     * @ODM\String @ODM\Index
     **/
    protected $applyId;
    
    /**
     * title of a job posting
     * 
     * @var String 
     *  
     * @ODM\String 
     */ 
    protected $title;
    
    /**
     * Description (Free text)
     * @var String
     * @ODM\String
     */
    protected $description;
    /**
     * name of the publishing company
     * 
     * @var String
     * 
     * @ODM\String
     */
    protected $company;
    
    /**
     * Email Adress, which is used to send notifications about e.g. new applications.
     * 
     * @ODM\String
     **/
    protected $contactEmail;
    
    /**
     * the owner of a Job Posting
     *  
     * @var unknown
     * @ODM\ReferenceOne(targetDocument="\Auth\Entity\User", simple=true) @ODM\Index
     */
    protected $user;
    
    /**
     * all applications of a certain jobad 
     * 
     * @var array \Applications\Entity\Application
     * 
     * @ODM\ReferenceMany(targetDocument="Applications\Entity\Application", simple=true, mappedBy="job")
     */
    protected $applications;
    
    /**
     * new applications
     * 
     * @ODM\ReferenceMany(targetDocument="Applications\Entity\Application", 
     *                    repositoryMethod="getUnreadApplications", mappedBy="job") 
     * @var unknown
     */
    protected $unreadApplications;
    
    /**
     * location of the job posting
     * 
     * @var unknown
     * 
     * @ODM\String
     */
    protected $location;
    
    /**
     * place of employment 
     * 
     * @var String
     * 
     * @ODM\String
     **/
    protected $link;
    
    /**
     * publishing date of a job posting
     * 
     * @var String
     * 
     * @ODM\Field(type="tz_date")
     */
    protected $datePublishStart;
    
    /**
     * Status of the job posting
     * 
     * @var unknown
     * 
     * @ODM\String
     */
    protected $status;
    
    /**
     * Reference of a jobad, on which an applicant can refer to.
     * 
     * @var String
     * 
     * @ODM\String 
     */
    protected $reference;
    
    /**
     * Unified Resource Locator to the company-Logo
     * 
     * @var String
     * 
     * @ODM\String 
     */
    protected $logoRef;
    
    /**
     * this must be enabled to use applications forms etc. for this job or
     * to see number of applications in the list of applications
     * 
     * @var Boolean
     * 
     * @ODM\Boolean 
     */
    protected $camEnabled;
    
    /**
     * stores a list of lowercase keywords. This array can be regenerated by 
     *   bin/cam jobs generatekeywords
     * 
     * @ODM\Collection
     */
    protected $keywords;
    
    
    /**
     * Permissions
     * 
     * @var PermissionsInterface
     * @ODM\EmbedOne(targetDocument="\Core\Entity\Permissions")
     */
    protected $permissions;
    
    public function getResourceId()
    {
        return 'Entity/Jobs/Job';
    }
    
    public function setApplyId($applyId) {
        $this->applyId = (string) $applyId;
        return $this;
    }

    public function getApplyId() {
        return $this->applyId;
    }

    /**
     * @return the $title
     */
    public function getTitle() {
        return $this->title;
    }

    /**
     * @param field_type $title
     */
    public function setTitle($title) {
        $this->title = (string) $title;
        return $this;
    }

    /**
     * {@inheritDoc}
     * @see \Jobs\Entity\JobInterface::setDescription()
     */
    public function setDescription($text)
    {
        $this->description = (string) $text;
        return $this;
    }
    
    /**
     * {@inheritDoc}
     * @see \Jobs\Entity\JobInterface::setDescription()
     */
    public function getDescription()
    {
        return $this->description;
    }
    
    /**
     * @return the $company
     */
    public function getCompany() {
        return $this->company;
    }

    /**
     * @param field_type $company
     */
    public function setCompany($company) 
    {
        $this->company = $company;
        return $this;
    }
    
    public function getContactEmail() 
    {
        if (false !== $this->contactEmail && !$this->contactEmail) {
            $user = $this->getUser();
            $email = False;
            if (isset($user)) {
                $email = $user->getInfo()->getEmail();
            }
            $this->setContactEmail($email);
        }
        return $this->contactEmail;
    }
    
    public function setContactEmail($email)
    {
        $this->contactEmail = (string) $email;
        return $this;
    }
    
    public function setLocation($location)
    {
    	$this->location = $location;
    	return $this;
    }
    
    public function getLocation()
    {
    	return $this->location;
    }
    
    public function setUser(UserInterface $user) {
        if ($this->user) {
            $this->getPermissions()->revokeFrom($this->user, Permissions::PERMISSION_ALL);
        }
        $this->user = $user;
        $this->getPermissions()->grantTo($user, Permissions::PERMISSION_ALL);
        return $this;
    }

    public function getUser() {
        return $this->user;
    }

    public function setApplications(Collection $applications) {
        $this->applications = $applications;
        return $this;
    }

    public function getApplications() {
        return $this->applications;
    }
    
    public function getUnreadApplications() {
        return $this->unreadApplications;
    }

    public function getLink() {
        return $this->link;
    }

    public function setLink($link) {
        $this->link = $link;
        return $this;
    }
    
    public function getDatePublishStart() {
        return $this->datePublishStart;
    }

    public function setDatePublishStart($datePublishStart) {
        $this->datePublishStart = $datePublishStart;
        return $this;
    }
    
    public function getStatus() {
        return $this->status;
    }

    public function setStatus($status) {
        $this->status = $status;
        return $this;
    }
    
    public function getReference() {
        return $this->reference;
    }

    public function setReference($reference) {
        $this->reference = $reference;
        return $this;
    }
    
    
    public function getCamEnabled() {
        return $this->camEnabled;
    }

    public function setCamEnabled($camEnabled) {
        $this->camEnabled = $camEnabled;
        return $this;
    }
    
    public function getLogoRef() {
        return $this->logoRef;
    }

    public function setLogoRef($logoRef) {
        $this->logoRef = $logoRef;
        return $this;
    }
    
    /**
     * @return array Names of attributes, which can be searched by keywords
     */
    public function getSearchableProperties()
    {
        return array('title', 'company', 'location', 'applyId', 'reference');
    }
    
    public function setKeywords(array $keywords)
    {
        $this->keywords = $keywords;
        return $this;
    }
    
    public function getKeywords()
    {
        return $this->keywords;
    }
    
    public function clearKeywords()
    {
        $this->keywords = array();
        return $this;
    }
    
    public function getPermissions()
    {
        if (!$this->permissions) {
            $permissions = new Permissions();
            if ($this->user) {
                $permissions->grantTo($this->user, Permissions::PERMISSION_ALL);
            }
            $this->setPermissions($permissions);
        }
        return $this->permissions;
    }
    
    public function setPermissions(PermissionsInterface $permissions) {
        $this->permissions = $permissions;
        return $this;
    }
}