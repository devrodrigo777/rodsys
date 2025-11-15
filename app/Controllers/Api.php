<?php

namespace App\Controllers;

use App\Libraries\Passlib;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\RESTful\ResourceController;
use Modules\Login\Models\LoginModel;

class Api extends ResourceController
{
    use ResponseTrait;

    /**
     * Cria uma nova empresa (da tabela empresas).
     *
     * @return ResponseInterface
     */
    public function createCompany(): ResponseInterface
    {
        $rules = [
            'cnpj'        => 'required|exact_length[14]|is_unique[empresas.cnpj]',
            'razao_social' => 'required|min_length[3]|max_length[255]',
            'data_adesao'  => 'required|valid_date', // Formato AAAA-MM-DD
        ];

        if (! $this->validate($rules)) {
            return $this->fail($this->validator->getErrors(), ResponseInterface::HTTP_BAD_REQUEST);
        }

        $data = [
            'cnpj'        => $this->request->getVar('cnpj'),
            'razao_social' => $this->request->getVar('razao_social'),
            'data_adesao'  => $this->request->getVar('data_adesao'),
        ];

        $model = new \Modules\Empresa\Models\EmpresaModel(); // Assumindo que você tem um modelo para Empresas
        $model->insert($data);

        return $this->respondCreated(['message' => 'Company created successfully']);
    }


    /**
     * Cria um novo usuário (da tabela login).
     *
     * @return ResponseInterface
     */
    public function createUser(): ResponseInterface
    {
        $rules = [
            'id_empresa' => 'required|integer',
            'usuario'   => 'required|min_length[5]|max_length[50]|is_unique[login.usuario,id_empresa,{id_empresa}]',
            'senha_hash'   => 'required|min_length[8]',
            'ativo' => 'required|in_list[0,1]'
        ];

        if (! $this->validate($rules)) {
            return $this->fail($this->validator->getErrors(), ResponseInterface::HTTP_BAD_REQUEST);
        }

        $passlib = new Passlib();

        $data = [
            'id_empresa' => $this->request->getVar('id_empresa'),
            'usuario'   => $this->request->getVar('usuario'),
            'senha_hash'   => $passlib->hashPassword($this->request->getVar('senha_hash')),
            'ativo'      => $this->request->getVar('ativo'),
        ];

        $model = new LoginModel();
        $model->insert($data);

        return $this->respondCreated(['message' => 'User created successfully']);
    }

    /**
     * Realiza o login do usuário.
     *
     * @return ResponseInterface
     */
    public function login(): ResponseInterface
    {
        $rules = [
            'id_empresa' => 'required|integer',
            'usuario'   => 'required|min_length[5]|max_length[50]',
            'senha'   => 'required|min_length[8]',
        ];

        if (! $this->validate($rules)) {
            return $this->fail($this->validator->getErrors(), ResponseInterface::HTTP_BAD_REQUEST);
        }
        // Lógica de autenticação aqui...
        return $this->respond(['message' => 'Login successful']);
    }
}