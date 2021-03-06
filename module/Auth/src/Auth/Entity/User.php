<?php
/**
 * YAWIK
 *
 * @copyright (c) 2013-2015 Cross Solution (http://cross-solution.de)
 * @license       MIT
 */

namespace Auth\Entity;

use Core\Entity\AbstractIdentifiableEntity;
use Core\Entity\Collection\ArrayCollection;
use Core\Entity\DraftableEntityInterface;
use Doctrine\Common\Collections\Collection;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Organizations\Entity\OrganizationReferenceInterface;
use Settings\Repository\SettingsEntityResolver;

/**
 * Defines an user model
 *
 * @ODM\Document(collection="users", repositoryClass="Auth\Repository\User")
 */
class User extends AbstractIdentifiableEntity implements UserInterface, DraftableEntityInterface
{

    /**
     * defines the role of a recruiter
     */
    const ROLE_RECRUITER = 'recruiter';
    /*
     * defines the role of an authenticated user
     */
    const ROLE_USER = 'user';
    /*
     * defines the role of an admin user.
     */
    const ROLE_ADMIN = 'admin';

    /**
     * Users login name
     *
     * @var string
     * @ODM\String @ODM\Index
     */
    protected $login;

    /**
     * Role of an user. Currently "user" or "recruiter"
     *
     * @ODM\String*/
    protected $role;

    /**
     * Users contact data.
     *
     * @ODM\EmbedOne(targetDocument="Info")
     */
    protected $info;

    /**
     * Authentification Sessions like oAuth
     * After Authentification with OAuth sessions can be stored like a password/key pair
     *
     * @ODM\EmbedMany(targetDocument="AuthSession")
     */
    protected $authSessions;

    /**
     * Users login password
     *
     * @ODM\String
     */
    protected $credential;

    /**
     * Users primary email address
     *
     * @ODM\String
     */
    protected $email;

    /**
     * pre-shared key, which allows an external application to authenticate
     *
     * @var String
     * @ODM\String
     */
    protected $secret;

    /**
     * Can contain various HybridAuth profiles.
     *
     * @var array
     * @ODM\Hash
     */
    protected $profile = array();

    /** @var array
     * @ODM\EmbedMany(discriminatorField="_entity")
     */
    protected $settings;

    /**
     * This is not a persistent property!
     *
     * @var SettingsEntityResolver
     */
    protected $settingsEntityResolver;

    /**
     * User groups.
     *
     * @var Collection
     * @ODM\ReferenceMany(targetDocument="Group", mappedBy="owner", simple=true, cascade="all")
     */
    protected $groups;

    /**
     * User tokens. Is generated when recovering Passwords as a short term key.
     *
     * @var Collection
     * @ODM\EmbedMany(targetDocument="Token")
     */
    protected $tokens;

    /**
     * The organization reference for the user.
     *
     * This field is not stored in the database, but injected on postLoad via
     * {@link \Organizations\Repository\Event\InjectOrganizationReferenceListener}
     *
     * @var OrganizationReferenceInterface
     *
     * @since 0.18
     */
    protected $organization;

    /**
     * Is this entity a draft or not?
     *
     * @var bool
     * @ODM\Boolean
     */
    protected $isDraft = false;

    /**
     * @see http://docs.doctrine-project.org/projects/doctrine-mongodb-odm/en/latest/reference/best-practices.html
     * It is recommended best practice to initialize any business collections in documents in the constructor.
     * {mg: What about lazy loading? Initialize the Collection in the getter, if none is set? Reduce overload.}
     */
    public function __construct()
    {
    }

    /**
     * @return bool
     */
    public function isDraft()
    {
        return $this->isDraft;
    }

    /**
     * @param bool $flag
     * @return $this
     */
    public function setIsDraft($flag)
    {
        $this->isDraft = (bool) $flag;

        return $this;
    }


    /** {@inheritdoc} */
    public function setLogin($login)
    {
        $this->login = trim((String)$login);
        return $this;
    }

    /** {@inheritdoc} */
    public function getLogin()
    {
        return $this->login;
    }

