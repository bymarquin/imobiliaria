<?php
require_once __DIR__ . '/../dao/CorretorDAO.php';

/**
 * Intermediário entre o formulário de corretores e o banco de dados.
 *
 * Recebe os dados do formulário, monta um objeto Corretor limpo
 * e passa pro DAO salvar. Segue o mesmo papel dos outros controllers.
 */
class CorretorController
{
    private CorretorDAO $dao;

    public function __construct()
    {
        $this->dao = new CorretorDAO();
    }

    /**
     * Retorna todos os corretores pra listagem.
     */
    public function listar(): array
    {
        return $this->dao->listar();
    }

    /**
     * Busca um corretor pelo ID pra pré-preencher o formulário de edição.
     */
    public function buscarPorId(int $id): ?Corretor
    {
        return $this->dao->buscarPorId($id);
    }

    /**
     * Recebe os dados do formulário, monta o objeto Corretor e salva.
     */
    public function salvar(array $data): void
    {
        $corretor = new Corretor();
        if (!empty($data['id'])) $corretor->setId((int) $data['id']);
        $corretor->setNome(trim($data['nome'] ?? ''));
        $corretor->setCreci(trim($data['creci'] ?? ''));
        $corretor->setTelefone(trim($data['telefone'] ?? ''));
        $corretor->setEmail(trim($data['email'] ?? ''));
        $this->dao->salvar($corretor);
    }

    /**
     * Remove o corretor com o ID informado.
     */
    public function excluir(int $id): void
    {
        $this->dao->excluir($id);
    }
}
