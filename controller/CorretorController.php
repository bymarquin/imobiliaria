<?php
require_once __DIR__ . '/../dao/CorretorDAO.php';

// Recebe os dados do formulário, organiza e repassa pro DAO salvar ou buscar.
class CorretorController
{
    private CorretorDAO $dao;

    public function __construct()
    {
        $this->dao = new CorretorDAO();
    }

    // Retorna todos os corretores
    public function listar(): array
    {
        return $this->dao->listar();
    }

    // Busca um corretor pelo ID pra preencher o formulário de edição
    public function buscarPorId(int $id): ?Corretor
    {
        return $this->dao->buscarPorId($id);
    }

    // Recebe os dados do formulário e salva o corretor
    public function salvar(array $data): void
    {
        $corretor = new Corretor();
        if (!empty($data['id'])) $corretor->setId((int) $data['id']); // se tem ID é edição
        $corretor->setNome(trim($data['nome'] ?? ''));
        $corretor->setCreci(trim($data['creci'] ?? ''));
        $corretor->setTelefone(trim($data['telefone'] ?? ''));
        $corretor->setEmail(trim($data['email'] ?? ''));
        $this->dao->salvar($corretor);
    }

    // Remove o corretor pelo ID
    public function excluir(int $id): void
    {
        $this->dao->excluir($id);
    }
}
