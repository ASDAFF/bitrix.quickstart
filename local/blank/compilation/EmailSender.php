<?php

class EmailSender
{
    function checkOutParams($data)
    {
        if(!empty($data) && !isset($data))
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    function sendEmail($to, $subject, $message )
    {
        if(mail($to, $subject, $message))
        {
            echo "OK";
        }
        else
        {
            echo "ERROR";
        }
    }

    function setSubject($data)
    {
        return trim(htmlspecialchars($data));
    }

    function setTo()
    {
        $email = 'sestrinskiy@mail.ru';//shoolonline@mail.ru
        return trim(htmlspecialchars($email));
    }

    function setMessage($data)
    {
        $str = '';
        foreach ($data as $key=>$data)
        {
            $str .= $key . ':  ' .$data. ' ' . PHP_EOL;
        }
        return trim(htmlspecialchars($str));
    }

}

function pr($data)
{
    echo  '<pre>';
    print_r( $data );
    echo  '</pre>';
}



$objEmail = new EmailSender();
if( $_POST['formSerailize'] || $_POST['hashLink'] )
{
    $data = array();
    $formSerailize  = parse_str($_POST['formSerailize'], $outputForm);
    $hashLink       = parse_str($_POST['hashLink'], $outputHash);

    foreach ($outputForm as $name=>$value)
    {
        if( $name=='name' && strlen($value)>0)
            $data['Имя'] = $value;
        else
            exit("Name need to be filled");


        if( $name=='phone' && strlen($value)>0)
            $data['Телефон'] = $value;
        else
            exit("Phone need to be filled");


        if( $name=='title' && strlen($value)>0)
            $data['Форма'] = $value;
        else
            exit("Title need to be filled");

    }

    foreach ($outputHash as $name=>$value)
        $data[$name] = $value;


    $result = $objEmail->sendEmail( $objEmail->setTo(),
                                    $objEmail->setSubject('Заявка - Школа Успеха'),
                                    $objEmail->setMessage($data) );
}

