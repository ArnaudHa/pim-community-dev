<?php

namespace spec\Pim\Bundle\NotificationBundle\Twig;

use Oro\Bundle\UserBundle\Entity\User;
use PhpSpec\ObjectBehavior;
use Pim\Bundle\NotificationBundle\Entity\Repository\UserNotificationRepository;
use Pim\Bundle\UserBundle\Context\UserContext;
use Prophecy\Argument;

class NotificationExtensionSpec extends ObjectBehavior
{
    function let(UserNotificationRepository $repository, UserContext $context)
    {
        $this->beConstructedWith($repository, $context);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Pim\Bundle\NotificationBundle\Twig\NotificationExtension');
    }

    function it_provides_the_unread_notification_count(User $user, $context, $repository)
    {
        $context->getUser()->willReturn($user);
        $repository->countUnreadForUser($user)->willReturn(3);

        $this->countNotifications()->shouldReturn(3);
    }

    function it_returns_zero_if_no_user_is_present_in_the_context($repository)
    {
        $repository->countUnreadForUser(Argument::cetera())->shouldNotBeCalled();

        $this->countNotifications()->shouldReturn(0);
    }

    function it_has_a_name()
    {
        $this->getName()->shouldBe('pim_notification_extension');
    }
}
