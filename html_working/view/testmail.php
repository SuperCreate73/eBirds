<?
$to = olivier.inge@gmail.com;
$email_subject = "Test email";
$email_body = "You have received a new message. ";
$headers = "From: info@ebirds.be";
$headers .= " Reply-To: info@ebirds.be";
mail($to,$email_subject,$email_body,$headers);
