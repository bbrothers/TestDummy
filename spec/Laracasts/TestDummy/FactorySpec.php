<?php

namespace spec\Laracasts\TestDummy;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class FactorySpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Laracasts\TestDummy\Factory');
    }

    function it_adds_an_exclusion_rule()
    {
        $this->addExclusionFilter('\.md$');
        $this->getExclusionFilters()->shouldBe(['__MACOSX', '^\.', '\.md$']);
    }

    function it_overrides_existing_exclusion_rules()
    {
        $this->setExclusionFilters('\.md$');
        $this->getExclusionFilters()->shouldBe(['\.md$']);
    }

    function it_should_not_allow_invalid_rule_types()
    {
        $invalid = new \stdClass();
        $this->shouldThrow('\InvalidArgumentException')->duringSetExclusionFilters($invalid);
    }
}
