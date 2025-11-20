<?php

namespace Modules\Login\Services;

use Modules\Login\Models\LoginModel;
use Modules\Login\Libraries\Passlib;

class AuthService
{
    protected $loginModel;
    protected $passlib;

    public function __construct()
    {
        $this->loginModel = new LoginModel();
        $this->passlib = new Passlib();
    }

    /**
     * Autentica um usuário com base nas credenciais fornecidas.
     *
     * @param int    $idEmpresa O ID da empresa.
     * @param string $usuario   O nome de usuário.
     * @param string $senha     A senha em texto simples.
     * @return array|false Retorna os dados do usuário se a autenticação for bem-sucedida, caso contrário, false.
     */
    public function authenticate(int $idEmpresa, string $usuario, string $senha)
    {
        $user = $this->loginModel->where('id_empresa', $idEmpresa)
                               ->where('usuario', $usuario)
                               ->where('ativo', 1)
                               ->first();

        if (! $user || ! $this->passlib->verifyPassword($senha, $user['senha_hash'])) {
            return false;
        }

        return $user;
    }

    public function validate_session()
    {
        $session = session();
        $id_usuario = $session->get('usuario');
        $id_empresa = $session->get('id_empresa');

        if (!$id_usuario || !$id_empresa) {
            return false;
        }

        $user = $this->loginModel->where('id_usuario', $id_usuario)->first();

        if ($user === null) {
            return false;
        }

        $empresaModel = model('Modules\Login\Models\EmpresaModel');
        $empresa = $empresaModel->where('id_empresa', $id_empresa)->first();

        return $empresa !== null && $empresa['plano_ativo'] == 1;
    }

    public function is_logged_in()
    {
        $session = session();
        return $session->get('logged_in') === true;
    }

    public function logout()
    {
        $session = session();
        $session->remove([
            'id_empresa',
            'usuario',
            'logged_in',
        ]);
    }

    public function setSession(array $user)
    {
        $session = session();

        $session->set([
            'id_empresa' => $user['id_empresa'],
            'usuario'    => $user['id_usuario'],
            'logged_in'  => true,
        ]);
    }
}
