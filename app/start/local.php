<?php

App::singleton('validator', function()
    {
        return CustomValidator::Instance();
    });