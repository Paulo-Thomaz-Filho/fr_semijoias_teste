<?php

namespace app\core\utils;

/**
 * Classe para gerar templates de email padronizados com CSS inline
 */
class EmailTemplate {
    
    /**
     * Template base HTML para emails
     */
    private static function getBaseTemplate($titulo, $conteudo) {
        return "
        <!DOCTYPE html>
        <html lang='pt-BR'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>{$titulo}</title>
            <link rel='preconnect' href='https://fonts.googleapis.com'>
            <link rel='preconnect' href='https://fonts.gstatic.com' crossorigin>
            <link href='https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap' rel='stylesheet'>
        </head>
        <body style='margin: 0; padding: 0; font-family: \"Poppins\", sans-serif; background-color: #f4f4f4;'>
            {$conteudo}
        </body>
        </html>
        ";
    }

    /**
     * Template de email de ativação de conta
     * 
     * @param string $nomeUsuario Nome do usuário
     * @param string $linkAtivacao Link completo para ativação
     * @param string $token Token de ativação (opcional, para exibir)
     * @return string HTML do email
     */
    public static function emailAtivacaoConta($nomeUsuario, $linkAtivacao, $token = null) {
        $conteudo = "
            <table role='presentation' style='width: 100%; border-collapse: collapse; background-color: #f4f4f4; padding: 40px 20px;'>
                <tr>
                    <td align='center'>
                        <table role='presentation' style='max-width: 600px; width: 100%; background-color: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);'>
                            <!-- Header -->
                            <tr>
                                <td style='background-color: #6c757d; padding: 40px 30px; text-align: center;'>
                                    <img src='http://frsemijoias.hostdeprojetosdoifsp.gru.br/public/assets/images/logo.svg' alt='FR Semijoias' style='height: 80px; display: block; margin: 0 auto;' />
                                </td>
                            </tr>
                            
                            <!-- Body -->
                            <tr>
                                <td style='padding: 40px 30px;'>
                                    <h2 style='margin: 0 0 20px 0; color: #333333; font-size: 24px; font-weight: 600;'>Bem-vindo(a), {$nomeUsuario}! 🎉</h2>
                                    
                                    <p style='margin: 0 0 16px 0; color: #555555; font-size: 16px; line-height: 1.6;'>
                                        Ficamos muito felizes com o seu cadastro na <strong>FR Semijoias</strong>!
                                    </p>
                                    
                                    <p style='margin: 0 0 24px 0; color: #555555; font-size: 16px; line-height: 1.6;'>
                                        Para começar a usar sua conta e aproveitar todas as vantagens, você precisa ativá-la clicando no botão abaixo:
                                    </p>
                                    
                                    <!-- Button -->
                                    <table role='presentation' style='width: 100%; margin: 32px 0;'>
                                        <tr>
                                            <td align='center'>
                                                <a href='{$linkAtivacao}' style='display: inline-block; background-color: #6c757d; color: #ffffff; text-decoration: none; padding: 16px 32px; border-radius: 8px; font-weight: 600; font-size: 16px; box-shadow: 0 4px 6px rgba(102, 126, 234, 0.1);'>
                                                    🔓 Ativar Minha Conta
                                                </a>
                                            </td>
                                        </tr>
                                    </table>";
        
        if ($token) {
            $conteudo .= "
                                    <!-- Divider -->
                                    <div style='border-top: 2px solid #e0e0e0; margin: 32px 0;'></div>
                                    
                                    <p style='margin: 0 0 16px 0; color: #333333; font-size: 16px; font-weight: 600;'>Ou use o código de ativação:</p>
                                    
                                    <!-- Token Box -->
                                    <table role='presentation' style='width: 100%; margin: 16px 0;'>
                                        <tr>
                                            <td align='center'>
                                                <div style='background-color: #f8f9fa; border: 2px dashed #667eea; border-radius: 8px; padding: 20px 40px; display: inline-block;'>
                                                    <span style='font-size: 28px; font-weight: 700; color: #667eea; letter-spacing: 4px; font-family: monospace;'>{$token}</span>
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                    
                                    <p style='margin: 16px 0 0 0; text-align: center; font-size: 14px; color: #666666;'>
                                        Cole este código na página de ativação
                                    </p>";
        }
        
        $conteudo .= "
                                    <!-- Divider -->
                                    <div style='border-top: 2px solid #e0e0e0; margin: 32px 0;'></div>
                                    
                                    <!-- Alert Box -->
                                    <table role='presentation' style='width: 100%; background-color: #fff3cd; border-left: 4px solid #ffc107; border-radius: 4px; margin: 24px 0;'>
                                        <tr>
                                            <td style='padding: 16px; color: #856404; font-size: 14px; line-height: 1.6;'>
                                                <strong>⏰ Importante:</strong> Este link é válido por tempo limitado. Ative sua conta o quanto antes!
                                            </td>
                                        </tr>
                                    </table>
                                    
                                    <p style='margin: 30px 0 0 0; font-size: 14px; color: #666666; line-height: 1.6;'>
                                        Se você não se cadastrou na FR Semijoias, por favor ignore este email.
                                    </p>
                                </td>
                            </tr>
                            
                            <!-- Footer -->
                            <tr>
                                <td style='background-color: #f8f9fa; padding: 30px; text-align: center; border-top: 1px solid #e0e0e0;'>
                                    <p style='margin: 0 0 10px 0; color: #666666; font-size: 14px;'>
                                        &copy; " . date('Y') . " FR Semijoias. Todos os direitos reservados.
                                    </p>
                                    <p style='margin: 0; color: #999999; font-size: 12px;'>
                                        Este é um email automático, por favor não responda.
                                    </p>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        ";
        
        return self::getBaseTemplate('Ative sua conta - FR Semijoias', $conteudo);
    }

