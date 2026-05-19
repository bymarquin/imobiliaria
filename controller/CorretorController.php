<?php
require_once __DIR__ . '/../dao/CorretorDAO.php';

class CorretorController
{
    private CorretorDAO $dao;

    public function __construct()
    {
        $this->dao = new CorretorDAO();
    }

    public function listar(): array
    {
        return $this->dao->listar();
    }

    public function buscarPorId(int $id): ?Corretor
    {
        return $this->dao->buscarPorId($id);
    }

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

    public function excluir(int $id): void
    {
        $this->dao->excluir($id);
    }
}
