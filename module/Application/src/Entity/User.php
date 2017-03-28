<?php

namespace Application\Entity;

use Application\Entity\Customer;
use Application\Entity\Preferences;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;


/**
 * This class represents a registered user.
 * @ORM\Entity()
 * @ORM\Table(name="users")
 */
class User 
{
    // User status constants.
    const STATUS_ACTIVE       = 1; // Active user.
    const STATUS_INACTIVE      = 0; // Retired user.
    
    /**
     * @ORM\Id
     * @ORM\Column(name="id")
     * @ORM\GeneratedValue
     */
    protected $id;

    /** 
     * @ORM\Column(name="email")  
     */
    protected $email;
    
    /** 
     * @ORM\Column(name="username")  
     */
    protected $username;
    
    /** 
     * @ORM\Column(name="full_name")  
     */
    protected $fullName;

    /** 
     * @ORM\Column(name="password")  
     */
    protected $password;

    /** 
     * @ORM\Column(name="status")  
     */
    protected $status;
    
    /**
     * @ORM\Column(name="date_created", type="datetime")  
     */
    protected $dateCreated;
        
    /**
     * @ORM\Column(name="pwd_reset_token")  
     */
    protected $passwordResetToken;
    
    /**
     * @ORM\Column(name="pwd_reset_token_creation_date", type="datetime", nullable=true)  
     */
    protected $passwordResetTokenCreationDate;
    
    /**
     * @var ArrayCollection|Role[]
     * @ORM\ManyToMany(targetEntity="Role", inversedBy="users")
     * @ORM\JoinTable(name="user_role",
     *      joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="role_id", referencedColumnName="id", unique=true)}
     *      )
     * @see http://docs.doctrine-project.org/projects/doctrine-orm/en/latest/reference/association-mapping.html (5.6. One-To-Many, Unidirectional with Join Table)
     */
    protected $roles;
    
    /**
     * @var ArrayCollection|Customer[]
     * @ORM\ManyToMany(targetEntity="Customer", inversedBy="users")
     * @ORM\JoinTable(name="user_customer",
     *      joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="customer_id", referencedColumnName="id", unique=true)}
     *      )
     */
    protected $customers;
    
    /**
     * @var ArrayCollection|Preferences[]
     * @ORM\OneToMany(targetEntity="Preferences", mappedBy="user")
     * 
     */
    protected $preferences;
    
    /**
     * @ORM\Column(name="last_login", type="datetime", nullable=true)
     */
    protected $lastlogin;
    
    /**
     * @ORM\Column(type="integer")
     */
    protected $sales_attr_id;
    
    /**
     * @ORM\Column(type="string")
     */
    protected $salespersonname;
    
    /**
     * @ORM\Column(type="string")
     */
    protected $phone1;
    
    public function __construct() {
        $this->customers = new ArrayCollection();
        $this->roles = new ArrayCollection();
    }
    
    public function setLastlogin($lastlogin) {
        $this->lastlogin = $lastlogin;
    }
    
    public function getLastlogin() {
        return $this->lastlogin;
    }
    
    public function getSalespersonname() {
        return $this->salespersonname;
    }

    public function getPhone1() {
        return $this->phone1;
    }

    public function setSalespersonname($salespersonname) {
        $this->salespersonname = $salespersonname;
    }

    public function setPhone1($phone1) {
        $this->phone1 = $phone1;
    }
    
    public function getPreferences() {
        return $this->preferences;
    }

    public function setPreferences($preferences) {
        $this->preferences = $preferences;
        return $this;
    }

    /**
     * Returns user ID.
     * @return integer
     */
    public function getId() 
    {
        return $this->id;
    }
    
    public function getSales_attr_id() {
        return $this->sales_attr_id;
    }

    public function setSales_attr_id($sales_attr_id) {
        $this->sales_attr_id = $sales_attr_id;
    }

    /**
     * 
     * @return [Role]
     */
    public function getRoles() 
    {
        return $this->roles;
    }
    
    /**
     * 
     * @return [Customer]
     */
    public function getCustomers() 
    {
        return $this->customers;
    }
    
    /**
     * Sets roles. 
     * @param array $roles    
     */
    public function setRoles($roles) 
    {
        $this->roles = $roles;
    }
    
    /**
     * Sets customers. 
     * @param array $customers    
     */
    public function setCustomers($customers) 
    {
        $this->customers = $customers;
    }

    /**
     * Sets user ID. 
     * @param int $id    
     */
    public function setId($id) 
    {
        $this->id = $id;
    }

    /**
     * Returns email.     
     * @return string
     */
    public function getEmail() 
    {
        return $this->email;
    }

    /**
     * Sets email.     
     * @param string $email
     */
    public function setEmail($email) 
    {
        $this->email = $email;
    }
    
    /**
     * Returns username.     
     * @return string
     */
    public function getUsername() 
    {
        return $this->username;
    }

    /**
     * Sets username.     
     * @param string $username
     */
    public function setUsername($username) 
    {
        $this->username = $username;
    }
    
    /**
     * Returns full name.
     * @return string     
     */
    public function getFullName() 
    {
        return $this->fullName;
    }       

    /**
     * Sets full name.
     * @param string $fullName
     */
    public function setFullName($fullName) 
    {
        $this->fullName = $fullName;
    }
    
    /**
     * Returns status.
     * @return int     
     */
    public function getStatus() 
    {
        return $this->status;
    }

    /**
     * Returns possible statuses as array.
     * @return array
     */
    public static function getStatusList() 
    {
        return [
            self::STATUS_ACTIVE => 'Active',
            self::STATUS_INACTIVE => 'Inactive'
        ];
    }    
    
    /**
     * Returns user status as string.
     * @return string
     */
    public function getStatusAsString()
    {
        $list = self::getStatusList();
        if (isset($list[$this->status]))
            return $list[$this->status];
        
        return 'Unknown';
    }    
    
    /**
     * Sets status.
     * @param int $status     
     */
    public function setStatus($status) 
    {
        $this->status = $status;
    }   
    
    /**
     * Returns password.
     * @return string
     */
    public function getPassword() 
    {
       return $this->password; 
    }
    
    /**
     * Sets password.     
     * @param string $password
     */
    public function setPassword($password) 
    {
        $this->password = $password;
    }
    
    /**
     * Returns the date of user creation.
     * @return string     
     */
    public function getDateCreated() 
    {
        return $this->dateCreated;
    }
    
    /**
     * Sets the date when this user was created.
     * @param string $dateCreated     
     */
    public function setDateCreated($dateCreated) 
    {
        $this->dateCreated = $dateCreated;
    }    
    
    /**
     * Returns password reset token.
     * @return string
     */
    public function getResetPasswordToken()
    {
        return $this->passwordResetToken;
    }
    
    /**
     * Sets password reset token.
     * @param string $token
     */
    public function setPasswordResetToken($token) 
    {
        $this->passwordResetToken = $token;
    }
    
    /**
     * Returns password reset token's creation date.
     * @return string
     */
    public function getPasswordResetTokenCreationDate()
    {
        return $this->passwordResetTokenCreationDate;
    }
    
    /**
     * Sets password reset token's creation date.
     * @param string $date
     */
    public function setPasswordResetTokenCreationDate($date) 
    {
        $this->passwordResetTokenCreationDate = $date;
    }
}



