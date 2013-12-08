<?php
/**
 * Created by JetBrains PhpStorm.
 * User: marie
 * Date: 01/12/13
 * Time: 15:21
 * To change this template use File | Settings | File Templates.
 */

namespace RoleVoters\BlogBundle\Securtiy;


use RoleVoters\BlogBundle\Entity\Comment;
use RoleVoters\BlogBundle\Entity\Post;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\RoleVoter;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

class ObjectVoter extends RoleVoter
{
    private $supportedRoles;

    public function __construct($supportedRoles)
    {
        $this->supportedRoles = $supportedRoles;
    }

    public function supportsAttribute($attribute)
    {
        return in_array($attribute, $this->supportedRoles);
    }

    public function vote(TokenInterface $token, $object, array $attributes)
    {
        $result = VoterInterface::ACCESS_ABSTAIN;

        if (!$object instanceof Post && !$object instanceof Comment) {
            return $result;
        }

        foreach ($attributes as $attribute) {

            if (!$this->supportsAttribute($attribute)) {
                continue;
            }

            $result = VoterInterface::ACCESS_DENIED;

            if ($object->getUser() === $token->getUser() || in_array('ROLE_ADMIN', $token->getRoles())) {
                return VoterInterface::ACCESS_GRANTED;
            }
        }

        return $result;
    }
}