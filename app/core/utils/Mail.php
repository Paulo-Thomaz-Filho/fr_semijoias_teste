<?php
namespace app\core\utils;

/**
 * Classe para envio de e-mails usando a função mail() nativa do PHP.
 * Suporta HTML e múltiplos anexos (de arquivos ou Base64).
 */
class Mail {
    
    private $to = "";
    private $subject = "";
    private $body = "";
    private $headers = [];
    private $attachments = [];
    
    public function __construct($to, $subject, $body) {
        $this->to = $to;
        $this->subject = $subject;
        $this->body = $body;
        
        // Configuração dos cabeçalhos padrão
        $this->headers[] = "MIME-Version: 1.0";
        // O Content-Type será definido no 'send()'
    }
     
    public function addHeader($header) {
        $this->headers[] = $header;
    }
    
    public function addAttachment($file) {
        if (file_exists($file)) {
            $filename = basename($file);
            $this->attachments[] = [
                'type' => 'file',
                'path' => $file,
                'filename' => $filename,
                'mime_type' => mime_content_type($file)
            ];
        }
    }

    public function addAttachmentBase64($filename, $data, $type = 'application/octet-stream') {
        $this->attachments[] = [
            'type' => 'base64',
            'content' => $data, // Armazena os dados base64
            'filename' => $filename,
            'mime_type' => $type
        ];
    }
        
    public function send() {
        $to = $this->to;
        $subject = $this->subject;
        $body = $this->body;
        
        // Gera um limite (boundary) único para separar as partes do e-mail
        $boundary = "----=_Separador_Parte_" . md5(time());
        
        // Se não houver anexos, envia um e-mail HTML simples
        if (count($this->attachments) == 0) {
            $this->headers[] = "Content-type: text/html; charset=UTF-8";
            $headers = implode("\r\n", $this->headers);
            return mail($to, $subject, $body, $headers);
        }

        // --- Se houver anexos, monta um e-mail complexo (multipart/mixed) ---
        
        $this->headers[] = "Content-Type: multipart/mixed; boundary=\"" . $boundary . "\"";
        $headers = implode("\r\n", $this->headers);

        // 1. Inicia o corpo da mensagem com a parte HTML
        $message_body = "--" . $boundary . "\r\n";
        $message_body .= "Content-Type: text/html; charset=\"utf-8\"\r\n";
        $message_body .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
        $message_body .= $body . "\r\n\r\n";

        // 2. Adiciona cada anexo
        foreach ($this->attachments as $attachment) {
            $filename = $attachment['filename'];
            $mime_type = $attachment['mime_type'];
            
            // Pega o conteúdo (seja do arquivo ou da string base64)
            if ($attachment['type'] == 'file') {
                $content = file_get_contents($attachment['path']);
            } else {
                $content = base64_decode($attachment['content']);
            }
            
            // Codifica o conteúdo em base64 para o e-mail
            $content_base64 = chunk_split(base64_encode($content));
            
            // Adiciona a parte do anexo
            $message_body .= "--" . $boundary . "\r\n";
            $message_body .= "Content-Type: " . $mime_type . "; name=\"" . $filename . "\"\r\n";
            $message_body .= "Content-Transfer-Encoding: base64\r\n";
            $message_body .= "Content-Disposition: attachment; filename=\"" . $filename . "\"\r\n\r\n";
            $message_body .= $content_base64 . "\r\n\r\n";
        }
        
        // 3. Fecha o corpo do e-mail
        $message_body .= "--" . $boundary . "--";
        
        // Envia o e-mail completo
        return mail($to, $subject, $message_body, $headers);
    }
}
?>