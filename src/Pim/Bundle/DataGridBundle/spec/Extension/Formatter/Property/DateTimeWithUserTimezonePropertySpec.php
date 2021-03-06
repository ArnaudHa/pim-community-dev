<?php

declare(strict_types=1);

namespace spec\Pim\Bundle\DataGridBundle\Extension\Formatter\Property;

use Akeneo\Component\Localization\Presenter\PresenterInterface;
use Oro\Bundle\DataGridBundle\Datasource\ResultRecord;
use Oro\Bundle\DataGridBundle\Extension\Formatter\Property\PropertyConfiguration;
use PhpSpec\ObjectBehavior;
use Pim\Bundle\UserBundle\Context\UserContext;
use Symfony\Component\Translation\TranslatorInterface;

class DateTimeWithUserTimezonePropertySpec extends ObjectBehavior
{
    function let(
        TranslatorInterface $translator,
        PresenterInterface $presenter,
        UserContext $userContext
    ) {
        $this->beConstructedWith($translator, $presenter, $userContext);

        $this->init(PropertyConfiguration::create([
            'name'          => 'a_date',
            'label'         => 'A date',
            'type'          => 'datetime_with_user_timezone',
            'frontend_type' => 'datetime',
        ]));
    }

    function it_formats_a_datetime_with_user_timezone(
        $translator,
        $userContext,
        $presenter
    ) {
        $datetime = new \DateTime('2018-03-20T18:13');

        $translator->getLocale()->willReturn('en_GB');
        $userContext->getUserTimezone()->willReturn('Pacific/Kiritimati');
        $presenter->present(
            $datetime,
            [
                'locale'   => 'en_GB',
                'timezone' => 'Pacific/Kiritimati'
            ]
        )->shouldBeCalled();

        $this->getValue(new ResultRecord(['a_date' => $datetime]));
    }
}
