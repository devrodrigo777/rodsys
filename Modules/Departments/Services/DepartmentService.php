<?php

namespace Modules\Departments\Services;

use Modules\Login\Models\CargosModel;
use Modules\Permissoes\Models\PermissoesModel;

/**
 * DepartmentService
 * 
 * Serviço de lógica de negócio para gerenciamento de departamentos (cargos).
 * Implementa padrão MSC (Model-Service-Controller) com:
 * - Métodos de renderização para create/edit e listagem
 * - Criação com associação de permissões (batch insert)
 * - Atualização com substituição de permissões
 * - Exclusão cascata (reatribui pessoas para cargo 'nenhum')
 * - Validações de segurança (só edita departamentos próprios, bloqueia globais)
 * - Transações para garantir consistência de dados
 * 
 * @package Modules\Departments\Services
 */
class DepartmentService
{
    private $renderFunction;
    private $params;

    public function __construct($renderFunction = 'renderManageDepartments', $params = null)
    {
        $this->renderFunction = $renderFunction;
        $this->params = $params;
    }

    public function render($data = [])
    {
        $func = $this->renderFunction;

        return $this->$func($data);
    }

    protected function renderCreateEditDepartment($data = [])
    {
        $data['permissoes'] = (new PermissoesModel())->getAllPermissions();

        // Defaults
        $data['is_editing'] = false;
        $data['selectedPermissions'] = [];

        if ($this->params) {
            $departmentModel = new CargosModel();
            $data['departamento'] = $departmentModel->find($this->params);
            $data['is_editing'] = true;

            // Load permissions associated with this cargo (id_permissao and slug)
            $db = \Config\Database::connect();
            $builder = $db->table('cargos_permissoes')
                ->select('cargos_permissoes.id_permissao, permissoes.slug')
                ->join('permissoes', 'permissoes.id_permissao = cargos_permissoes.id_permissao')
                ->where('cargos_permissoes.id_cargo', $this->params);

            $assigned = $builder->get()->getResultArray();

            $selected = [];
            foreach ($assigned as $row) {
                if (isset($row['id_permissao'])) {
                    $selected[] = (int) $row['id_permissao'];
                }
                if (isset($row['slug'])) {
                    $selected[] = $row['slug'];
                }
            }

            $data['selectedPermissions'] = $selected;
        }

        return module_view('Departments', 'CRUD/CreateEdit', $data);
    }

    protected function renderManageDepartments($data = [])
    {
        $data['lista_cargos'] = (new CargosModel())->listForMe();

        return module_view('Departments', 'CRUD/Read', $data);
    }

    public function deleteDepartment($id_cargo)
    {
        $cargoModel = new CargosModel();
        $db = \Config\Database::connect();

        try {
            // Check if cargo exists
            $cargo = $cargoModel->find($id_cargo);
            if (!$cargo) {
                return [
                    'success' => false,
                    'message' => 'Departamento não encontrado.'
                ];
            }

            // Find the 'nenhum' cargo id
            $nenhumCargo = $db->table('cargos')
                ->select('id_cargo')
                ->where('slug', 'nenhum')
                ->get()
                ->getRow();

            if (!$nenhumCargo) {
                return [
                    'success' => false,
                    'message' => 'Cargo genérico "nenhum" não encontrado.'
                ];
            }

            $id_cargo_nenhum = $nenhumCargo->id_cargo;

            // Reassign all people with this cargo to the 'nenhum' cargo
            $db->table('pessoas')
                ->where('id_cargo', $id_cargo)
                ->update(['id_cargo' => $id_cargo_nenhum]);

            // Delete associated permissions
            $db->table('cargos_permissoes')->where('id_cargo', $id_cargo)->delete();

            // Delete the cargo
            $cargoModel->delete($id_cargo);

            return [
                'success' => true,
                'message' => 'Departamento removido com sucesso.'
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Erro ao remover departamento: ' . $e->getMessage()
            ];
        }
    }

