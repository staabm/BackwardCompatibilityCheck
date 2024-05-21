<?php

declare(strict_types=1);

namespace RoaveTestAsset;

enum EnumWithCasesBeingChanged
{
    case March;
    case April;
    case May;
    case June;
    case July;
    case August;
    case September;
    case October;
    case November;
    Case december; // oops forget this is spelled with a capital D

    /**
     * @internal - may be removed without notice
     */
    case FakeMonth;
}
