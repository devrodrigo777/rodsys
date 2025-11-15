<?php

/**
 * Aplicaremos a lógica de verificação de menu ativo aqui.
 * Essa lógica irá verificar se contém palavras especiais como por exemplo dashboard/*, ou faturamento/novo, etc.
 * Definindo se o menu está ativo ou não na variável.
 */

namespace Modules\Dashboard\Libraries;

class Menuactive
{
    /**
     * Verifica se a URI atual corresponde ao padrão de ativação do menu.
     * * @param string $current_uri A URI atual da página (e.g., 'dashboard/principal/1').
     * @param string $active_pattern O padrão de ativação do menu (e.g., 'dashboard', 'faturamento/*').
     * @return bool
     */
    public static function is_active_menu(string $current_uri, string $active_pattern): bool
    {
        // Remove barras iniciais e finais de ambas as strings para garantir a comparação
        $current_uri = trim($current_uri, '/');
        $active_pattern = trim($active_pattern, '/');

        // 1. Verificação com Curinga (Ex: 'dashboard/*')
        if (str_ends_with($active_pattern, '/*')) {
            // Remove o "/*" do padrão para obter o prefixo
            $prefix = rtrim($active_pattern, '/*');
            
            // Verifica se a URI atual começa com esse prefixo
            // Se a URI for 'dashboard/principal/1' e o prefixo for 'dashboard', retorna TRUE.
            return str_starts_with($current_uri, $prefix);
        }
        
        // 2. Verificação de Correspondência Exata (Ex: 'dashboard')
        // Se não houver curinga, a correspondência deve ser exata.
        // Se a URI for 'dashboard/principal/1' e o padrão for 'dashboard', retorna FALSE.
        // Se a URI for 'dashboard' e o padrão for 'dashboard', retorna TRUE.
        return $current_uri === $active_pattern;
    }
}
