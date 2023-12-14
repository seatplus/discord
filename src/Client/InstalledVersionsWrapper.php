<?php

namespace Seatplus\Discord\Client;

use Composer\InstalledVersions;

class InstalledVersionsWrapper
{
    public function getPrettyVersion(string $packageName): ?string
    {
        return InstalledVersions::getPrettyVersion($packageName);
    }
}