    /**
     * {@inheritdoc}
     */
    public function setRole($role)
    {
        $this->role = $role;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getRole()
    {
        if (!$this->role) {
            $this->setRole('user');
        }
        return $this->role;
    }

    /**
     * {@inheritdoc}
     */
    public function getRoleId()
    {
        return $this->getRole();
    }

    /**
     * {@inheritdoc}
     */
    public function setInfo(InfoInterface $info)
    {
        $this->info = $info;
        return $this;
    }

    /** {@inheritdoc} */
    public function getInfo()
    {
        if (null == $this->info) {
            $this->setInfo(new Info());
        }
        return $this->info;
    }

    /**
     * @param $key
     * @param $sessionParameter
     * @return $this
     */
    public function updateAuthSession($key, $sessionParameter) {
        $notExists = True;

        foreach ($this->authSessions as $authSession) {
            /* @var $authSession AuthSession */
            if ($key == $authSession->getName()) {
                $authSession->setSession($sessionParameter);
                $notExists = False;
            }
        }
        if ($notExists) {
            $authSession = new AuthSession();
            $authSession->setName($key);
            $authSession->setSession($sessionParameter);
            $this->authSessions[] = $authSession;
        }
        return $this;
    }

    /**
     * @param $key
     * @return null
     */
    public function getAuthSession($key) {
        $result = Null;

        foreach ($this->authSessions as $authSession) {
            /* @var $authSession AuthSession */
            if ($key == $authSession->getName()) {
                $result = $authSession->getSession();
            }
        }
        return $result;
    }

    /**
     * removes a stored Session
     * @param string|null $key providerName, if null, remove all sessions
     * @return $this
     */
    public function removeSessionData($key = Null) {
        $authSessionRefresh = array();
        foreach ($this->authSessions as $authSession) {
            /* @var $authSession AuthSession */
            if (isset($key) && $key != $authSession->getName()) {
                $authSessionRefresh[] = $authSession;
            }
        }
        $this->authSessions = $authSessionRefresh;
        return $this;
    }

    /** {@inheritdoc} */
    public function getCredential()
    {
        return $this->credential;
    }

    /** {@inheritdoc} */
    public function setPassword($password)
    {
        $filter = new Filter\CredentialFilter();
        $credential = $filter->filter($password);
        return $this->setCredential($credential);
    }

    /** {@inheritdoc} */
    public function setCredential($credential)
    {
        $this->credential = $credential;
        return $this;
    }

    /** {@inheritdoc} */
    public function getSecret()
    {
        if (isset($this->secret)) {
            return $this->secret;
        }
        return $this->credential;
    }

    /** {@inheritdoc} */
    public function setSecret($secret)
    {
        $this->secret = $secret;
        return $this;
    }

    /** {@inheritdoc} */
    public function getEmail()
    {
        return $this->email ?: $this->getInfo()->getEmail();
    }

    /** {@inheritdoc} */
    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }

    /** {@inheritdoc} */
    public function setProfile(array $profile)
    {
        $this->profile = $profile;
        return $this;
    }

    /** {@inheritdoc} */
    public function getProfile()
    {
        return $this->profile;
    }

    /** {@inheritdoc} */
    public function setSettingsEntityResolver($resolver)
    {
        $this->settingsEntityResolver = $resolver;
    }

    /** {@inheritdoc} */
    public function getSettings($module)
    {
        if (!isset($module)) {
            throw new \InvalidArgumentException('$module must not be null.');
        }

        if (!$this->settings) {
            $this->settings = new ArrayCollection();
        }

        foreach ($this->settings as $settings) {
            if ($settings->moduleName == $module) {
                return $settings;
            }
        }

        $settings = $this->settingsEntityResolver->getNewSettingsEntity($module);
        $this->settings->add($settings);
        return $settings;
    }

    /** {@inheritdoc} */
    public function getGroups()
    {
        if (!$this->groups) {
            $this->groups = new ArrayCollection();
        }
        return $this->groups;
    }

    /** {@inheritdoc} */
    public function getGroup($name, $create = false)
    {
        $groups = $this->getGroups();
        foreach ($groups as $group) {
            /* @var $group GroupInterface */
            if ($group->getName() == $name) {
                return $group;
            }
        }
        if ($create) {
            $group = new Group($name, $this);
            $groups->add($group);
            return $group;
        }
        return null;
    }

    /**
     * @return Collection
     */
    public function getTokens()
    {
        if (!$this->tokens) {
            $this->tokens = new ArrayCollection();
        }

        return $this->tokens;
    }

    /**
     * @param Collection $tokens
     */
    public function setTokens($tokens)
    {
        $this->tokens = $tokens;
    }

    /**
     * @param OrganizationReferenceInterface $organization
     * @return $this
     */
    public function setOrganization(OrganizationReferenceInterface $organization)
    {
        $this->organization = $organization;

        return $this;
    }

    /**
     * @return bool
     */
    public function hasOrganization()
    {
        /* @var $this->organization \Organizations\Entity\OrganizationReference */
        return $this->organization &&
               $this->organization->hasAssociation();
    }

    /**
     * @return OrganizationReferenceInterface
     */
    public function getOrganization()
    {
        return $this->organization;
    }
}