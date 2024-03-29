<?php

namespace User\Service;

use Application\Entity\Role;
use Application\Entity\User;
use DateTime;
use Doctrine\ORM\EntityManager;
use Zend\Crypt\Password\Bcrypt;
use Zend\Math\Rand;

/**
 * This service is responsible for adding/editing users
 * and changing user password.
 */
class UserManager {

    /**
     * Doctrine entity manager.
     * @var EntityManager
     */
    private $entityManager;

    /**
     * Constructs the service.
     */
    public function __construct($entityManager) {
        $this->entityManager = $entityManager;
    }

    /**
     * This method adds a new user.
     */
    public function addUser($data) {
        // Do not allow several users with the same email address.
        if ($this->checkUserExists($data['email'])) {
            throw new \Exception("User with email address " . $data['$email'] . " already exists");
        }

        // Create new User entity.
        $user = new User();
        $user->setEmail($data['email']);
        $user->setUsername($data['username']);
        $user->setFullName($data['full_name']);

        // Encrypt password and store the password in encrypted state.
        $bcrypt = new Bcrypt();
        $passwordHash = $bcrypt->create($data['password']);
        $user->setPassword($passwordHash);

        $user->setStatus($data['status']);
        $user->setSales_attr_id($data['salesAttrId']);

        $currentDate = new DateTime();
        $user->setDateCreated($currentDate);

        //email, username, full_name, password, status, dateCreated, phone1, salesAttrId, AND
        //salespersonname <-- this is really deprecated, it was only needed when there was a distinction 
        //between the user and their default salesperson (which was quite redundant and unneeded and sloppy!)
        $user->setSalespersonname($data['full_name']);
        $user->setPhone1($data['phone1']);

        $roles = $this->entityManager->getRepository(Role::class)
                ->findBy(['name' => $data['role']]);

        $user->setRoles($roles);


        // Add the entity to the entity manager.
        $this->entityManager->persist($user);

        // Apply changes to database.
        $this->entityManager->flush();

        return $user;
    }

    /**
     * This method updates data of an existing user.
     */
    public function updateUser($user, $data) {
        // Do not allow to change user email if another user with such email already exits.
        if ($user->getEmail() != $data['email'] && $this->checkUserExists($data['email'])) {
            throw new \Exception("Another user with email address " . $data['email'] . " already exists");
        }

        // Do not allow to change user username if another user with such username already exits.
        if ($user->getUsername() != $data['username'] && $this->checkUserExistsUsername($data['username'])) {
            throw new \Exception("Another user with username " . $data['username'] . " already exists");
        }

        $user->setEmail($data['email']);
        $user->setUsername($data['username']);
        $user->setFullName($data['full_name']);
        $user->setStatus(($data['status'] == '1' || $data['status'] == 1 || $data['status'] == TRUE) ? User::STATUS_ACTIVE : User::STATUS_INACTIVE);


        //not every user has a value for salespersonname or sales_attr_id
        if ($data['salesAttrId']) {
            //only set when user is a salesperson - proven by existence of ales_attr_id value.
            $user->setSalespersonname($data['full_name']);
            $user->setSales_attr_id($data['salesAttrId']);
        }

        //only change password when there is a value passed for password
        //otherwise leave it alone.
        if (!empty($data['password'])) {
            // Encrypt password and store the password in encrypted state.
            $bcrypt = new Bcrypt();
            $passwordHash = $bcrypt->create($data['password']);
            $user->setPassword($passwordHash);
        }

        $user->setPhone1($data['phone1']);

        if ($data['role']) {

            $role = $this->entityManager->getRepository(Role::class)
                    ->findOneByName($data['role']);

            if (!empty($role)) {
                $user->setRoles([$role]);
            }
        }

        // Apply changes to database.
        $this->entityManager->flush();

        return true;
    }

    /**
     * Checks whether an active user with given email address already exists in the database.     
     */
    public function checkUserExists($email) {

        $user = $this->entityManager->getRepository(User::class)
                ->findOneByEmail($email);

        return $user !== null;
    }

    /**
     * Checks whether an active user with given email address already exists in the database.     
     */
    public function checkUserExistsUsername($username) {

        $user = $this->entityManager->getRepository(User::class)
                ->findOneByUsername($username);

        return $user !== null;
    }

    /**
     * Checks that the given password is correct.
     */
    public function validatePassword($user, $password) {
        $bcrypt = new Bcrypt();
        $passwordHash = $user->getPassword();

        if ($bcrypt->verify($password, $passwordHash)) {
            return true;
        }

        return false;
    }

    /**
     * Generates a password reset token for the user. This token is then stored in database and 
     * sent to the user's E-mail address. When the user clicks the link in E-mail message, he is 
     * directed to the Set Password page.
     */
    public function generatePasswordResetToken($user) {

        // Generate a token.
        $token = Rand::getString(32, '0123456789abcdefghijklmnopqrstuvwxyz', true);
        $user->setPasswordResetToken($token);

        $currentDate = date('Y-m-d H:i:s');
        $user->setPasswordResetTokenCreationDate($currentDate);

        $this->entityManager->flush();

        $subject = 'Password Reset';

        $httpHost = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost';
        $passwordResetUrl = 'http://' . $httpHost . '/set-password?token=' . $token;

        $body = 'Please follow the link below to reset your password:\n';
        $body .= "$passwordResetUrl\n";
        $body .= "If you haven't asked to reset your password, please ignore this message.\n";

        // Send email to user.
        mail($user->getEmail(), $subject, $body);
    }

    /**
     * Checks whether the given password reset token is a valid one.
     */
    public function validatePasswordResetToken($passwordResetToken) {
        $user = $this->entityManager->getRepository(User::class)
                ->findOneByPasswordResetToken($passwordResetToken);

        if ($user == null) {
            return false;
        }

        $tokenCreationDate = $user->getPasswordResetTokenCreationDate();
        $tokenCreationDate = strtotime($tokenCreationDate);

        $currentDate = strtotime('now');

        if ($currentDate - $tokenCreationDate > 24 * 60 * 60) {
            return false; // expired
        }

        return true;
    }

    /**
     * This method sets new password by password reset token.
     */
    public function setNewPasswordByToken($passwordResetToken, $newPassword) {
        if (!$this->validatePasswordResetToken($passwordResetToken)) {
            return false;
        }

        $user = $this->entityManager->getRepository(User::class)
                ->findOneBy(['passwordResetToken' => $passwordResetToken]);

        if ($user === null) {
            return false;
        }

        // Set new password for user        
        $bcrypt = new Bcrypt();
        $passwordHash = $bcrypt->create($newPassword);
        $user->setPassword($passwordHash);

        // Remove password reset token
        $user->setPasswordResetToken(null);
        $user->setPasswordResetTokenCreationDate(null);

        $this->entityManager->flush();

        return true;
    }

    /**
     * This method is used to change the password for the given user. To change the password,
     * one must know the old password.
     */
    public function changePassword($adminuser, $user, $data) {
        $adminPassword = $data['admin_password'];

        // Check that old password is correct
        if (!$this->validatePassword($adminuser, $adminPassword)) {
            return false;
        }

        $newPassword = $data['new_password'];

        // Check password length
        if (strlen($newPassword) < 6 || strlen($newPassword) > 64) {
            return false;
        }

        // Set new password for user        
        $bcrypt = new Bcrypt();
        $passwordHash = $bcrypt->create($newPassword);
        $user->setPassword($passwordHash);

        // Apply changes
        $this->entityManager->flush();

        return true;
    }

}
