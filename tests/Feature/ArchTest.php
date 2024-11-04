<?php

namespace Tests\Feature;

test('globals')
    ->expect(['dd', 'dump', 'ray', 'ds'])
    ->not->toBeUsed();