    /**
     * Template de email de confirmação de conta ativada
     * 
     * @param string $nomeUsuario Nome do usuário
     * @param string $linkLogin Link para página de login
     * @return string HTML do email
     */
    public static function emailContaAtivada($nomeUsuario, $linkLogin) {
        $conteudo = "
            <table role='presentation' style='width: 100%; border-collapse: collapse; background-color: #f4f4f4; padding: 40px 20px;'>
                <tr>
                    <td align='center'>
                        <table role='presentation' style='max-width: 600px; width: 100%; background-color: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);'>
                            <!-- Header -->
                            <tr>
                                <td style='background-color: #6c757d; padding: 40px 30px; text-align: center;'>
                                    <img src='http://frsemijoias.hostdeprojetosdoifsp.gru.br/public/assets/images/logo.svg' alt='FR Semijoias' style='height: 80px; display: block; margin: 0 auto;' />
                                </td>
                            </tr>
                            
                            <!-- Body -->
                            <tr>
                                <td style='padding: 40px 30px;'>
                                    <h2 style='margin: 0 0 20px 0; color: #333333; font-size: 24px; font-weight: 600;'>Conta Ativada! ✅</h2>
                                    
                                    <p style='margin: 0 0 16px 0; color: #555555; font-size: 16px; line-height: 1.6;'>
                                        Olá, <strong>{$nomeUsuario}</strong>!
                                    </p>
                                    
                                    <p style='margin: 0 0 24px 0; color: #555555; font-size: 16px; line-height: 1.6;'>
                                        Sua conta foi ativada com sucesso! Agora você já pode fazer login e aproveitar todos os recursos da FR Semijoias.
                                    </p>
                                    
                                    <!-- Button -->
                                    <table role='presentation' style='width: 100%; margin: 32px 0;'>
                                        <tr>
                                            <td align='center'>
                                                <a href='{$linkLogin}' style='display: inline-block; background-color: #6c757d; color: #ffffff; text-decoration: none; padding: 16px 32px; border-radius: 8px; font-weight: 600; font-size: 16px;'>
                                                    🔐 Fazer Login
                                                </a>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                            
                            <!-- Footer -->
                            <tr>
                                <td style='background-color: #f8f9fa; padding: 30px; text-align: center; border-top: 1px solid #e0e0e0;'>
                                    <p style='margin: 0 0 10px 0; color: #666666; font-size: 14px;'>
                                        &copy; " . date('Y') . " FR Semijoias. Todos os direitos reservados.
                                    </p>
                                    <p style='margin: 0; color: #999999; font-size: 12px;'>
                                        Este é um email automático, por favor não responda.
                                    </p>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        ";
        
        return self::getBaseTemplate('Conta Ativada - FR Semijoias', $conteudo);
    }

