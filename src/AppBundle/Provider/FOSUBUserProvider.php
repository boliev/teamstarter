<?php

namespace AppBundle\Provider;

use AppBundle\Exception\LoginDataProviderNoDataException;
use FOS\UserBundle\Model\UserManagerInterface;
use FOS\UserBundle\Util\TokenGeneratorInterface;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use HWI\Bundle\OAuthBundle\Security\Core\User\FOSUBUserProvider as BaseClass;
use Symfony\Component\Security\Core\User\UserInterface;

class FOSUBUserProvider extends BaseClass
{
    /**
     * @var TokenGeneratorInterface
     */
    private $tokenGenerator;

    /**
     * FOSUBUserProvider constructor.
     *
     * @param UserManagerInterface    $userManager
     * @param TokenGeneratorInterface $tokenGenerator
     * @param array                   $properties
     */
    public function __construct(UserManagerInterface $userManager, TokenGeneratorInterface $tokenGenerator, array $properties)
    {
        parent::__construct($userManager, $properties);
        $this->tokenGenerator = $tokenGenerator;
    }

    /**
     * {@inheritdoc}
     */
    public function connect(UserInterface $user, UserResponseInterface $response)
    {
        $property = $this->getProperty($response);
        $username = $response->getUsername();
        //on connect - get the access token and the user ID
        $service = $response->getResourceOwner()->getName();
        $setter = 'set'.ucfirst($service);
        $setter_id = $setter.'Id';
        $setter_token = $setter.'AccessToken';
        //we "disconnect" previously connected users
        if (null !== $previousUser = $this->userManager->findUserBy(array($property => $username))) {
            $previousUser->$setter_id(null);
            $previousUser->$setter_token(null);
            $this->userManager->updateUser($previousUser);
        }
        //we connect current user
        $user->$setter_id($username);
        $user->$setter_token($response->getAccessToken());
        $this->userManager->updateUser($user);
    }

    /**
     * {@inheritdoc}
     */
    public function loadUserByOAuthUserResponse(UserResponseInterface $response)
    {
        $service = $response->getResourceOwner()->getName();
        $username = $response->getEmail();
        $data = $response->getData();
        $profilePicture = $data['picture']['data']['url'] ?? null;
        if (!isset($data['id'])) {
            throw new LoginDataProviderNoDataException('There is no id in response data from '.$service);
        }
        $id = $data['id'];
        $user = null;
        if ('facebook' === $service) {
            $user = $this->userManager->findUserBy(['facebookId' => $id]);
        }

        if (null === $user) {
            $setter = 'set'.ucfirst($service);
            $setter_id = $setter.'Id';
            $setter_token = $setter.'AccessToken';

            $user = $this->userManager->createUser();
            $user->$setter_id($id);
            $user->$setter_token($response->getAccessToken());

            $user->setUsername($username);
            $user->setEmail($username);
            $user->setFirstName($response->getFirstName());
            $user->setLastName($response->getLastName());
            $user->setProfilePicture($profilePicture);
            $user->setPlainPassword($this->tokenGenerator->generateToken());
            $user->setEnabled(true);
            $this->userManager->updateUser($user);

            return $user;
        }
        //if user exists - go with the HWIOAuth way
//        $user = parent::loadUserByOAuthUserResponse($response);
        $serviceName = $response->getResourceOwner()->getName();
        $setter = 'set'.ucfirst($serviceName).'AccessToken';
        //update access token
        $user->$setter($response->getAccessToken());

        return $user;
    }
}