    public function updateWithPermissions($id_cargo, $nome, $descricao, $permissoes = [])
    {
        $db = \Config\Database::connect();
        $db->transStart();
        try {
            // Security check: prevent editing global or readonly departments
            $cargoModel = new CargosModel();
            $department = $cargoModel->find($id_cargo);
            
            if (!$department) {
                return [
                    'success' => false,
                    'message' => 'Departamento não encontrado.'
                ];
            }

            // Check if department is global or readonly
            if ($department['readonly'] == 1) {
                return [
                    'success' => false,
                    'message' => 'Não é permitido editar departamentos globais ou protegidos.'
                ];
            }

            // Verify department belongs to logged-in user's company (unless superadmin)
            $permissionsModel = new PermissoesModel();
            if (!$permissionsModel->user_is_superadmin()) {
                $id_empresa = session()->get('id_empresa');
                if ($department['id_empresa'] != $id_empresa) {
                    return [
                        'success' => false,
                        'message' => 'Você não tem permissão para editar este departamento.'
                    ];
                }
            }

            // Update cargo
            $cargoData = [
                'nome' => $nome,
                'descricao' => $descricao,
            ];

            $cargoModel->update($id_cargo, $cargoData);

            // Clear existing permissions
            $db->table('cargos_permissoes')->where('id_cargo', $id_cargo)->delete();

            // Associate new permissions in cargos_permissoes table using a batch insert
            if (!empty($permissoes) && is_array($permissoes)) {
                $permissoesTable = $db->table('cargos_permissoes');
                $batch = [];
                foreach ($permissoes as $id_permissao) {
                    $batch[] = [
                        'id_cargo' => $id_cargo,
                        'id_permissao' => (int) $id_permissao,
                    ];
                }
                if (!empty($batch)) {
                    $permissoesTable->insertBatch($batch);
                }
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \Exception('Erro ao atualizar departamento e associar permissões.');
            }

            return [
                'success' => true,
                'message' => 'Departamento atualizado com sucesso.'
            ];

        } catch (\Exception $e) {
            $db->transRollback();
            return [
                'success' => false,
                'message' => 'Erro: ' . $e->getMessage()
            ];
        }
    }

    public function createWithPermissions($nome, $descricao, $permissoes = [])
    {
        /**
         * Cria um novo departamento com permissões associadas.
         * 
         * FLUXO:
         * 1. Inicia transação
         * 2. Insere cargo na tabela 'cargos'
         * 3. Batch insert de permissões na tabela 'cargos_permissoes'
         * 4. Commit se sucesso, rollback se erro
         * 
         * DADOS ARMAZENADOS:
         * - cargos: id_cargo (auto), nome, descricao, id_empresa (sessão), is_global=0
         * - cargos_permissoes: [id_cargo, id_permissao][]
         * 
         * @param string $nome Nome do departamento (ex: "Recursos Humanos")
         * @param string $descricao Descrição do cargo (ex: "Responsável por RH")
         * @param array $permissoes Array de id_permissao associados
         * @return array ['success' => bool, 'id_cargo' => int, 'message' => string]
         */
        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Insert new cargo
            $cargoData = [
                'nome' => $nome,
                'descricao' => $descricao,
                'id_empresa' => session()->get('id_empresa'),
                'is_global' => 0,
            ];


            $cargoModel = new CargosModel();
            $id_cargo = $cargoModel->insert($cargoData);

            // Associate permissions in cargos_permissoes table using a batch insert
            if (!empty($permissoes) && is_array($permissoes)) {
                $permissoesTable = $db->table('cargos_permissoes');
                $batch = [];
                foreach ($permissoes as $id_permissao) {
                    $batch[] = [
                        'id_cargo' => $id_cargo,
                        'id_permissao' => (int) $id_permissao,
                    ];
                }
                if (!empty($batch)) {
                    $permissoesTable->insertBatch($batch);
                }
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \Exception('Erro ao criar departamento e associar permissões.');
            }

            return [
                'success' => true,
                'id_cargo' => $id_cargo,
                'message' => 'Departamento criado com sucesso.'
            ];

        } catch (\Exception $e) {
            $db->transRollback();
            return [
                'success' => false,
                'message' => 'Erro: ' . $e->getMessage()
            ];
        }
    }
}