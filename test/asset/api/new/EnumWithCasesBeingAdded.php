<?php

declare(strict_types=1);

namespace RoaveTestAsset;

enum EnumWithCasesBeingAdded
{
    // two new months below:
    case January;
    case February;

    case March;
    case April;
    case May;
    case June;
    case July;
    case August;
    case September;
    case October;
    case November;
    case December;

    /**
     * For testing now but for use only in a future major version. As it should not be statically referenced from
     * outside this library, and the library will not pass a reference to it to the outside at runtime, adding it
     * is not a BC break.
     * @internal
     */
    case Sol;
}
