<?php

namespace App\Entity;

enum Visibility
{
    case VISIBILITY_PUBLIC;
    case VISIBILITY_PRIVATE;
    case VISIBILITY_SUBSCRIBERS_ONLY;
}
