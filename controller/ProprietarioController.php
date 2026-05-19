<?php
require_once __DIR__ . '/../dao/ProprietarioDAO.php';

class ProprietarioController
{
    private ProprietarioDAO $dao;

    public function __construct()
    {
        $this->dao = new ProprietarioDAO();
    }

    public function listar(): array
    {
        return $this->dao->listar();
    }

    public function buscarPorId(int $id): ?Proprietario
    {
        return $this->dao->buscarPorId($id);
    }

    public function salvar(array $data): void
    {
        $proprietario = new Proprietario();
        if (!empty($data['id'])) $proprietario->setId((int) $data['id']);
        $proprietario->setNome(trim($data['nome'] ?? ''));
        $proprietario->setCpf(trim($data['cpf'] ?? ''));
        $proprietario->setTelefone(trim($data['telefone'] ?? ''));
        $proprietario->setEmail(trim($data['email'] ?? ''));
        $this->dao->salvar($proprietario);
    }

    public function excluir(int $id): void
    {
        $this->dao->excluir($id);
    }
}
