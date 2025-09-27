<?php

// The tests should not catch this comment by the Keywords BUG, FIX, SECURITY, TEST or TODO.

// BUG this is a bug.
// BUG @john-doe this is a bug.
// FIX this is to fix.
// FIX @john-doe this is to fix.
// SECURITY this is for security.
// SECURITY @john-doe this is for security.
// TEST this is to test.
// TEST @john-doe this is to test.
function lineExample() {
    echo "Line example 1"; // TODO this is a todo.
    echo "Line example 2"; // TODO @john-doe this is a todo.
}
