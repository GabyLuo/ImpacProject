<?php

require (__DIR__ . '/../vendor/swiftmailer/swiftmailer/lib/swift_required.php');

if (!defined('ENVIRONMENT')) {
    define('ENVIRONMENT', 'development');
}

if (!defined('EMAILTEMPLATES')) {
    define('EMAILTEMPLATES', __DIR__ . '/../lib/templates/');
}

class Mailer
{
    public $header;
    public $footer;

    public $from;
    public $to;
    public $tousers = array();
    public $Cc;
    public $Bcc;
    public $template;
    public $type;
    public $subject;
    public $archivo;
    public $misc = array();

    function __construct() {
        $this->header = EMAILTEMPLATES . "header.xml";
        $this->footer = EMAILTEMPLATES . "footer.xml";
    }

    public function process()
    {
        return $this->sendEmail();
    }

    public function sendEmail()
    {

        switch (ENVIRONMENT) {
            case 'development':
                $transport = Swift_SmtpTransport::newInstance('smtp.gmail.com', 465, 'ssl');
                $transport->setUsername("donotreply@ant.com.mx");
                $transport->setPassword("d0n0tr3ply");
                break;
            case 'production':
                $transport = Swift_SmtpTransport::newInstance('10.218.108.241', 25, "");
                break;
            default:
                return false;
        }

        $mailer = Swift_Mailer::newInstance($transport);

        if ($this->type === 'order_new') {
            $message = $this->sendOrderNew($mailer);
        } else if ($this->type === 'alert') {
            $message = $this->sendAlert($mailer);
        } else if ($this->type === 'comment') {
            $message = $this->sendComment($mailer);
        } else if ($this->type === 'shopping') {
            $message = $this->sendCommentShopping($mailer);
        } else if ($this->type === 'authorization') {
            $message = $this->sendAuthorization($mailer);
        } else {
            $message = $this->sendRecover($mailer);
        }

        if (!empty($this->Cc)) {
            $message->setCc($this->Cc);
        }
        if (!empty($this->Bcc)) {
            $message->setBcc($this->Bcc);
        }

        $result = ['status' => false, 'message' => 'error'];

        $numSent = $mailer->send($message);

        if ($numSent) {
            $result = ['status' => true, 'message' => 'ok'];
        }

        return (object) $result;
    }

    public function placeHeaderFooter($path)
    {
        $xmlHeader = file_get_contents($this->header);
        $xmlFooter = file_get_contents($this->footer);
        $xml = file_get_contents($path);
        return $xmlHeader . $xml . $xmlFooter;
    }

    // Mail- Recuparar contraseña
    public function sendRecover()
    {
        $path = EMAILTEMPLATES . $this->template;
        $HTMLFile = $this->transformXML_sendRecover($this->placeHeaderFooter($path));
        $message = Swift_Message::newInstance($this->subject)
        ->setFrom($this->from)
        ->setTo($this->tousers)
        ->setBody($HTMLFile, 'text/html');

        return $message;
    }

    public function transformXML_sendRecover($xml)
    {
        $xml = str_replace("{##NAME##}", $this->misc['name'], $xml);
        $xml = str_replace("{##PASSWORD##}", $this->misc['password'], $xml);
        $xml = str_replace("{##LOGO##}", $this->misc['logo'], $xml);
        return $xml;
    }

    // Mail, eniar comentario
    public function sendComment()
    {
        $path = EMAILTEMPLATES . $this->template;
        $HTMLFile = $this->transformXML_sendComment($this->placeHeaderFooter($path));
        $message = Swift_Message::newInstance($this->subject)
        ->setFrom($this->from)
        ->setTo($this->tousers)
        ->setBody($HTMLFile, 'text/html');

        return $message;
    }

    public function sendCommentShopping()
    {
        $path = EMAILTEMPLATES . $this->template;
        $HTMLFile = $this->transformXML_sendShopping($this->placeHeaderFooter($path));
        $message = Swift_Message::newInstance($this->subject)
        ->setFrom($this->from)
        ->setTo($this->tousers)
        ->setBody($HTMLFile, 'text/html');
        $attachment = Swift_Attachment::fromPath($this->archivo);
        $message->attach($attachment);

        return $message;
    }

    public function transformXML_sendComment($xml)
    {
        $xml = str_replace("{##SOLICITUD##}", $this->misc['solicitud'], $xml);
        $xml = str_replace("{##AUTOR##}", $this->misc['autor'], $xml);
        $xml = str_replace("{##LOGO##}", $this->misc['logo'], $xml);
        return $xml;
    }

    public function transformXML_sendShopping($xml)
    {
        $xml = str_replace("{##SOLICITUD##}", $this->misc['solicitud'], $xml);
        $xml = str_replace("{##AUTOR##}", $this->misc['autor'], $xml);
        $xml = str_replace("{##LOGO##}", $this->misc['logo'], $xml);
        $xml = str_replace("{##PROVEEDOR##}", $this->misc['proveedor'], $xml);
        return $xml;
    }

    // Mail, autorizacion en solicitudes
    public function sendAuthorization()
    {
        $path = EMAILTEMPLATES . $this->template;
        $HTMLFile = $this->transformXML_sendAuthorization($this->placeHeaderFooter($path));
        $message = Swift_Message::newInstance($this->subject)
        ->setFrom($this->from)
        ->setTo($this->tousers)
        ->setBody($HTMLFile, 'text/html');

        return $message;
    }

    public function transformXML_sendAuthorization($xml)
    {
        $xml = str_replace("{##SOLICITUD##}", $this->misc['solicitud'], $xml);
        $xml = str_replace("{##LOGO##}", $this->misc['logo'], $xml);
        return $xml;
    }

    // Mail - Movimiento necesita autorización
    public function sendAlert()
    {
        $path = EMAILTEMPLATES . $this->template;
        $HTMLFile = $this->transformXML_sendAlert($this->placeHeaderFooter($path));
        $message = Swift_Message::newInstance($this->subject)
        ->setFrom($this->from)
        ->setTo($this->to)
        ->setBody($HTMLFile, 'text/html');

        return $message;
    }

    public function transformXML_sendAlert($xml)
    {
        $xml = str_replace("{##USUARIO##}", $this->misc['usuario'], $xml);
        $xml = str_replace("{##FECHA##}", $this->misc['fecha'], $xml);
        $xml = str_replace("{##TIPOMOVIMIENTO##}", $this->misc['tipomovimiento'], $xml);
        $xml = str_replace("{##EMPRESA##}", $this->misc['empresa'], $xml);
        $xml = str_replace("{##ALMACEN##}", $this->misc['almacen'], $xml);
        $xml = str_replace("{##FOLIO##}", $this->misc['folio'], $xml);
        $xml = str_replace("{##NT##}", $this->misc['nt'], $xml);
        $xml = str_replace("{##FECHAMOV##}", $this->misc['fechamov'], $xml);
        return $xml;
    }

    // Mail - Orden de producción fase - NUEVO
    public function sendOrderNew()
    {
        $path = EMAILTEMPLATES . $this->template;
        $HTMLFile = $this->transformXML_sendOrderNew($this->placeHeaderFooter($path));
        $message = Swift_Message::newInstance($this->subject)
        ->setFrom($this->from)
        ->setTo($this->to)
        ->setBody($HTMLFile, 'text/html');

        return $message;
    }

    public function transformXML_sendOrderNew($xml)
    {
        $xml = str_replace("{##FOLIO##}", $this->misc['folio'], $xml);
        $xml = str_replace("{##FECHA##}", $this->misc['fecha'], $xml);
        return $xml;
    }

}
