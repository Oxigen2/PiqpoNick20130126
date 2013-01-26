<?php

class APIErrorCode
{
    const success = 1;
    const failure = 2;
    const processingException = 10;
    const userNotAuthenticated = 101;
    const actionNotAuthorised = 102;
    const accountAlreadyExists = 103;
    const requestInvalid = 201;
    const illegalCommand = 202;
    const missingArguments = 203;
    const invalidStream = 301;
}

?>
