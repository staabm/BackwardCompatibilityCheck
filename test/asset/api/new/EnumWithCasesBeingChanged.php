<?php

declare(strict_types=1);

namespace RoaveTestAsset;

enum EnumWithCasesBeingChanged
{
    // two new months below:
    case January;
    case February;

    /** @internal  - tired from the two new Months, reserving March for private use from now on.*/
    case March;

    case April;
    case May;
    case June;
    case July;

    // We're on holiday in August, not going to allow that month any more.
    // case August;

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
