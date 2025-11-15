<?php

namespace Modules\Login\Libraries;

class Passlib
{
    /**
     * Gera um hash de uma senha usando um algoritmo de hash forte e unidirecional.
     *
     * @param string $password A senha em texto simples para gerar o hash.
     * @return string A senha com hash.
     */
    public function hashPassword(string $password): string
    {
        // Usa a função password_hash embutida do PHP com um algoritmo forte (por exemplo, PASSWORD_BCRYPT)
        // Esta função lida automaticamente com a adição de salt.
        return password_hash($password, PASSWORD_BCRYPT);
    }

    /**
     * Verifica se uma senha em texto simples corresponde a uma senha com hash.
     *
     * @param string $password A senha em texto simples para verificar.
     * @param string $hashedPassword A senha com hash para comparar.
     * @return bool Verdadeiro se a senha corresponder, falso caso contrário.
     */
    public function verifyPassword(string $password, string $hashedPassword): bool
    {
        // Usa a função password_verify embutida do PHP
        return password_verify($password, $hashedPassword);
    }
}
