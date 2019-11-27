<?php

namespace Tests\Unit;

use App\Enums\UserPermission;
use App\Enums\WebsiteAccessType;
use Tests\TestCase;

class WebsiteAccessTypeTest extends TestCase
{
    public function testWebsiteAccessTypeFromUserPermission(): void
    {
        $this->assertEquals(WebsiteAccessType::NO_ACCESS, WebsiteAccessType::fromUserPermission(UserPermission::NO_ACCESS));
        $this->assertEquals(WebsiteAccessType::VIEW, WebsiteAccessType::fromUserPermission(UserPermission::READ_ANALYTICS));
        $this->assertEquals(WebsiteAccessType::WRITE, WebsiteAccessType::fromUserPermission(UserPermission::MANAGE_ANALYTICS));
    }
}