    /**
     * Template de email de recuperação de senha
     * 
     * @param string $nomeUsuario Nome do usuário
     * @param string $linkRecuperacao Link para redefinir senha
     * @param string $token Token de recuperação (opcional)
     * @return string HTML do email
     */
    public static function emailRecuperacaoSenha($nomeUsuario, $linkRecuperacao, $token = null) {
        $conteudo = "
            <table role='presentation' style='width: 100%; border-collapse: collapse; background-color: #f4f4f4; padding: 40px 20px;'>
                <tr>
                    <td align='center'>
                        <table role='presentation' style='max-width: 600px; width: 100%; background-color: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);'>
                            <!-- Header -->
                            <tr>
                                <td style='background-color: #6c757d; padding: 40px 30px; text-align: center;'>
                                    <img src='http://frsemijoias.hostdeprojetosdoifsp.gru.br/public/assets/images/logo.svg' alt='FR Semijoias' style='height: 80px; display: block; margin: 0 auto;' />
                                </td>
                            </tr>
                            
                            <!-- Body -->
                            <tr>
                                <td style='padding: 40px 30px;'>
                                    <h2 style='margin: 0 0 20px 0; color: #333333; font-size: 24px; font-weight: 600;'>Recuperação de Senha 🔑</h2>
                                    
                                    <p style='margin: 0 0 16px 0; color: #555555; font-size: 16px; line-height: 1.6;'>
                                        Olá, <strong>{$nomeUsuario}</strong>!
                                    </p>
                                    
                                    <p style='margin: 0 0 24px 0; color: #555555; font-size: 16px; line-height: 1.6;'>
                                        Recebemos uma solicitação para redefinir a senha da sua conta. Clique no botão abaixo para criar uma nova senha:
                                    </p>
                                    
                                    <!-- Button -->
                                    <table role='presentation' style='width: 100%; margin: 32px 0;'>
                                        <tr>
                                            <td align='center'>
                                                <a href='{$linkRecuperacao}' style='display: inline-block; background-color: #6c757d; color: #ffffff; text-decoration: none; padding: 16px 32px; border-radius: 8px; font-weight: 600; font-size: 16px;'>
                                                    🔓 Redefinir Senha
                                                </a>
                                            </td>
                                        </tr>
                                    </table>";
        
        if ($token) {
            $conteudo .= "
                                    <div style='border-top: 2px solid #e0e0e0; margin: 32px 0;'></div>
                                    <p style='margin: 0 0 16px 0; color: #333333; font-size: 16px; font-weight: 600;'>Ou use o código:</p>
                                    <table role='presentation' style='width: 100%; margin: 16px 0;'>
                                        <tr>
                                            <td align='center'>
                                                <div style='background-color: #f8f9fa; border: 2px dashed #667eea; border-radius: 8px; padding: 20px 40px; display: inline-block;'>
                                                    <span style='font-size: 28px; font-weight: 700; color: #667eea; letter-spacing: 4px; font-family: monospace;'>{$token}</span>
                                                </div>
                                            </td>
                                        </tr>
                                    </table>";
        }
        
        $conteudo .= "
                                    <div style='border-top: 2px solid #e0e0e0; margin: 32px 0;'></div>
                                    <table role='presentation' style='width: 100%; background-color: #f8d7da; border-left: 4px solid #dc3545; border-radius: 4px; margin: 24px 0;'>
                                        <tr>
                                            <td style='padding: 16px; color: #721c24; font-size: 14px; line-height: 1.6;'>
                                                <strong>⚠️ Atenção:</strong> Se você não solicitou esta recuperação, ignore este email e sua senha permanecerá a mesma.
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                            
                            <!-- Footer -->
                            <tr>
                                <td style='background-color: #f8f9fa; padding: 30px; text-align: center; border-top: 1px solid #e0e0e0;'>
                                    <p style='margin: 0 0 10px 0; color: #666666; font-size: 14px;'>
                                        &copy; " . date('Y') . " FR Semijoias. Todos os direitos reservados.
                                    </p>
                                    <p style='margin: 0; color: #999999; font-size: 12px;'>
                                        Este é um email automático, por favor não responda.
                                    </p>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        ";
        
        return self::getBaseTemplate('Recuperação de Senha - FR Semijoias', $conteudo);
    }

