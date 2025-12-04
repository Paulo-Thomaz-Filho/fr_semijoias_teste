<?php

namespace app\core\utils;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

/**
 * Classe para envio de e-mails usando PHPMailer.
 * Suporta HTML, múltiplos anexos (de arquivos ou Base64) e SMTP.
 */
class Mail {
    
    private $mailer;
    private $attachments = [];
    
    /**
     * Construtor da classe Mail
     * @param string $to Destinatário do e-mail
     * @param string $subject Assunto do e-mail
     * @param string $body Corpo do e-mail (HTML)
     */
    public function __construct($to, $subject, $body) {
        $this->mailer = new PHPMailer(true);
        
        try {
            // Configurações do servidor SMTP
            $this->mailer->isSMTP();
            $this->mailer->Host       = $_ENV['MAIL_HOST'];
            $this->mailer->SMTPAuth   = true;
            $this->mailer->Username   = $_ENV['MAIL_USERNAME'];
            $this->mailer->Password   = $_ENV['MAIL_PASSWORD'];
            $this->mailer->SMTPSecure = $_ENV['MAIL_ENCRYPTION'];
            $this->mailer->Port       = $_ENV['MAIL_PORT'];
            $this->mailer->CharSet    = 'UTF-8';
            $fromEmail = $_ENV['MAIL_FROM_EMAIL'] ?? $_ENV['MAIL_USERNAME'];
            $fromName  = $_ENV['MAIL_FROM_NAME'] ?? 'FR Semijoias';
            $this->mailer->setFrom($fromEmail, $fromName);
            
            // Configurações do destinatário
            $this->mailer->addAddress($to);
            
            // Configurações do conteúdo
            $this->mailer->isHTML(true);
            $this->mailer->Subject = $subject;
            $this->mailer->Body    = $body;
            $this->mailer->AltBody = strip_tags($body); // Versão texto puro
            
        } catch (Exception $e) {
            error_log("Erro ao configurar PHPMailer: {$e->getMessage()}");
        }
    }
    
    /**
     * Adiciona um cabeçalho customizado ao e-mail
     * @param string $header Cabeçalho no formato "Nome: Valor"
     */
    public function addHeader($header) {
        if (strpos($header, ':') !== false) {
            list($name, $value) = explode(':', $header, 2);
            $this->mailer->addCustomHeader(trim($name), trim($value));
        }
    }
    
    /**
     * Adiciona um anexo de arquivo ao e-mail
     * @param string $file Caminho completo do arquivo
     */
    public function addAttachment($file) {
        if (file_exists($file)) {
            $this->attachments[] = [
                'type' => 'file',
                'path' => $file,
                'filename' => basename($file)
            ];
        }
    }

    /**
     * Adiciona um anexo Base64 ao e-mail
     * @param string $filename Nome do arquivo
     * @param string $data Dados em Base64
     * @param string $type Tipo MIME do arquivo
     */
    public function addAttachmentBase64($filename, $data, $type = 'application/octet-stream') {
        $this->attachments[] = [
            'type' => 'base64',
            'content' => $data,
            'filename' => $filename,
            'mime_type' => $type
        ];
    }
    
    /**
     * Envia o e-mail
     * @return bool True se enviado com sucesso, False caso contrário
     */
    public function send() {
        try {
            // Adiciona os anexos
            foreach ($this->attachments as $attachment) {
                if ($attachment['type'] == 'file') {
                    // Anexo de arquivo
                    $this->mailer->addAttachment(
                        $attachment['path'], 
                        $attachment['filename']
                    );
                } else {
                    // Anexo Base64
                    $content = base64_decode($attachment['content']);
                    $this->mailer->addStringAttachment(
                        $content,
                        $attachment['filename'],
                        'base64',
                        $attachment['mime_type']
                    );
                }
            }
            
            // Envia o e-mail
            return $this->mailer->send();
            
        } catch (Exception $e) {
            error_log("Erro ao enviar e-mail: {$this->mailer->ErrorInfo}");
            return false;
        }
    }
    
    /**
     * Retorna a última mensagem de erro
     * @return string Mensagem de erro
     */
    public function getError() {
        return $this->mailer->ErrorInfo;
    }
}
?>