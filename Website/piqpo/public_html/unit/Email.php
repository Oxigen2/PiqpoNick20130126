<?php

require_once(getenv("DOCUMENT_ROOT")."/inc_piqpo.php");

$emailManager = new EmailManager;

$res = $emailManager->send( 'nick@nickaltmann.net', 'admin@piqpo.com', 'Piqpo password reset', 'hello' );

if ( $res )
{
    echo "Success";
}
else
{
    echo "Failure";
}

?>
