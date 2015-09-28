<?php

namespace API\UserBundle\Entity;

use Doctrine\Common\Persistence\ObjectManager;
use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class User
 * @package API\UserBundle\Entity
 */
class User  extends BaseUser implements EquatableInterface, \Serializable, UserInterface
{
    /**
     * @var string
     */
    protected $firstName;

    /**
     * @var string
     */
    protected $lastName;

    /**
     * @var boolean
     */
    protected $isActive;

    /**
     * @var string
     */
    protected $logo;

    /**
     * @var \DateTime
     */
    protected $createAt;

    /**
     * @var \DateTime
     */
    protected $updateAt;

    /**
     * @var \DateTime
     */
    protected $lastActivity;

    /** @var array */
    protected $roles = array('ROLE_USER');

    private $file;
    private $temp;

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Sets file.
     *
     * @param UploadedFile $file
     */
    public function setLogo(UploadedFile $file = null)
    {

        $this->file = $file;

        // check if we have an old image path
        if (is_file($this->getAbsolutePath())) {
            // store the old name to delete after the update
            $this->temp = $this->getAbsolutePath();
        } else {
            $this->logo = 'initial';
        }
    }

    /**
     * Get file.
     *
     * @return UploadedFile
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * Set firstName
     *
     * @param string $firstName
     * @return User
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * Get firstName
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set lastName
     *
     * @param string $lastName
     * @return User
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * Get lastName
     *
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Set isActive
     *
     * @param boolean $isActive
     * @return User
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * Get isActive
     *
     * @return boolean
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     * Get logo
     *
     * @return string
     */
    public function getLogo()
    {
        return $this->logo ? $this->getWebPath().$this->logo : null;
    }

    /**
     * Set createAt
     *
     * @param \DateTime $createAt
     * @return User
     */
    public function setCreateAt($createAt)
    {
        $this->createAt = $createAt;

        return $this;
    }

    /**
     * Get createAt
     *
     * @return \DateTime
     */
    public function getCreateAt()
    {
        return $this->createAt;
    }

    /**
     * Set updateAt
     *
     * @param \DateTime $updateAt
     * @return User
     */
    public function setUpdateAt($updateAt)
    {
        $this->updateAt = $updateAt;

        return $this;
    }

    /**
     * Get updateAt
     *
     * @return \DateTime
     */
    public function getUpdateAt()
    {
        return $this->updateAt;
    }

    /**
     * Set lastActivity
     *
     * @param \DateTime $lastActivity
     * @return User
     */
    public function setLastActivity($lastActivity)
    {
        $this->lastActivity = $lastActivity;

        return $this;
    }

    /**
     * Get lastActivity
     *
     * @return \DateTime
     */
    public function getLastActivity()
    {
        return $this->lastActivity;
    }

    /**
     * @see \Serializable::serialize()
     */
    public function serialize()
    {
        return serialize(array($this->id,));
    }

    /**
     * @see \Serializable::unserialize()
     */

    public function unserialize($serialized)
    {
        list ($this->id) = unserialize($serialized);
    }

    /**
     * @param User|UserInterface $user
     * @return bool
     */
    public function isEqualTo(UserInterface $user)
    {
        return ((int)$this->getId() === $user->getId());
    }

    public function __sleep()
    {
        return array('id');
    }


    public function createNewAccount(
        PasswordEncoderInterface $encoder,
        ObjectManager $em,
        $email,
        $password,
        array $data = array()
    ) {
        //  set default values
        foreach ($data as $field => $val) {
            $method = 'set'.ucfirst($field);
            if (method_exists($this, $method)) {
                $this->$method($val);
            }
        }

        $password = $encoder->encodePassword($password, $this->getSalt());
        $this->setPassword($password);
        $this->setEmail($email);

        $this->setCreateAt(new \DateTime());
        $this->setLastActivity(new \DateTime());
        $this->setUpdateAt(new \DateTime());
        $this->setIsActive(true);

        $em->persist($this);
        $em->flush();
    }

    public function getAbsolutePath()
    {
        return null === $this->logo ? null : $this->getUploadRootDir().'/'.$this->logo;
    }

    public function getWebPath()
    {
        return null === $this->logo ? null : $this->getUploadDir().'/';
        //.$this->logo;
    }

    public function getUploadRootDir()
    {
        // the absolute directory path where uploaded
        // documents should be saved
        return __DIR__.'/../../../../web/'.$this->getUploadDir();
    }

    public function getUploadDir()
    {
        // get rid of the __DIR__ so it doesn't screw up
        // when displaying uploaded doc/image in the view.
        return 'uploads/user_pics';
    }

    public function preUpload()
    {
        $fileName = $this->getId()."_".md5($this->getEmail()).".";

        if (null != $this->getFile()) {
            $this->logo = $fileName.strtolower($this->getFile()->getClientOriginalExtension());
        }

        return $this->logo;
    }

    public function upload()
    {
        if (null === $this->getFile()) {
            return;
        }

        // check if we have an old image
        if (isset($this->temp)) {
            // delete the old image
            unlink($this->temp);
            // clear the temp image path
            $this->temp = null;
        }

        // you must throw an exception here if the file cannot be moved
        // so that the entity is not persisted to the database
        // which the UploadedFile move() method does
        $this->getFile()->move(
            $this->getUploadRootDir(),
//            $this->getFile()->getClientOriginalName()
            $this->preUpload()
        );

        $this->setLogo(null);
    }

    public function storeFilenameForRemove()
    {
        $this->temp = $this->getAbsolutePath();
    }

    public function removeUpload()
    {
        if (isset($this->temp)) {
            unlink($this->temp);
        }
    }

    public function isNew()
    {
        return $this->getId()?false:true;
    }
}
