<?php

require_once 'ESys/ArrayAccessor.php';

$address = $this->getRequired('address');
$html = $this->getOptional('html', false);

$address = new ESys_ArrayAccessor($address);
$address = $address->get(array(
    'street1',
    'street2',
    'city',
    'state',
    'zip',
    'country',
));

if ($html) {
    array_walk($address, create_function('&$v', '$v = esc_html($v);'));
}


$newline = $html ? "<br>\n" : "\n";


$line1 = $address['street1'];
$line1 .= empty($line1) ? '' : $newline;
echo $line1;
if (! empty($address['street2'])) {
    echo $address['street2'].$newline;
}
$line3 = $address['city'];
$line3 .= empty($line3) ? '' : ', ';
$line3 .= $address['state'];
$line3 .= empty($line3) ? '' : ' ';
$line3 .= $address['zip'];
$line3 .= empty($line3) ? '' : $newline;
echo $line3;
echo $address['country'];