    /**
     * Template de email de pedido realizado
     * 
     * @param string $nomeUsuario Nome do usuário
     * @param string $numeroPedido Número do pedido
     * @param string $linkPedido Link para ver detalhes do pedido
     * @return string HTML do email
     */
    public static function emailPedidoRealizado($nomeUsuario, $numeroPedido, $linkPedido) {
        $conteudo = "
            <table role='presentation' style='width: 100%; border-collapse: collapse; background-color: #f4f4f4; padding: 40px 20px;'>
                <tr>
                    <td align='center'>
                        <table role='presentation' style='max-width: 600px; width: 100%; background-color: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);'>
                            <!-- Header -->
                            <tr>
                                <td style='background-color: #6c757d; padding: 40px 30px; text-align: center;'>
                                    <img src='http://frsemijoias.hostdeprojetosdoifsp.gru.br/public/assets/images/logo.svg' alt='FR Semijoias' style='height: 80px; display: block; margin: 0 auto;' />
                                </td>
                            </tr>
                            
                            <!-- Body -->
                            <tr>
                                <td style='padding: 40px 30px;'>
                                    <h2 style='margin: 0 0 20px 0; color: #333333; font-size: 24px; font-weight: 600;'>Pedido Confirmado! 🎉</h2>
                                    
                                    <p style='margin: 0 0 16px 0; color: #555555; font-size: 16px; line-height: 1.6;'>
                                        Olá, <strong>{$nomeUsuario}</strong>!
                                    </p>
                                    
                                    <p style='margin: 0 0 24px 0; color: #555555; font-size: 16px; line-height: 1.6;'>
                                        Seu pedido <strong>#{$numeroPedido}</strong> foi realizado com sucesso e já está sendo processado.
                                    </p>
                                    
                                    <!-- Button -->
                                    <table role='presentation' style='width: 100%; margin: 32px 0;'>
                                        <tr>
                                            <td align='center'>
                                                <a href='{$linkPedido}' style='display: inline-block; background-color: #6c757d; color: #ffffff; text-decoration: none; padding: 16px 32px; border-radius: 8px; font-weight: 600; font-size: 16px;'>
                                                    📦 Ver Detalhes do Pedido
                                                </a>
                                            </td>
                                        </tr>
                                    </table>
                                    
                                    <div style='border-top: 2px solid #e0e0e0; margin: 32px 0;'></div>
                                    
                                    <table role='presentation' style='width: 100%; background-color: #d1ecf1; border-left: 4px solid #17a2b8; border-radius: 4px; margin: 24px 0;'>
                                        <tr>
                                            <td style='padding: 16px; color: #0c5460; font-size: 14px; line-height: 1.6;'>
                                                <strong>📧 Acompanhamento:</strong> Você receberá atualizações sobre o status do seu pedido por email.
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                            
                            <!-- Footer -->
                            <tr>
                                <td style='background-color: #f8f9fa; padding: 30px; text-align: center; border-top: 1px solid #e0e0e0;'>
                                    <p style='margin: 0 0 10px 0; color: #666666; font-size: 14px;'>
                                        &copy; " . date('Y') . " FR Semijoias. Todos os direitos reservados.
                                    </p>
                                    <p style='margin: 0; color: #999999; font-size: 12px;'>
                                        Este é um email automático, por favor não responda.
                                    </p>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        ";
        
        return self::getBaseTemplate('Pedido Confirmado - FR Semijoias', $conteudo);
    }
}
