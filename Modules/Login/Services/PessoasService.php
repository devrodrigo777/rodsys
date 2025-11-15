<?php

namespace Modules\Login\Services;

use Modules\Login\Models\PessoasModel;

class PessoasService
{
    private $pessoasModel;

    public function __construct()
    {
        $this->pessoasModel = new PessoasModel();
    }

    public function getPessoaById($id)
    {
        return $this->pessoasModel->find($id);
    }

    public function getPessoaLogada()
    {
        $session = session();

        
        $idUsuarioLogin = $session->get('usuario');
        $idEmpresaLogin = $session->get('id_empresa');
                

        if ($idUsuarioLogin && $idEmpresaLogin) {
            return $this->pessoasModel
                ->select('pessoas.id_empresa, pessoas.nome_completo, pessoas.id_cargo, cargos.nome as cargo, empresas.razao_social,')
                ->join('empresas', 'empresas.id_empresa = pessoas.id_empresa')
                ->join('cargos', 'cargos.id_cargo = pessoas.id_cargo')
                ->where('pessoas.id_empresa', $idEmpresaLogin)->where('id_usuario_login', $idUsuarioLogin)->first();
        }

        return null;
    }
}
