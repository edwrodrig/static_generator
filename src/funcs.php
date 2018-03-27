<?php

namespace edwrodrig\static_generator;

function tr($translatable) :string {
    return Site::get()->tr($translatable);
}